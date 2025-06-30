<?php

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryRepository;
use WechatOfficialAccountStatsBundle\Request\InterfaceSummaryDataRequest;

/**
 * 获取接口分析数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Analytics_API.html
 */
// #[AsCronTask(expression: '2 1 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取接口分析数据')]
class SyncInterfaceSummaryCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-interface-summary';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly InterfaceSummaryRepository $interfaceSummaryRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('获取接口分析数据')
            ->addArgument('startTime', InputArgument::OPTIONAL, 'order start time', CarbonImmutable::now()->subDay()->startOfDay()->format('Y-m-d'))
            ->addArgument('endTime', InputArgument::OPTIONAL, 'order end time', CarbonImmutable::now()->subDay()->endOfDay()->format('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTimeString = $input->getArgument('startTime');
        $endTimeString = $input->getArgument('endTime');

        $startTime = CarbonImmutable::parse($startTimeString);
        $endTime = CarbonImmutable::parse($endTimeString);
        // 判断开始时间和结束时间之间的跨度
        if ($startTime->diffInDays($endTime) > 30) {
            $output->writeln('开始时间和结束时间的跨度不能超过 30 天');

            return Command::FAILURE;
        }

        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new InterfaceSummaryDataRequest();
            $request->setAccount($account);
            $request->setBeginDate($startTime);
            $request->setEndDate($endTime);
            $response = $this->client->request($request);
            foreach ($response['list'] as $item) {
                $date = CarbonImmutable::parse($item['ref_date']);
                $interfaceSummaryData = $this->interfaceSummaryRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if ($interfaceSummaryData === null) {
                    $interfaceSummaryData = new InterfaceSummary();
                    $interfaceSummaryData->setAccount($account);
                    $interfaceSummaryData->setDate($date);
                }
                $interfaceSummaryData->setCallbackCount($item['callback_count']);
                $interfaceSummaryData->setFailCount($item['fail_count']);
                $interfaceSummaryData->setMaxTimeCost($item['max_time_cost']);
                $interfaceSummaryData->setTotalTimeCost($item['total_time_cost']);
                $this->entityManager->persist($interfaceSummaryData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
