<?php

namespace App\Http\Controllers\Callback;

use App\Components\Qiniu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QiniuController extends Controller
{


    /**
     * UEditor上传回调
     * @param Request $request
     * @param Qiniu $qiniu
     * @return array
     */
    public function ueditor(Request $request, Qiniu $qiniu)
    {
        $callbackBody = $request->getContent();
        $contentType = $request->server('HTTP_CONTENT_TYPE');
        $authorization = $request->server('HTTP_AUTHORIZATION');
        $url = config('qiniu.callback_ueditor');

        $callbackBodyHash = json_decode($callbackBody, true);

        if (is_array($callbackBodyHash) && $qiniu->verifyCallback($contentType, $authorization, $url, $callbackBody)) {
            $url = $qiniu->moveToPublic($callbackBodyHash);

            return array(
                'state' => 'SUCCESS',
                'url' => $url,
                'title' => $url,
                'original' => data_get($callbackBodyHash, 'fname'),
                'type' => data_get($callbackBodyHash, 'mimeType'),
                'size' => data_get($callbackBodyHash, 'fsize'),
            );
        } else {
            return array('state' => 'FAILURE');
        }
    }

}