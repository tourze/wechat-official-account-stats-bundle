<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\MessageSenDistData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataCountIntervalEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSenDistDataRepository;

class MessageSendDistDataProcessor
{
    public function __construct(
        private readonly MessageSenDistDataRepository $messageSenDistDataRepository,
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
            $this->logger->error('获取消息发送分布数据发生错误', [
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
        $countInterval = $this->extractCountInterval($item);
        $msgUser = $this->extractMsgUser($item);

        if (null === $countInterval) {
            return; // 跳过无效的枚举值
        }

        $messageSenDistData = $this->findOrCreateMessageSenDistData($account, $date, $countInterval);
        $messageSenDistData->setMsgUser($msgUser);

        $this->entityManager->persist($messageSenDistData);
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
     * 提取计数间隔枚举值
     *
     * @param array<string, mixed> $item 数据项
     * @return MessageSendDataCountIntervalEnum|null
     */
    private function extractCountInterval(array $item): ?MessageSendDataCountIntervalEnum
    {
        $countIntervalValue = 0;
        if (isset($item['count_interval']) && is_numeric($item['count_interval'])) {
            $countIntervalValue = (int) $item['count_interval'];
        }

        return MessageSendDataCountIntervalEnum::tryFrom($countIntervalValue);
    }

    /**
     * 提取消息用户数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractMsgUser(array $item): int
    {
        $msgUser = 0;
        if (isset($item['msg_user']) && is_numeric($item['msg_user'])) {
            $msgUser = (int) $item['msg_user'];
        }

        return $msgUser;
    }

    /**
     * 查找或创建消息发送分布数据实体
     *
     * @param Account $account 账户信息
     * @param \DateTimeImmutable $date 日期
     * @param MessageSendDataCountIntervalEnum $countInterval 计数间隔
     * @return MessageSenDistData
     */
    private function findOrCreateMessageSenDistData(
        Account $account,
        \DateTimeImmutable $date,
        MessageSendDataCountIntervalEnum $countInterval,
    ): MessageSenDistData {
        $messageSenDistData = $this->messageSenDistDataRepository->findOneBy([
            'account' => $account,
            'date' => $date,
            'countInterval' => $countInterval,
        ]);

        if (null === $messageSenDistData) {
            $messageSenDistData = new MessageSenDistData();
            $messageSenDistData->setAccount($account);
            $messageSenDistData->setDate($date);
            $messageSenDistData->setCountInterval($countInterval);
        }

        return $messageSenDistData;
    }
}
