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
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;
use WechatOfficialAccountStatsBundle\Repository\ImageTextShareDataHourRepository;
use WechatOfficialAccountStatsBundle\Request\GetUserShareHourRequest;

/**
 * 获取图文分享转发分时数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Graphic_Analysis_Data_Interface.html
 */
#[AsCronTask(expression: '0 12 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取图文分享转发分时数据')]
class SyncImageTextShareDataHourCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-image-text-share-data-hour';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly ImageTextShareDataHourRepository $imageTextShareDataHourRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetUserShareHourRequest();
            $request->setAccount($account);
            $request->setBeginDate(CarbonImmutable::now()->subDays());
            $request->setEndDate(CarbonImmutable::now()->subDays());
            $response = $this->client->request($request);
            if (!isset($response['list'])) {
                $this->logger->error('获取图文分享转发分时数据发生错误', [
                    'account' => $account,
                    'response' => $response,
                ]);
                continue;
            }

            foreach ($response['list'] as $item) {
                $date = CarbonImmutable::parse($item['ref_date']);
                $userShareHour = $this->imageTextShareDataHourRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if ($userShareHour === null) {
                    $userShareHour = new ImageTextShareDataHour();
                    $userShareHour->setAccount($account);
                    $userShareHour->setDate($date);
                }
                $userShareHour->setRefHour($item['ref_hour']);
                $userShareHour->setShareScene($item['share_scene']);
                $userShareHour->setShareCount($item['share_count']);
                $userShareHour->setShareUser($item['share_user']);
                $this->entityManager->persist($userShareHour);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
