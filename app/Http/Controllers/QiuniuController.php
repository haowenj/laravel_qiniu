<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class QiuniuController extends Controller
{
    /**
     * 获取上传token
     * @return \Illuminate\Http\JsonResponse
     */
    private function getToken($key)
    {
        $accessKey = env('QINIU_ACCESSKEY');
        $secretKey = env('QINIU_SECRETKEY');
        $bucket = env('QINIU_BUCKET');
        $policy = array(
            'callbackUrl' => env('APP_URL') . '/qiniuCallback',
            'callbackBody' => 'fkey=$(key)',
            'saveKey' => $key
        );
        $auth = new Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($bucket, null, '', $policy);
        return $token;
    }

    /**
     * 上传图片
     * @param Request $request
     * @return mixed
     */
    public function uploadFile(Request $request)
    {
        $file = $request->file('pic');
        //检测上传的文件是否有效
        if ($file->isValid()) {
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg
            $filename = date('YmdHis') . '' . uniqid() . '.' . $ext;

            // 要上传文件的本地路径
            $filePath =  $realPath;
            // 上传到七牛后保存的文件名
            $key = $filename;

            //拿到上传token
            $token = $this->getToken($key);
            $uploadMgr = new UploadManager();
            list($ret, $err) = $uploadMgr->putFile($token, null, $filePath);

            if ($err !== null) {
                return $err;
            } else {
                return $ret;
            }
        }
    }

    public function qiniuCallback(Request $request)
    {
        $filename = $request->input('fkey');
        Image::create(compact('filename'));
        $url = env('QINIU_IMGURL') . $filename;
        return $url;
    }
}
