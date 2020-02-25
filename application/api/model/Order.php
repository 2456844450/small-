<?php


namespace app\api\model;


use app\api\model\Order as OrderModel;
use app\api\service\DeliveryMessage;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Exception;

class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    public function getSnapItemsAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode(($value));
    }

    public static function getSummaryByUser($uid, $page=1, $size=15)
    {
        $pagingData = self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }

    public static function getSummaryByOrderNo($orderNo,$page=1,$size=15){
        $pagingData = self::where('order_no', '=', $orderNo)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }

    public static function getSummaryByPage($page=1, $size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }
    public static function getOrderDetail($orderNo){
        $id = self::get(['order_no' => $orderNo])->id;
        $order = self::with('products')
            ->with('useraddress')
            ->find($id);
        return $order;
    }
    public function products()
    {
        return $this->belongsToMany('Product', 'order_product', 'product_id', 'order_id');
    }

    public function useraddress(){
        return $this->belongsTo('UserAddress','user_id','id');
    }
}