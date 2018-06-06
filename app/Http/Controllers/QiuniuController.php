<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class QiuniuController extends Controller
{
    /**
     * 获取上传token
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken()
    {
        $accessKey = env('QINIU_ACCESSKEY');
        $secretKey = env('QINIU_SECRETKEY');
        $bucket = env('QINIU_BUCKET');
        $auth = new Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($bucket);

        return $token;
    }

    public function uploadFile(Request $request)
    {
        $file = $request->file('pic');
        //检测上传的文件是否有效
        if ($file->isValid()) {
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg
            $filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $ext;

            // 要上传文件的本地路径
            $filePath =  $realPath;
            // 上传到七牛后保存的文件名
            $key = $filename;

            //拿到上传token
            $token = $this->getToken();
            $uploadMgr = new UploadManager();
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

            if ($err !== null) {
                return $err;
            } else {
                return $ret;
            }
        }
    }
}
