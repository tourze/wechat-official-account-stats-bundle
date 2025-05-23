<?php

namespace WechatOfficialAccountStatsBundle\Request;

use Carbon\CarbonInterface;
use WechatOfficialAccountBundle\Request\WithAccountRequest;

/**
 * 消息分析-获取消息发送分布数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Message_analysis_data_interface.html
 */
class MessageSendDistDataRequest extends WithAccountRequest
{
    /**
     * @var CarbonInterface 获取数据的起始日期，begin_date和end_date的差值需小于“最大时间跨度”（比如最大时间跨度为1时，begin_date和end_date的差值只能为0，才能小于1），否则会报错
     */
    private CarbonInterface $beginDate;

    /**
     * @var CarbonInterface 获取数据的结束日期，end_date允许设置的最大值为昨日
     */
    private CarbonInterface $endDate;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/datacube/getupstreammsgdist';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'begin_date' => $this->getBeginDate()->format('Y-m-d'),
            'end_date' => $this->getEndDate()->format('Y-m-d'),
        ];

        return [
            'json' => $json,
        ];
    }

    public function getBeginDate(): CarbonInterface
    {
        return $this->beginDate;
    }

    public function setBeginDate(CarbonInterface $beginDate): void
    {
        $this->beginDate = $beginDate;
    }

    public function getEndDate(): CarbonInterface
    {
        return $this->endDate;
    }

    public function setEndDate(CarbonInterface $endDate): void
    {
        $this->endDate = $endDate;
    }
}
