<?php

namespace WechatOfficialAccountStatsBundle\Request;

/**
 * 用户管理-关注者用户
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/User_Management/Getting_a_User_List.html
 */
class GetNoticerDataRequest extends AbstractRequest
{
    private string $requestPath;

    private string $requestMethod = 'GET';

    /**
     * @var string next_openid
     */
    private string $nextOpenid = '';

    /**
     * @var string accessToken
     */
    private string $accessToken = '';

    public function getRequestPath(): string
    {
        return $this->requestPath;
    }

    public function setRequestPath(string $requestPath): void
    {
        $this->requestPath = $requestPath;
    }

    public function getNextOpenid(): string
    {
        return $this->nextOpenid;
    }

    public function setNextOpenid(string $nextOpenid): void
    {
        $this->nextOpenid = $nextOpenid;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRequestPayload(): ?array
    {
        $result = [
            'next_openid' => $this->getNextOpenid(),
            'access_token' => $this->getAccessToken(),
        ];

        return [
            'json' => $result,
        ];
    }

    public function setRequestPayload(array $requestPayload): void
    {
        $this->requestPayload = $requestPayload;
    }

    public function getRequestMethod(): ?string
    {
        return $this->requestMethod;
    }

    public function setRequestMethod(string $requestMethod): GetAdvertisingSpaceDataRequest
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    public function getRequestOptions(): ?array
    {
        // TODO: Implement getRequestOptions() method.
    }
}
