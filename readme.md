### env配置
````$xslt
QINIU_ACCESSKEY=七牛云accesskey
QINIU_SECRETKEY=七牛云secretkey
QINIU_BUCKET=存储空间名字
QINIU_IMGURL=访问图片的域名(是bashUrl不是全部的url)
````

### 遇到的坑
````PHP
获取上传token时，key指定为null，然后上传图片的时候key也指定为null，文件名在获取上传token的时候通过上传策略参数saveKey指定
````