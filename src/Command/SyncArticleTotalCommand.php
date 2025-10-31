<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
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
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;
use WechatOfficialAccountStatsBundle\Repository\ArticleTotalRepository;
use WechatOfficialAccountStatsBundle\Request\GetArticleTotalRequest;
use WechatOfficialAccountStatsBundle\Service\ArticleTotalDataExtractor;

/**
 * 获取图文群发总数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
#[WithMonologChannel(channel: 'wechat_official_account_stats')]
#[AsCronTask(expression: '0 12 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文群发总数据')]
class SyncArticleTotalCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-article-total';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ArticleTotalRepository $articleTotalRepository,
        private readonly ArticleTotalDataExtractor $dataExtractor,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $this->syncArticleTotalForAccount($account);
        }

        return Command::SUCCESS;
    }

    private function syncArticleTotalForAccount(Account $account): void
    {
        $request = $this->createRequest($account);
        $response = $this->client->request($request);

        if (!$this->isValidResponse($response)) {
            $this->logError($account, $response);

            return;
        }

        assert(is_array($response));
        assert(is_array($response['list']));
        $this->processResponseList($account, $response['list']);
    }

    /**
     * @param mixed $response
     */
    private function isValidResponse(mixed $response): bool
    {
        return is_array($response) && isset($response['list']) && is_array($response['list']);
    }

    private function createRequest(Account $account): GetArticleTotalRequest
    {
        $request = new GetArticleTotalRequest();
        $request->setAccount($account);
        $request->setBeginDate(CarbonImmutable::now()->subDays());
        $request->setEndDate(CarbonImmutable::now()->subDays());

        return $request;
    }

    /**
     * @param mixed $response
     */
    private function logError(Account $account, mixed $response): void
    {
        $this->logger->error('获取累计用户数据发生错误', [
            'account' => $account,
            'response' => $response,
        ]);
    }

    /**
     * @param array<mixed> $list
     */
    private function processResponseList(Account $account, array $list): void
    {
        foreach ($list as $item) {
            if (!is_array($item)) {
                continue;
            }
            /** @var array<string, mixed> $item */
            $this->processResponseItem($account, $item);
        }
        $this->entityManager->flush();
    }

    /**
     * @param array<string, mixed> $item
     */
    private function processResponseItem(Account $account, array $item): void
    {
        $date = $this->extractRefDate($item);
        $details = $this->extractDetails($item);

        if (null === $details) {
            return;
        }

        foreach ($details as $detailValue) {
            if (!is_array($detailValue)) {
                continue;
            }
            /** @var array<string, mixed> $detailValue */
            $this->processArticleDetail($account, $date, $item, $detailValue);
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function extractRefDate(array $item): CarbonImmutable
    {
        $refDate = isset($item['ref_date']) && is_string($item['ref_date']) ? $item['ref_date'] : '';

        return CarbonImmutable::parse($refDate);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<mixed, mixed>|null
     */
    private function extractDetails(array $item): ?array
    {
        if (!isset($item['details']) || !is_array($item['details'])) {
            return null;
        }

        return $item['details'];
    }

    /**
     * @param array<string, mixed> $item
     * @param array<string, mixed> $detailValue
     */
    private function processArticleDetail(Account $account, CarbonImmutable $date, array $item, array $detailValue): void
    {
        $articleTotal = $this->findOrCreateArticleTotal($account, $date, $detailValue);
        $this->updateArticleTotal($articleTotal, $item, $detailValue);
        $this->entityManager->persist($articleTotal);
    }

    /**
     * @param array<string, mixed> $detailValue
     */
    private function findOrCreateArticleTotal(Account $account, CarbonImmutable $date, array $detailValue): ArticleTotal
    {
        /** @var ArticleTotal|null $articleTotal */
        $articleTotal = $this->articleTotalRepository->findOneBy([
            'account' => $account,
            'date' => $date,
            'statDate' => CarbonImmutable::parse(isset($detailValue['stat_date']) && is_string($detailValue['stat_date']) ? $detailValue['stat_date'] : ''),
        ]);

        if (null === $articleTotal) {
            $articleTotal = new ArticleTotal();
            $articleTotal->setAccount($account);
            $articleTotal->setDate($date);
            $articleTotal->setStatDate(CarbonImmutable::parse(isset($detailValue['stat_date']) && is_string($detailValue['stat_date']) ? $detailValue['stat_date'] : ''));
        }

        return $articleTotal;
    }

    /**
     * @param array<string, mixed> $item
     * @param array<string, mixed> $detailValue
     */
    private function updateArticleTotal(ArticleTotal $articleTotal, array $item, array $detailValue): void
    {
        $this->dataExtractor->populate($articleTotal, $item, $detailValue);
    }
}
