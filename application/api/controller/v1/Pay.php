<?php


namespace app\api\controller\v1;

use app\api\service\Pay as PayService;
use app\api\controller\BaseController;
use app\api\service\WxNotify as WxNotify;
use app\api\validate\IDMustBePositiveInt;
use think\Loader;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');
class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder']

    ];
    public function getPreOrder($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();


    }
    public function receiveNotify(){
//        $notify = new WxNotify();
//        $notify->Handle();
        $xmlData = file_get_contents('php://input');
        $result = curl_post_raw('https://yikexcx.cn/api/v1/pay/re_notify',$xmlData);
    }

    public function redirectNotify(){
        $config = new \WxPayConfig();
        $notify = new WxNotify();
        $notify->Handle($config, $needSign = true);
    }

    public function notifyConcurrency()
    {
        $notify = new WxNotify();
        $notify->Handle();
    }


}