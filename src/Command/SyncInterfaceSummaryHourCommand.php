<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Request\InterfaceSummaryHourDataRequest;
use WechatOfficialAccountStatsBundle\Service\InterfaceSummaryHourProcessor;

/**
 * 获取接口分析数据bu hour
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Analytics_API.html
 */
#[WithMonologChannel(channel: 'wechat_official_account_stats')]
// #[AsCronTask(expression: '2 1 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取接口分析数据by hour')]
class SyncInterfaceSummaryHourCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-interface-summary-hour';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly InterfaceSummaryHourProcessor $dataProcessor,
        private readonly LoggerInterface $logger,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('获取接口分析数据')
            ->addArgument('startTime', InputArgument::OPTIONAL, 'order start time', CarbonImmutable::yesterday()->format('Y-m-d'))
            ->addArgument('endTime', InputArgument::OPTIONAL, 'order end time', CarbonImmutable::yesterday()->format('Y-m-d'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTimeString = $input->getArgument('startTime');
        $endTimeString = $input->getArgument('endTime');

        if (!is_string($startTimeString) || !is_string($endTimeString)) {
            $output->writeln('开始时间和结束时间必须是字符串');

            return Command::FAILURE;
        }

        $startTime = CarbonImmutable::parse($startTimeString);
        $endTime = CarbonImmutable::parse($endTimeString);

        // 判断开始时间和结束时间之间的跨度
        if ($startTime->diffInDays($endTime) > 30) {
            $output->writeln('开始时间和结束时间的跨度不能超过 30 天');

            return Command::FAILURE;
        }

        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            try {
                $this->processAccount($account, $startTime, $endTime);
            } catch (\Throwable $e) {
                $this->logger->error('处理账户接口分析分时数据失败', [
                    'account' => $account,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        return Command::SUCCESS;
    }

    /**
     * 处理单个账户
     */
    private function processAccount(Account $account, CarbonImmutable $startTime, CarbonImmutable $endTime): void
    {
        $request = new InterfaceSummaryHourDataRequest();
        $request->setAccount($account);
        $request->setBeginDate($startTime);
        $request->setEndDate($endTime);

        $response = $this->client->request($request);

        if (!is_array($response)) {
            $this->logger->error('API响应格式错误', [
                'account' => $account,
                'response' => $response,
            ]);

            return;
        }

        /** @var array<string, mixed> $typedResponse */
        $typedResponse = $response;
        $this->dataProcessor->processResponse($typedResponse, $account);
    }
}
