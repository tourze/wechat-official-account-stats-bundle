<?php

namespace WechatOfficialAccountStatsBundle\Request;

use WechatOfficialAccountBundle\Request\WithAccountRequest;

/**
 * 广告分析-分广告位数据
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/Ad_Analysis.html
 */
class GetAdvertisingSpaceDataRequest extends WithAccountRequest
{
    /**
     * @var string action
     */
    private string $action = '';

    /**
     * @var string page
     */
    private string $page = '';

    /**
     * @var string pageSize
     */
    private string $pageSize = '';

    /**
     * @var string startDate
     */
    private string $startDate = '';

    /**
     * @var string endDate
     */
    private string $endDate = '';

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/publisher/stat';
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'action' => $this->getAction(),
                'page' => $this->getPage(),
                'page_size' => $this->getPageSize(),
                'start_date' => $this->getStartDate(),
                'end_date' => $this->getEndDate(),
            ],
        ];
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function setPage(string $page): void
    {
        $this->page = $page;
    }

    public function getPageSize(): string
    {
        return $this->pageSize;
    }

    public function setPageSize(string $pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function setEndDate(string $endDate): void
    {
        $this->endDate = $endDate;
    }
}
