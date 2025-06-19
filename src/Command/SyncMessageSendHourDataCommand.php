<?php

namespace WechatOfficialAccountStatsBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatOfficialAccountBundle\Repository\AccountRepository;
use WechatOfficialAccountBundle\Service\OfficialAccountClient;
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;
use WechatOfficialAccountStatsBundle\Repository\MessageSendHourDataRepository;
use WechatOfficialAccountStatsBundle\Request\GetMessageSendHourDataRequest;

/**
 * 获取消息发送分时数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
 */
#[AsCronTask('1 2 * * *')]
#[AsCommand(name: self::NAME, description: '公众号-获取消息发送分时数据')]
class SyncMessageSendHourDataCommand extends Command
{
    public const NAME = 'wechat:official-account:sync-message-send-hour-data';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly OfficialAccountClient $client,
        private readonly MessageSendHourDataRepository $messageSendHourDataRepository,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $request = new GetMessageSendHourDataRequest();
            $request->setAccount($account);
            $request->setBeginDate(CarbonImmutable::now()->subDays());
            $request->setEndDate(CarbonImmutable::now()->subDays());
            $response = $this->client->request($request);
            foreach ($response['list'] as $item) {
                $date = CarbonImmutable::parse($item['ref_date']);
                $messageSendHourData = $this->messageSendHourDataRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                    'refHour' => $item['ref_hour'],
                ]);
                if ($messageSendHourData === null) {
                    $messageSendHourData = new MessageSendHourData();
                    $messageSendHourData->setAccount($account);
                    $messageSendHourData->setDate($date);
                    $messageSendHourData->setRefHour($item['ref_hour']);
                }
                $messageSendHourData->setMsgType(MessageSendDataTypeEnum::tryFrom($item['msg_type']));
                $messageSendHourData->setMsgUser($item['msg_user']);
                $messageSendHourData->setMsgCount($item['msg_count']);
                $this->entityManager->persist($messageSendHourData);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
