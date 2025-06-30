<?php

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsHourRepository;
use WechatOfficialAccountStatsBundle\Request\GetUserReadHourRequest;

/**
 * 获取图文统计分时数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
#[AsCronTask(expression: '0 12 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文统计分时数据')]
class SyncImageTextStatisticsHourCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-image-text-statistics-hour';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ImageTextStatisticsHourRepository $imageTextStatisticsHourRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetUserReadHourRequest();
            $request->setAccount($account);
            $request->setBeginDate(CarbonImmutable::now()->subDays());
            $request->setEndDate(CarbonImmutable::now()->subDays());
            $response = $this->client->request($request);
            if (!isset($response['list'])) {
                $this->logger->error('获取累计用户数据发生错误', [
                    'account' => $account,
                    'response' => $response,
                ]);
                continue;
            }

            foreach ($response['list'] as $item) {
                $date = CarbonImmutable::parse($item['ref_date']);
                $imageTextStatisticsHour = $this->imageTextStatisticsHourRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if ($imageTextStatisticsHour === null) {
                    $imageTextStatisticsHour = new ImageTextStatisticsHour();
                    $imageTextStatisticsHour->setAccount($account);
                    $imageTextStatisticsHour->setDate($date);
                }
                $imageTextStatisticsHour->setRefHour($item['ref_hour']);
                $imageTextStatisticsHour->setIntPageReadUser($item['int_page_read_user']);
                $imageTextStatisticsHour->setIntPageReadCount($item['int_page_read_count']);
                $imageTextStatisticsHour->setOriPageReadUser($item['ori_page_read_user']);
                $imageTextStatisticsHour->setOriPageReadCount($item['ori_page_read_count']);
                $imageTextStatisticsHour->setShareUser($item['share_user']);
                $imageTextStatisticsHour->setShareCount($item['share_count']);
                $imageTextStatisticsHour->setAddToFavUser($item['add_to_fav_user']);
                $imageTextStatisticsHour->setAddToFavCount($item['add_to_fav_count']);
                $this->entityManager->persist($imageTextStatisticsHour);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
