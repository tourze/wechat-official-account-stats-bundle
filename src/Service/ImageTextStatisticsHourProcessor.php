<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsHourRepository;

/**
 * 图文统计分时数据处理器
 */
class ImageTextStatisticsHourProcessor
{
    public function __construct(
        private readonly ImageTextStatisticsHourRepository $imageTextStatisticsHourRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 处理图文统计分时数据响应
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
                $this->logger->error('处理图文统计分时数据项失败', [
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
        $imageTextStatisticsHour = $this->findOrCreateEntity($account, $date);

        $this->updateEntity($imageTextStatisticsHour, $item);
        $this->entityManager->persist($imageTextStatisticsHour);
    }

    /**
     * 解析日期
     *
     * @param array<string, mixed> $item 数据项
     */
    private function parseDate(array $item): \DateTimeImmutable
    {
        $dateString = isset($item['ref_date']) && is_string($item['ref_date']) ? $item['ref_date'] : '';

        return new \DateTimeImmutable($dateString);
    }

    /**
     * 查找或创建实体
     */
    private function findOrCreateEntity(Account $account, \DateTimeImmutable $date): ImageTextStatisticsHour
    {
        $imageTextStatisticsHour = $this->imageTextStatisticsHourRepository->findOneBy([
            'account' => $account,
            'date' => $date,
        ]);

        if (null === $imageTextStatisticsHour) {
            $imageTextStatisticsHour = new ImageTextStatisticsHour();
            $imageTextStatisticsHour->setAccount($account);
            $imageTextStatisticsHour->setDate($date);
        }

        return $imageTextStatisticsHour;
    }

    /**
     * 更新实体数据
     *
     * @param ImageTextStatisticsHour $entity 实体
     * @param array<string, mixed> $item 数据项
     */
    private function updateEntity(ImageTextStatisticsHour $entity, array $item): void
    {
        $entity->setRefHour($this->extractInt($item, 'ref_hour'));
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
}
