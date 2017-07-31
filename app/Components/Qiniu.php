<?php

namespace App\Components;

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;

class Qiniu
{

    /** @var Auth */
    private $auth;

    /** @var BucketManager */
    private $bucketMgr;

    /** @var string */
    private $privateBucket;

    /** @var string */
    private $publicBucket;

    /**
     * Qiniu constructor.
     */
    public function __construct()
    {
        $ak = config('qiniu.access_key');
        $sk = config('qiniu.secret_key');

        $this->auth = new Auth($ak, $sk);
        $this->bucketMgr = new BucketManager($this->auth);
        $this->privateBucket = config('qiniu.private_bucket');
        $this->publicBucket = config('qiniu.public_bucket');
    }

    /**
     * 获取uploadToken
     * @param $callbackUrl
     * @return string
     */
    public function uploadToken($callbackUrl)
    {
        $policy = array(
            'callbackUrl' => $callbackUrl,
            'callbackBody' => json_encode(array(
                'key' => '$(key)',
                'etag' => '$(etag)',
                'ext' => '$(ext)',
                'bucket' => '$(bucket)',
                'mimeType' => '$(mimeType)',// 资源类型，例如JPG图片的资源类型为image/jpg
                'fname' => '$(fname)',// 上传的原始文件名
                'fsize' => '$(fsize)',// 资源尺寸，单位为字节
            ))
        );

        return $this->auth->uploadToken($this->privateBucket, null, 3600, $policy);
    }

    /**
     * 验证回调
     * @param $contentType
     * @param $authorization
     * @param $url
     * @param $callbackBody
     * @return bool
     */
    public function verifyCallback($contentType, $authorization, $url, $callbackBody)
    {
        if ($this->auth->verifyCallback($contentType, $authorization, $url, $callbackBody)) {
            // 验证body中4个必要的参数
            $callbackBodyHash = json_decode($callbackBody, true);
            $requireParams = array('key', 'etag', 'ext', 'bucket');
            foreach ($requireParams as $requireParam) {
                if (empty($callbackBodyHash[$requireParam])) {
                    return false;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 移到公开空间，并返回url
     * @param $callbackBodyHash
     * @return string
     */
    public function moveToPublic($callbackBodyHash)
    {
        // 组装etag+后缀
        $key = $callbackBodyHash['etag'] . $callbackBodyHash['ext'];

        // 判断在公开bucket中是否存在
        list($file) = $this->bucketMgr->stat($this->publicBucket, $key);

        if ($file === null) {
            // 移到公开环境
            $bucket = $callbackBodyHash['bucket'];
            $this->bucketMgr->move($bucket, $callbackBodyHash['key'], $this->publicBucket, $key);
        }

        return $this->getUrl($key);
    }

    /**
     * 获取文件列表
     * @return array
     */
    public function listFile()
    {
        list($files) = $this->bucketMgr->listFiles($this->publicBucket, null, null, 1000);

        return $files;
    }

    /**
     * 返回资源地址
     * @param $key
     * @return string
     */
    public function getUrl($key)
    {
        return '//' . config('qiniu.public_domain') . '/' . $key;
    }


}