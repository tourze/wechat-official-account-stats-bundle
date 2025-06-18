<?php

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;
use WechatOfficialAccountStatsBundle\Enum\ImageTextUserSourceEnum;
use WechatOfficialAccountStatsBundle\Repository\ImageTextStatisticsRepository;
use WechatOfficialAccountStatsBundle\Request\GetUserReadRequest;

/**
 * 获取图文统计数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
#[AsCronTask('0 12 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文统计数据')]
class SyncImageTextStatisticsCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-image-text-statistics';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ImageTextStatisticsRepository $imageTextStatisticsRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetUserReadRequest();
            $request->setAccount($account);
            $request->setBeginDate(Carbon::now()->subDays());
            $request->setEndDate(Carbon::now()->subDays());
            $response = $this->client->request($request);
            if (!isset($response['list'])) {
                $this->logger->error('获取累计用户数据发生错误', [
                    'account' => $account,
                    'response' => $response,
                ]);
                continue;
            }

            foreach ($response['list'] as $item) {
                $date = Carbon::parse($item['ref_date']);
                $ImageTextStatistics = $this->imageTextStatisticsRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if ($ImageTextStatistics === null) {
                    $ImageTextStatistics = new ImageTextStatistics();
                    $ImageTextStatistics->setAccount($account);
                    $ImageTextStatistics->setDate($date);
                }
                $ImageTextStatistics->setUserSource(ImageTextUserSourceEnum::tryFrom($item['user_source']));
                $ImageTextStatistics->setIntPageReadUser($item['int_page_read_user']);
                $ImageTextStatistics->setIntPageReadCount($item['int_page_read_count']);
                $ImageTextStatistics->setOriPageReadUser($item['ori_page_read_user']);
                $ImageTextStatistics->setOriPageReadCount($item['ori_page_read_count']);
                $ImageTextStatistics->setShareUser($item['share_user']);
                $ImageTextStatistics->setShareCount($item['share_count']);
                $ImageTextStatistics->setAddToFavUser($item['add_to_fav_user']);
                $ImageTextStatistics->setAddToFavCount($item['add_to_fav_count']);
                $this->entityManager->persist($ImageTextStatistics);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
