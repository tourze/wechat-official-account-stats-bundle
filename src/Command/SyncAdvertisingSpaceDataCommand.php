<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Command;

use DateTimeImmutable;
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
use WechatOfficialAccountStatsBundle\Request\GetAdvertisingSpaceDataRequest;
use WechatOfficialAccountStatsBundle\Service\AdvertisingSpaceDataProcessor;

/**
 * 获取公众号分广告位数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Ad_Analysis.html
 */
#[AsCronTask(expression: '1 1 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取公众号分广告位数据')]
#[WithMonologChannel(channel: 'wechat_official_account_stats')]
class SyncAdvertisingSpaceDataCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-advertising-space-data';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly AdvertisingSpaceDataProcessor $dataProcessor,
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
                $this->logger->error('处理账户广告位数据失败', [
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
        $request = new GetAdvertisingSpaceDataRequest();
        $request->setAction('publisher_adpos_general');
        $request->setAccount($account);
        $request->setPage('1');
        $request->setPageSize('10');

        // 使用DateTimeImmutable替代Carbon
        $now = new \DateTimeImmutable();
        $startOfWeek = $now->modify('this week');
        $startDate = $startOfWeek->modify('-1 week')->format('Y-m-d');
        $endDate = $startOfWeek->modify('-1 day')->format('Y-m-d');

        $request->setStartDate($startDate);
        $request->setEndDate($endDate);

        $result = $this->client->request($request);

        if (!is_array($result)) {
            $this->logger->error('API响应格式错误', [
                'account' => $account,
                'response' => $result,
            ]);

            return;
        }

        /** @var array<string, mixed> $typedResult */
        $typedResult = $result;
        $this->dataProcessor->processResponse($typedResult, $account);
    }
}
