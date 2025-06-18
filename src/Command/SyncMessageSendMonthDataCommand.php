<?php

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendMonthDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetMessageSendMonthDataRequest;

/**
 * 获取消息发送月数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
 */
// 每个月1号执行
#[AsCronTask('22 4 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取消息发送月数据')]
class SyncMessageSendMonthDataCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-message-send-month-data';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly MessageSendMonthDataRepository $messageSendMonthDataRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetMessageSendMonthDataRequest();
            $request->setAccount($account);
            $request->setBeginDate(Carbon::now()->subMonth()->startOfMonth());
            $request->setEndDate(Carbon::now()->subMonth()->endOfMonth());
            $response = $this->client->request($request);
            foreach ($response['list'] as $item) {
                $date = Carbon::parse($item['ref_date']);
                $messageSendMonthData = $this->messageSendMonthDataRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if ($messageSendMonthData === null) {
                    $messageSendMonthData = new MessageSendMonthData();
                    $messageSendMonthData->setAccount($account);
                    $messageSendMonthData->setDate($date);
                }
                $messageSendMonthData->setMsgType(MessageSendDataTypeEnum::tryFrom($item['msg_type']));
                $messageSendMonthData->setMsgUser($item['msg_user']);
                $messageSendMonthData->setMsgCount($item['msg_count']);
                $this->entityManager->persist($messageSendMonthData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
