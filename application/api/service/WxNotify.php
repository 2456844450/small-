<?php


namespace app\api\service;
use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Exception;
use think\Loader;
use think\Log;
use think\Db;
use app\api\model\Order;
use app\api\service\Order as OrderService;
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data, $config, &$msg)
    {
        $data=$data->values;
        if($data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try{
                $order = Order::where('order_no', '=', $orderNo)->lock(true)->find();
                if($order->status == 1){
                    $service = new OrderService();
                    $status = $service->checkOrderStock($order->id);
                    if($status['pass']){
                        $this->updateOrderStatus($order->id,true);
                        $this->reduceStock($status);
                    }
                    else{
                        $this->updateOrderStatus($order->id, false);
                    }

                }
                Db::commit();
            }catch(Exception $ex){
                Db::rollback();
                Log::error($ex);
                //如果出现异常，向微信返回false，请求重发
                return false;
            }
        }
        return true;
    }

    private function reduceStock($status){
        foreach ($status['pStatusArray'] as $singlePStatus){
            Product::where('id', '=', $singlePStatus['id'])
                ->setDec('stock', $singlePStatus['count']);
        }
    }

    private function updateOrderStatus($orderID, $success){
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        Order::where('id', '=', $orderID)
            ->update(['status' => $status]);
    }
}