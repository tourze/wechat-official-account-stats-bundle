<?php

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\Carbon;
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
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;
use WechatOfficialAccountStatsBundle\Request\GetUserSummaryRequest;

/**
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/User_Analysis_Data_Interface.html
 */
#[AsCronTask('4 3 * * *')]
#[AsCommand(name: 'wechat:official-account:SyncUserSummaryCommand', description: '公众号-获取用户增减数据')]
class SyncUserSummaryCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly UserSummaryRepository $summaryRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endDate = CarbonImmutable::yesterday();
        $startDate = $endDate->subDays(6);
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetUserSummaryRequest();
            $request->setAccount($account);
            $request->setBeginDate($startDate);
            $request->setEndDate($endDate);
            $response = $this->client->request($request);
            if (!isset($response['list'])) {
                $this->logger->error('获取用户增减数据发生错误', [
                    'account' => $account,
                    'response' => $response,
                ]);
                continue;
            }

            foreach ($response['list'] as $item) {
                $date = Carbon::parse($item['ref_date'])->startOfDay();
                $source = UserSummarySource::tryFrom($item['user_source']);
                if (!$source) {
                    $this->logger->error('发生未知的数据来源', [
                        'item' => $item,
                    ]);
                    continue;
                }

                $summary = $this->summaryRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                    'source' => $source,
                ]);
                if (!$summary) {
                    $summary = new UserSummary();
                    $summary->setAccount($account);
                    $summary->setDate($date);
                    $summary->setSource($source);
                }
                $summary->setNewUser($item['new_user']);
                $summary->setCancelUser($item['cancel_user']);
                $this->entityManager->persist($summary);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
