<?php

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;
use WechatOfficialAccountStatsBundle\Repository\InterfaceSummaryHourRepository;
use WechatOfficialAccountStatsBundle\Request\InterfaceSummaryHourDataRequest;

/**
 * 获取接口分析数据bu hour
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Analytics_API.html
 */
// #[AsCronTask('2 1 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取接口分析数据by hour')]
class SyncInterfaceSummaryHourCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-interface-summary-hour';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly InterfaceSummaryHourRepository $interfaceSummaryHourRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('获取接口分析数据')
            ->addArgument('startTime', InputArgument::OPTIONAL, 'order start time', Carbon::now()->subDay()->startOfDay()->format('Y-m-d'))
            ->addArgument('endTime', InputArgument::OPTIONAL, 'order end time', Carbon::now()->subDay()->endOfDay()->format('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTimeString = $input->getArgument('startTime');
        $endTimeString = $input->getArgument('endTime');

        $startTime = Carbon::parse($startTimeString);
        $endTime = Carbon::parse($endTimeString);
        // 判断开始时间和结束时间之间的跨度
        if ($startTime->diffInDays($endTime) > 30) {
            $output->writeln('开始时间和结束时间的跨度不能超过 30 天');

            return Command::FAILURE;
        }

        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new InterfaceSummaryHourDataRequest();
            $request->setAccount($account);
            $request->setBeginDate($startTime);
            $request->setEndDate($endTime);
            $response = $this->client->request($request);
            foreach ($response['list'] as $item) {
                $date = Carbon::parse($item['ref_date']);
                $interfaceSummaryHourData = $this->interfaceSummaryHourRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                    'refHour' => $item['ref_hour'],
                ]);
                if ($interfaceSummaryHourData === null) {
                    $interfaceSummaryHourData = new InterfaceSummaryHour();
                    $interfaceSummaryHourData->setAccount($account);
                    $interfaceSummaryHourData->setDate($date);
                    $interfaceSummaryHourData->setRefHour($item['ref_hour']);
                }
                $interfaceSummaryHourData->setCallbackCount($item['callback_count']);
                $interfaceSummaryHourData->setFailCount($item['fail_count']);
                $interfaceSummaryHourData->setMaxTimeCost($item['max_time_cost']);
                $interfaceSummaryHourData->setTotalTimeCost($item['total_time_cost']);
                $this->entityManager->persist($interfaceSummaryHourData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
