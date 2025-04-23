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
use WechatOfficialAccountStatsBundle\Entity\MessageSenDistData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataCountIntervalEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSenDistDataRepository;
use WechatOfficialAccountStatsBundle\Request\MessageSendDistDataRequest;

/**
 * 获取消息发送分布数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
 */
#[AsCronTask('0 12 * * *')]
#[AsCommand(name: 'wechat:official-account:SyncMessageSendDIstDataCommand', description: '公众号-获取消息发送分布数据')]
class SyncMessageSendDIstDataCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly MessageSenDistDataRepository $messageSenDistDataRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new MessageSendDistDataRequest();
            $request->setAccount($account);
            $request->setBeginDate(Carbon::now()->subDays());
            $request->setEndDate(Carbon::now()->subDays());
            $response = $this->client->request($request);
            foreach ($response['list'] as $item) {
                $date = Carbon::parse($item['ref_date']);
                $MessageSenDistData = $this->messageSenDistDataRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                ]);
                if (!$MessageSenDistData) {
                    $MessageSenDistData = new MessageSenDistData();
                    $MessageSenDistData->setAccount($account);
                    $MessageSenDistData->setDate($date);
                }
                $MessageSenDistData->setCountInterval(MessageSendDataCountIntervalEnum::tryFrom($item['count_interval']));
                $MessageSenDistData->setMsgUser($item['msg_user']);
                $this->entityManager->persist($MessageSenDistData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
