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
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;
use WechatOfficialAccountStatsBundle\Repository\ArticleTotalRepository;
use WechatOfficialAccountStatsBundle\Request\GetArticleTotalRequest;

/**
 * 获取图文群发总数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
#[AsCronTask('0 12 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文群发总数据')]
class SyncArticleTotalCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-article-total';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ArticleTotalRepository $articleTotalRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetArticleTotalRequest();
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
                foreach ($item['details'] as $detailValue) {
                    $articleTotal = $this->articleTotalRepository->findOneBy([
                        'account' => $account,
                        'date' => $date,
                        'stat_date' => CarbonImmutable::parse($detailValue['stat_date']),
                    ]);
                    if ($articleTotal === null) {
                        $articleTotal = new ArticleTotal();
                        $articleTotal->setAccount($account);
                        $articleTotal->setDate($date);
                        $articleTotal->setStatDate(CarbonImmutable::parse($detailValue['stat_date']));
                    }
                    $articleTotal->setMsgId($item['msgId']);
                    $articleTotal->setTitle($item['title']);
                    $articleTotal->setTargetUser($detailValue['target_user']);
                    $articleTotal->setIntPageReadUser($detailValue['int_page_read_user']);
                    $articleTotal->setIntPageReadCount($detailValue['int_page_read_count']);
                    $articleTotal->setOriPageReadUser($detailValue['ori_page_read_user']);
                    $articleTotal->setOriPageReadCount($detailValue['ori_page_read_count']);
                    $articleTotal->setShareUser($detailValue['share_user']);
                    $articleTotal->setShareCount($detailValue['share_count']);
                    $articleTotal->setAddToFavUser($detailValue['add_to_fav_user']);
                    $articleTotal->setAddToFavCount($detailValue['add_to_fav_count']);
                    $articleTotal->setIntPageFromSessionReadUser($detailValue['int_page_from_session_read_user']);
                    $articleTotal->setIntPageFromSessionReadCount($detailValue['int_page_from_session_read_count']);
                    $articleTotal->setIntPageFromHistMsgReadUser($detailValue['int_page_from_hist_msg_read_user']);
                    $articleTotal->setIntPageFromHistMsgReadCount($detailValue['int_page_from_hist_msg_read_count']);
                    $articleTotal->setIntPageFromFeedReadUser($detailValue['int_page_from_feed_read_user']);
                    $articleTotal->setIntPageFromFeedReadCount($detailValue['int_page_from_feed_read_count']);
                    $articleTotal->setIntPageFromFriendsReadUser($detailValue['int_page_from_friends_read_user']);
                    $articleTotal->setIntPageFromFriendsReadCount($detailValue['int_page_from_friends_read_count']);
                    $articleTotal->setIntPageFromOtherReadUser($detailValue['int_page_from_other_read_user']);
                    $articleTotal->setIntPageFromOtherReadCount($detailValue['int_page_from_other_read_count']);
                    $articleTotal->setFeedShareFromSessionUser($detailValue['feed_share_from_session_user']);
                    $articleTotal->setFeedShareFromSessionCnt($detailValue['feed_share_from_session_cnt']);
                    $articleTotal->setFeedShareFromFeedUser($detailValue['feed_share_from_feed_user']);
                    $articleTotal->setFeedShareFromFeedCnt($detailValue['feed_share_from_feed_cnt']);
                    $articleTotal->setFeedShareFromOtherUser($detailValue['feed_share_from_other_user']);
                    $articleTotal->setFeedShareFromOtherCnt($detailValue['feed_share_from_other_cnt']);
                    $this->entityManager->persist($articleTotal);
                    $this->entityManager->flush();
                }
            }
        }

        return Command::SUCCESS;
    }
}
