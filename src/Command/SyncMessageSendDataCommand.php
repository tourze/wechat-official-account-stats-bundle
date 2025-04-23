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
use WechatOfficialAccountStatsBundle\Entity\MessageSendData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetMessageSendDataRequest;

/**
 * 获取消息发送概况数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
 */
#[AsCronTask('2 1 * * *')]
#[AsCommand(name: 'wechat:official-account:SyncMessageSendDataCommand', description: '公众号-获取消息发送概况数据')]
class SyncMessageSendDataCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly MessageSendDataRepository $messageSendDataRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetMessageSendDataRequest();
            $request->setAccount($account);
            $request->setBeginDate(Carbon::now()->weekday(1)->subDays(7));
            $request->setEndDate(Carbon::now()->weekday(6)->subDays(6));
            $response = $this->client->request($request);
            foreach ($response['list'] as $item) {
                $date = Carbon::parse($item['ref_date']);
                $messageSendData = $this->messageSendDataRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if (!$messageSendData) {
                    $messageSendData = new MessageSendData();
                    $messageSendData->setAccount($account);
                    $messageSendData->setDate($date);
                }
                $messageSendData->setMsgType(MessageSendDataTypeEnum::tryFrom($item['msg_type']));
                $messageSendData->setMsgUser($item['msg_user']);
                $messageSendData->setMsgCount($item['msg_count']);
                $this->entityManager->persist($messageSendData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
