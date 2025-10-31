<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendHourDataRepository;

class MessageSendHourDataProcessor
{
    public function __construct(
        private readonly MessageSendHourDataRepository $messageSendHourDataRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 处理API响应数据
     *
     * @param Account $account 账户信息
     * @param array<mixed> $response API响应数据
     */
    public function processResponse(Account $account, array $response): void
    {
        if (!$this->validateResponse($response)) {
            $this->logger->error('获取消息发送分时数据发生错误', [
                'account' => $account,
                'response' => $response,
            ]);

            return;
        }

        $list = $response['list'];
        assert(is_array($list));
        $this->processDataList($account, $list);
    }

    /**
     * 验证响应格式
     *
     * @param array<mixed> $response API响应数据
     * @return bool
     */
    private function validateResponse(array $response): bool
    {
        return isset($response['list']) && is_array($response['list']);
    }

    /**
     * 处理数据列表
     *
     * @param Account $account 账户信息
     * @param array<mixed> $list 数据列表
     */
    private function processDataList(Account $account, array $list): void
    {
        foreach ($list as $item) {
            $this->processDataItem($account, $item);
        }
    }

    /**
     * 处理单个数据项
     *
     * @param Account $account 账户信息
     * @param mixed $item 数据项
     */
    private function processDataItem(Account $account, mixed $item): void
    {
        if (!is_array($item)) {
            return;
        }

        /** @var array<string, mixed> $item */
        $date = $this->extractDate($item);
        $refHour = $this->extractRefHour($item);
        $msgUser = $this->extractMsgUser($item);
        $msgCount = $this->extractMsgCount($item);
        $msgType = $this->extractMsgType($item);

        $messageSendHourData = $this->findOrCreateMessageSendHourData($account, $date, $refHour);
        $messageSendHourData->setMsgUser($msgUser);
        $messageSendHourData->setMsgCount($msgCount);

        if (null !== $msgType) {
            $messageSendHourData->setMsgType($msgType);
        }

        $this->entityManager->persist($messageSendHourData);
    }

    /**
     * 提取日期
     *
     * @param array<string, mixed> $item 数据项
     * @return \DateTimeImmutable
     */
    private function extractDate(array $item): \DateTimeImmutable
    {
        $refDate = '';
        if (isset($item['ref_date']) && is_string($item['ref_date'])) {
            $refDate = $item['ref_date'];
        }

        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $refDate);
        if (false === $dateTime) {
            $dateTime = new \DateTimeImmutable();
        }

        return $dateTime->setTime(0, 0, 0);
    }

    /**
     * 提取小时
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractRefHour(array $item): int
    {
        if (isset($item['ref_hour']) && is_numeric($item['ref_hour'])) {
            return (int) $item['ref_hour'];
        }

        return 0;
    }

    /**
     * 提取消息类型枚举值
     *
     * @param array<string, mixed> $item 数据项
     * @return MessageSendDataTypeEnum|null
     */
    private function extractMsgType(array $item): ?MessageSendDataTypeEnum
    {
        $msgTypeValue = 0;
        if (isset($item['msg_type']) && is_numeric($item['msg_type'])) {
            $msgTypeValue = (int) $item['msg_type'];
        }

        return MessageSendDataTypeEnum::tryFrom($msgTypeValue);
    }

    /**
     * 提取消息用户数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractMsgUser(array $item): int
    {
        if (isset($item['msg_user']) && is_numeric($item['msg_user'])) {
            return (int) $item['msg_user'];
        }

        return 0;
    }

    /**
     * 提取消息数量
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractMsgCount(array $item): int
    {
        if (isset($item['msg_count']) && is_numeric($item['msg_count'])) {
            return (int) $item['msg_count'];
        }

        return 0;
    }

    /**
     * 查找或创建消息发送分时数据实体
     *
     * @param Account $account 账户信息
     * @param \DateTimeImmutable $date 日期
     * @param int $refHour 小时
     * @return MessageSendHourData
     */
    private function findOrCreateMessageSendHourData(
        Account $account,
        \DateTimeImmutable $date,
        int $refHour,
    ): MessageSendHourData {
        $messageSendHourData = $this->messageSendHourDataRepository->findOneBy([
            'account' => $account,
            'date' => $date,
            'refHour' => $refHour,
        ]);

        if (null === $messageSendHourData) {
            $messageSendHourData = new MessageSendHourData();
            $messageSendHourData->setAccount($account);
            $messageSendHourData->setDate($date);
            $messageSendHourData->setRefHour($refHour);
        }

        return $messageSendHourData;
    }
}
