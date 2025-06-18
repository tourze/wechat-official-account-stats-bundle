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
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;
use WechatOfficialAccountStatsBundle\Repository\ArticleDailySummaryRepository;
use WechatOfficialAccountStatsBundle\Request\GetArticleSummaryRequest;

/**
 * 获取图文群发每日数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
#[AsCronTask('0 12 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文群发每日数据')]
class SyncArticleSummaryCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-article-summary';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ArticleDailySummaryRepository $articleSummaryRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetArticleSummaryRequest();
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
                $date = Carbon::parse($item['ref_date'])->startOfDay();
                $articleSummary = $this->articleSummaryRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                    'msgId' => $item['msgId'],
                ]);
                if ($articleSummary === null) {
                    $articleSummary = new ArticleDailySummary();
                    $articleSummary->setAccount($account);
                    $articleSummary->setDate($date);
                    $articleSummary->setMsgId($item['msgId']);
                }
                $articleSummary->setTitle($item['title']);
                $articleSummary->setIntPageReadUser($item['int_page_read_user']);
                $articleSummary->setIntPageReadCount($item['int_page_read_count']);
                $articleSummary->setOriPageReadUser($item['ori_page_read_user']);
                $articleSummary->setOriPageReadCount($item['ori_page_read_count']);
                $articleSummary->setShareUser($item['share_user']);
                $articleSummary->setShareCount($item['share_count']);
                $articleSummary->setAddToFavUser($item['add_to_fav_user']);
                $articleSummary->setAddToFavCount($item['add_to_fav_count']);
                $this->entityManager->persist($articleSummary);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
