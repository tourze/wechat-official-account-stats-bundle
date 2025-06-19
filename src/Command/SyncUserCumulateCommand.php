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
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;
use WechatOfficialAccountStatsBundle\Repository\UserCumulateRepository;
use WechatOfficialAccountStatsBundle\Request\GetUserCumulateRequest;

/**
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/User_Analysis_Data_Interface.html
 */
#[AsCronTask('4 3 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取累计用户数据')]
class SyncUserCumulateCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-user-cumulate';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly UserCumulateRepository $cumulateRepository,
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
            $request = new GetUserCumulateRequest();
            $request->setAccount($account);
            $request->setBeginDate($startDate);
            $request->setEndDate($endDate);
            $response = $this->client->request($request);
            if (!isset($response['list'])) {
                $this->logger->error('获取累计用户数据发生错误', [
                    'account' => $account,
                    'response' => $response,
                ]);
                continue;
            }

            foreach ($response['list'] as $item) {
                $date = CarbonImmutable::parse($item['ref_date'])->startOfDay();

                $cumulate = $this->cumulateRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if ($cumulate === null) {
                    $cumulate = new UserCumulate();
                    $cumulate->setAccount($account);
                    $cumulate->setDate($date);
                }
                $cumulate->setCumulateUser($item['cumulate_user']);
                $this->entityManager->persist($cumulate);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
