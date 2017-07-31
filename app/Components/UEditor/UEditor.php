<?php

namespace App\Components\UEditor;
use App\Components\Qiniu;

/**
 * UE组件
 */
class UEditor
{

    private $config = array();

    /** @var \Illuminate\Http\Request */
    private $request;

    /**
     * UEditor constructor.
     * @param \Illuminate\Http\Request $request
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->config = config('ueditor');
    }

    /**
     * 调用Action
     * @param $action
     * @param $callback
     * @return mixed|string
     */
    public function callAction($action, $callback)
    {
        switch ($action) {
                // 获取配置
            case 'config':
                $result =  json_encode($this->config);
                break;

                // 上传图片
//            case 'uploadimage':
                // 上传涂鸦
//            case 'uploadscrawl':
                // 上传视频
//            case 'uploadvideo':
                // 上传文件
//            case 'uploadfile':
                // UE中已将文件上传改为直传七牛，不需要经过服务器
//                $result = array('error' => '暂不支持上传');
//                break;

                // 列出图片
            case 'listimage':
                // 列出文件
//            case 'listfile':
                $result = $this->imageList();
                break;

                // 抓取远程文件
//            case 'catchimage':
//                $result = $this->actionCrawler($action);
//                break;

            default:
                $result = json_encode(array(
                    'state'=> '暂不支持该功能'
                ));
                break;
        }

        if ($callback) {
            if (preg_match("/^[\w_]+$/", $callback)) {
                return htmlspecialchars($callback) . '(' . $result . ')';
            } else {
                return json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            return $result;
        }
    }

    /**
     * 获取图片列表
     * @return string
     */
    private function imageList()
    {
//        $size = $this->request->input('size', $this->config['imageManagerListSize']);
        $start = $this->request->input('start', 0);
//        $end = $start + $size;

        // 这里只会返回一页数据，共1000条，若$start大于0时，不返回空
        if ($start > 0) {
            return json_encode(array(
                'state' => '没有更多文件了。',
                'list' => array(),
                'start' => $start,
                'total' => 0
            ));
        }

        $qiniu = new Qiniu();
        $files = $qiniu->listFile();
        $list = array();

        foreach ($files as $file) {
            $list[] = array(
                'url' => $qiniu->getUrl($file['key'])
            );
        }

        return json_encode(array(
            'state' => 'SUCCESS',
            'list' => $list,
            'start' => $start,
            'total' => count($list)
        ));
    }
}