<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Request\GetUserReadHourRequest;
use WechatOfficialAccountStatsBundle\Service\ImageTextStatisticsHourProcessor;

/**
 * 获取图文统计分时数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
#[WithMonologChannel(channel: 'wechat_official_account_stats')]
#[AsCronTask(expression: '0 12 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文统计分时数据')]
class SyncImageTextStatisticsHourCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-image-text-statistics-hour';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ImageTextStatisticsHourProcessor $dataProcessor,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            try {
                $this->processAccount($account);
            } catch (\Throwable $e) {
                $this->logger->error('处理账户图文统计分时数据失败', [
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
    private function processAccount(Account $account): void
    {
        $request = new GetUserReadHourRequest();
        $request->setAccount($account);

        // 使用Carbon
        $yesterday = CarbonImmutable::yesterday();
        $request->setBeginDate($yesterday);
        $request->setEndDate($yesterday);

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
