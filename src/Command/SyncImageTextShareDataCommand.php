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
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetUserShareRequest;

/**
 * 获取图文分享转发数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
// 每周一跑
#[AsCronTask(expression: '11 1 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文分享转发数据')]
class SyncImageTextShareDataCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-image-text-share-data';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ImageTextShareDataRepository $imageTextShareDataRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetUserShareRequest();
            $request->setAccount($account);
            $request->setBeginDate(CarbonImmutable::now()->weekday(1)->subDays(7));
            $request->setEndDate(CarbonImmutable::now()->weekday(6)->subDays(6));
            $response = $this->client->request($request);
            if (!isset($response['list'])) {
                $this->logger->error('获取图文分享转发数据发生错误', [
                    'account' => $account,
                    'response' => $response,
                ]);
                continue;
            }

            foreach ($response['list'] as $item) {
                $date = CarbonImmutable::parse($item['ref_date']);
                $userShare = $this->imageTextShareDataRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if ($userShare === null) {
                    $userShare = new ImageTextShareData();
                    $userShare->setAccount($account);
                    $userShare->setDate($date);
                }
                $userShare->setShareScene($item['share_scene']);
                $userShare->setShareCount($item['share_count']);
                $userShare->setShareUser($item['share_user']);
                $this->entityManager->persist($userShare);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
