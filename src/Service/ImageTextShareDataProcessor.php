<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataRepository;

class ImageTextShareDataProcessor
{
    public function __construct(
        private readonly ImageTextShareDataRepository $imageTextShareDataRepository,
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
            $this->logger->error('获取图文分享转发数据发生错误', [
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
        $shareScene = $this->extractShareScene($item);
        $shareCount = $this->extractShareCount($item);
        $shareUser = $this->extractShareUser($item);

        $imageTextShareData = $this->findOrCreateImageTextShareData($account, $date);
        $imageTextShareData->setShareScene($shareScene);
        $imageTextShareData->setShareCount($shareCount);
        $imageTextShareData->setShareUser($shareUser);

        $this->entityManager->persist($imageTextShareData);
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
     * 提取分享场景
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractShareScene(array $item): int
    {
        if (isset($item['share_scene']) && is_numeric($item['share_scene'])) {
            return (int) $item['share_scene'];
        }

        return 0;
    }

    /**
     * 提取分享次数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractShareCount(array $item): int
    {
        if (isset($item['share_count']) && is_numeric($item['share_count'])) {
            return (int) $item['share_count'];
        }

        return 0;
    }

    /**
     * 提取分享人数
     *
     * @param array<string, mixed> $item 数据项
     * @return int
     */
    private function extractShareUser(array $item): int
    {
        if (isset($item['share_user']) && is_numeric($item['share_user'])) {
            return (int) $item['share_user'];
        }

        return 0;
    }

    /**
     * 查找或创建图文分享数据实体
     *
     * @param Account $account 账户信息
     * @param \DateTimeImmutable $date 日期
     * @return ImageTextShareData
     */
    private function findOrCreateImageTextShareData(
        Account $account,
        \DateTimeImmutable $date,
    ): ImageTextShareData {
        $imageTextShareData = $this->imageTextShareDataRepository->findOneBy([
            'account' => $account,
            'date' => $date,
        ]);

        if (null === $imageTextShareData) {
            $imageTextShareData = new ImageTextShareData();
            $imageTextShareData->setAccount($account);
            $imageTextShareData->setDate($date);
        }

        return $imageTextShareData;
    }
}
