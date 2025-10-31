<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;
use WechatOfficialAccountStatsBundle\Repository\UserCumulateRepository;

class UserCumulateDataProcessor
{
    public function __construct(
        private readonly UserCumulateRepository $cumulateRepository,
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
            $this->logger->error('获取累计用户数据发生错误', [
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
        $cumulateUser = $this->extractCumulateUser($item);

        $cumulate = $this->findOrCreateCumulate($account, $date);
        $cumulate->setCumulateUser($cumulateUser);

        $this->entityManager->persist($cumulate);
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
     * 提取累计用户数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractCumulateUser(array $item): int
    {
        return isset($item['cumulate_user']) && is_int($item['cumulate_user']) ? $item['cumulate_user'] : 0;
    }

    /**
     * 查找或创建累计用户实体
     *
     * @param Account $account 账户信息
     * @param \DateTimeImmutable $date 日期
     * @return UserCumulate
     */
    private function findOrCreateCumulate(Account $account, \DateTimeImmutable $date): UserCumulate
    {
        $cumulate = $this->cumulateRepository->findOneBy([
            'account' => $account,
            'date' => $date,
        ]);

        if (null === $cumulate) {
            $cumulate = new UserCumulate();
            $cumulate->setAccount($account);
            $cumulate->setDate($date);
        }

        return $cumulate;
    }
}
