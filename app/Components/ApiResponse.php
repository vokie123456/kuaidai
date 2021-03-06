<?php

namespace App\Components;

use Illuminate\Http\JsonResponse;

/**
 * Api响应
 */
class ApiResponse extends JsonResponse
{

    /**
     * ApiResponse constructor.
     * @param mixed|null $data
     * @param int $code
     * @param string $msg
     */
    public function __construct($data, $code = 0, $msg = '')
    {
        $resp = array(
            'data' => $data,
            'code' => $code,
            'msg' => $msg,
        );

        parent::__construct($resp, 200, [], 0);
    }

    /**
     * 通过数组构建
     * @param array $resp
     * [$code, $msg, $data]
     * @return ApiResponse
     */
    public static function buildFromArray(array $resp = [0, 'ok'])
    {
        list($code, $msg) = $resp;

        $data = isset($resp[2]) ? $resp[2] : null;

        return new self($data, $code, $msg);
    }

    /**
     * 构建成功数据
     * @param array $data
     * @param int $code
     * @param string $msg
     * @return ApiResponse
     */
    public static function buildSuccess(array $data, $code = 0, $msg = 'ok')
    {
        return new self($data, $code, $msg);
    }

    /**
     * 构建失败数据
     * @param $code
     * @param $msg
     * @return ApiResponse
     */
    public static function buildFail($code, $msg)
    {
        return new self(null, $code, $msg);
    }

}