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
use WechatOfficialAccountStatsBundle\Entity\MessageSendWeekData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendWeekDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetMessageSendWeekDataRequest;

/**
 * 获取消息发送周数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
 */
#[AsCronTask('50 3 * * *')]
#[AsCommand(name: 'wechat:official-account:SyncMessageSendWeekDataCommand', description: '公众号-获取消息发送周数据')]
class SyncMessageSendWeekDataCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly MessageSendWeekDataRepository $messageSendWeekDataRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetMessageSendWeekDataRequest();
            $request->setAccount($account);
            $request->setBeginDate(Carbon::now()->weekday(1)->subDays(7));
            $request->setEndDate(Carbon::now()->weekday(6)->subDays(6));
            $response = $this->client->request($request);
            foreach ($response['list'] as $item) {
                $date = Carbon::parse($item['ref_date']);
                $messageSendWeekData = $this->messageSendWeekDataRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if (!$messageSendWeekData) {
                    $messageSendWeekData = new MessageSendWeekData();
                    $messageSendWeekData->setAccount($account);
                    $messageSendWeekData->setDate($date);
                }
                $messageSendWeekData->setMsgType(MessageSendDataTypeEnum::tryFrom($item['msg_type']));
                $messageSendWeekData->setMsgUser($item['msg_user']);
                $messageSendWeekData->setMsgCount($item['msg_count']);
                $this->entityManager->persist($messageSendWeekData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
