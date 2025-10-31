<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;
use WechatOfficialAccountStatsBundle\Repository\ArticleDailySummaryRepository;

/**
 * 图文群发每日数据处理器
 */
class ArticleSummaryProcessor
{
    public function __construct(
        private readonly ArticleDailySummaryRepository $articleSummaryRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 处理图文群发每日数据响应
     *
     * @param array<string, mixed> $response API响应数据
     * @param Account $account 账户
     */
    public function processResponse(array $response, Account $account): void
    {
        if (!isset($response['list']) || !is_array($response['list'])) {
            $this->logger->error('获取累计用户数据发生错误', [
                'account' => $account,
                'response' => $response,
            ]);

            return;
        }

        foreach ($response['list'] as $item) {
            /** @var array<string, mixed> $item */

            try {
                $this->processItem($item, $account);
            } catch (\Throwable $e) {
                $this->logger->error('处理图文群发每日数据项失败', [
                    'account' => $account,
                    'item' => $item,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * 处理单个数据项
     *
     * @param array<string, mixed> $item 单个数据项
     * @param Account $account 账户
     */
    private function processItem(array $item, Account $account): void
    {
        $date = $this->parseDate($item);
        $msgId = $this->extractString($item, 'msgId');
        $articleSummary = $this->findOrCreateEntity($account, $date, $msgId);

        $this->updateEntity($articleSummary, $item);
        $this->entityManager->persist($articleSummary);
    }

    /**
     * 解析日期
     *
     * @param array<string, mixed> $item 数据项
     */
    private function parseDate(array $item): \DateTimeImmutable
    {
        $dateString = isset($item['ref_date']) && is_string($item['ref_date']) ? $item['ref_date'] : '';
        $date = new \DateTimeImmutable($dateString);

        return $date->setTime(0, 0, 0); // 开始时间
    }

    /**
     * 查找或创建实体
     */
    private function findOrCreateEntity(Account $account, \DateTimeImmutable $date, string $msgId): ArticleDailySummary
    {
        $articleSummary = $this->articleSummaryRepository->findOneBy([
            'account' => $account,
            'date' => $date,
            'msgId' => $msgId,
        ]);

        if (null === $articleSummary) {
            $articleSummary = new ArticleDailySummary();
            $articleSummary->setAccount($account);
            $articleSummary->setDate($date);
            $articleSummary->setMsgId($msgId);
        }

        return $articleSummary;
    }

    /**
     * 更新实体数据
     *
     * @param ArticleDailySummary $entity 实体
     * @param array<string, mixed> $item 数据项
     */
    private function updateEntity(ArticleDailySummary $entity, array $item): void
    {
        $entity->setTitle($this->extractString($item, 'title'));
        $entity->setIntPageReadUser($this->extractInt($item, 'int_page_read_user'));
        $entity->setIntPageReadCount($this->extractInt($item, 'int_page_read_count'));
        $entity->setOriPageReadUser($this->extractInt($item, 'ori_page_read_user'));
        $entity->setOriPageReadCount($this->extractInt($item, 'ori_page_read_count'));
        $entity->setShareUser($this->extractInt($item, 'share_user'));
        $entity->setShareCount($this->extractInt($item, 'share_count'));
        $entity->setAddToFavUser($this->extractInt($item, 'add_to_fav_user'));
        $entity->setAddToFavCount($this->extractInt($item, 'add_to_fav_count'));
    }

    /**
     * 提取整数值
     *
     * @param array<string, mixed> $data 数据
     * @param string $key 键名
     */
    private function extractInt(array $data, string $key): int
    {
        return isset($data[$key]) && is_numeric($data[$key]) ? (int) $data[$key] : 0;
    }

    /**
     * 提取字符串值
     *
     * @param array<string, mixed> $data 数据
     * @param string $key 键名
     */
    private function extractString(array $data, string $key): string
    {
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : '';
    }
}
