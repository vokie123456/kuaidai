<?php

namespace App\Components;

use Vinelab\Http\Client as HttpClient;
use Vinelab\Http\Response;

/**
 * 短信接口
 */
class Sms
{

    /** @var string */
    private $username;

    /** @var string */
    private $passwd;

    public function __construct()
    {
        $this->username = config('sms.username');
        $this->passwd = config('sms.passwd');
    }

    /**
     * 发送短信
     * @param $mobile
     * @param $content
     * @return array
     * @throws Exception
     */
    public function send($mobile, $content)
    {
        // 接入短信接口
        $params = array(
            'un' => $this->username,
            'pw' => $this->passwd,
            'phone' => $mobile,
            'msg' => $content,
            'rd' => '1',
        );

        $request = [
            'url' => 'https://sms.253.com/msg/send?' . http_build_query($params),
        ];

        $client = new HttpClient();
        /** @var Response $response */
        $response = $client->get($request);

        $contents = explode("\n", $response->content());
        @list($msgId, $status) = explode(',', $contents[0]);

        if ($status == 0 && $status !== null && $status !== '') {
            return $msgId;
        } else {
            $msg = "发送短信失败，请联系管理员【{$status}】";
            $code = ErrorCode::SYSTEM_ERROR[0];

            throw new Exception($msg, $code);
        }
    }
}