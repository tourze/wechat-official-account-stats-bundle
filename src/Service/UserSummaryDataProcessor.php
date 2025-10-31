<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;

class UserSummaryDataProcessor
{
    public function __construct(
        private readonly UserSummaryRepository $summaryRepository,
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
            $this->logger->error('获取用户增减数据发生错误', [
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
        $source = $this->extractSource($item);

        if (null === $source) {
            return; // 跳过无效的枚举值
        }

        $newUser = $this->extractNewUser($item);
        $cancelUser = $this->extractCancelUser($item);

        $userSummary = $this->findOrCreateUserSummary($account, $date, $source);
        $userSummary->setNewUser($newUser);
        $userSummary->setCancelUser($cancelUser);

        $this->entityManager->persist($userSummary);
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
     * 提取来源枚举值
     *
     * @param array<string, mixed> $item 数据项
     * @return UserSummarySource|null
     */
    private function extractSource(array $item): ?UserSummarySource
    {
        $sourceValue = 0;
        if (isset($item['user_source']) && is_numeric($item['user_source'])) {
            $sourceValue = (int) $item['user_source'];
        }

        return UserSummarySource::tryFrom($sourceValue);
    }

    /**
     * 提取新增用户数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractNewUser(array $item): int
    {
        $newUser = 0;
        if (isset($item['new_user']) && is_numeric($item['new_user'])) {
            $newUser = (int) $item['new_user'];
        }

        return $newUser;
    }

    /**
     * 提取取消关注用户数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractCancelUser(array $item): int
    {
        $cancelUser = 0;
        if (isset($item['cancel_user']) && is_numeric($item['cancel_user'])) {
            $cancelUser = (int) $item['cancel_user'];
        }

        return $cancelUser;
    }

    /**
     * 查找或创建用户摘要数据实体
     *
     * @param Account $account 账户信息
     * @param \DateTimeImmutable $date 日期
     * @param UserSummarySource $source 来源
     * @return UserSummary
     */
    private function findOrCreateUserSummary(
        Account $account,
        \DateTimeImmutable $date,
        UserSummarySource $source,
    ): UserSummary {
        $userSummary = $this->summaryRepository->findOneBy([
            'account' => $account,
            'date' => $date,
            'source' => $source,
        ]);

        if (null === $userSummary) {
            $userSummary = new UserSummary();
            $userSummary->setAccount($account);
            $userSummary->setDate($date);
            $userSummary->setSource($source);
        }

        return $userSummary;
    }
}
