<?php

namespace App\Http\Controllers;

use App\Components\ApiResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** 需要执行trim的参数 @var array */
    protected $trimInputs = array();

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        if (isset($this->trimInputs) && is_array($this->trimInputs)) {
            $this->trimInput($this->trimInputs);
        }
    }

    /**
     * Create the response for when a request fails validation.
     * @param Request $request
     * @param array $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if (($request->ajax() && ! $request->pjax()) || $request->wantsJson()) {
            $resp = array(442, current(current($errors)));

            return ApiResponse::buildFromArray($resp);
        }

        return redirect()->to($this->getRedirectUrl())
            ->withInput($request->input())
            ->withErrors($errors, $this->errorBag());
    }

    /**
     * 将请求数据去除空格
     * @param array $keys 指定需要去除空格的参数
     */
    protected function trimInput(array $keys)
    {
        $input = \Request::all();
        array_walk($input, function(&$value, $key) use ($keys) {
            if (is_string($value) && in_array($key, $keys)) {
                \Request::merge([$key => trim($value)]);
            }
        });
    }

    /**
     * 将请求数据中包含多个空白的替换为1个
     * @param array $keys
     */
    protected function multiSpace(array $keys)
    {
        $input = \Request::all();
        array_walk($input, function(&$value, $key) use ($keys) {
            if (is_string($value) && in_array($key, $keys)) {
                \Request::merge([$key => trim(preg_replace("/\s(?=\s)/", "\\1", $value))]);
            }
        });
    }
}
