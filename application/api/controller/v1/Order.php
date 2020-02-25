<?php


namespace app\api\controller\v1;
use app\api\service\Order as OrderService;
use app\api\service\Token;
use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\model\Order as OrderModel;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;
use think\Controller;

class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser'],
        'checkSuperScope' => ['only' => 'getSummary']
    ];

    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');
        $uid = Token::getCurrentUid();
        $order = new OrderService();

        $status = $order->place($uid, $products);
        return $status;
    }

    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if (!$orderDetail)
        {
            throw new OrderException();
        }
        return $orderDetail
            ->hidden(['prepay_id']);
    }

    public function getOrderDetail($orderNo){
        $orderDetail = OrderModel::getOrderDetail($orderNo);
        if(!$orderDetail)
        {
            return [
                'status' => 1,
                'msg' => '没有找到订单！'
            ];
        }
        return [
            'status' => 0,
            'data' => $orderDetail->toArray()
        ];
    }
    public function getSummaryByUser($page = 1, $size = 15)
    {
        (new PagingParameter())->goCheck();
        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
//        $collection = collection($pagingOrders->items());
//        $data = $collection->hidden(['snap_items', 'snap_address'])
//            ->toArray();
        $data = $pagingOrders->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];

    }

    public function getOrderList($pageNum,$orderNo='',$size=15){
        //传进来两个参数 一个是开始页  一个是订单编号
        $data = [];
        if(!$orderNo){
            $pagingOrders = OrderModel::getSummaryByPage($pageNum, $size);
            if ($pagingOrders->isEmpty())
            {
                return [
                    'status' => 0 ,
//                'current_page' => $pagingProducts->currentPage(),
                    'data' => $data
                ];
            }
            $data = $pagingOrders
                ->toArray();
            // 如果是简洁分页模式，直接序列化$pagingProducts这个Paginator对象会报错
            //        $pagingProducts->data = $data;
            return [
                'status' => 0,
                'data' => $data
            ];
        }
        else{
//            如果订单号存在

            $pagingOrders = OrderModel::getSummaryByOrderNo($orderNo,$pageNum, $size);
            if ($pagingOrders->isEmpty())
            {
                return [
                    'status' => 0 ,
//                'current_page' => $pagingProducts->currentPage(),
                    'data' => $data
                ];
            }
            $data = $pagingOrders
                ->toArray();
            // 如果是简洁分页模式，直接序列化$pagingProducts这个Paginator对象会报错
            //        $pagingProducts->data = $data;
            return [
                'status' => 0,
                'data' => $data
            ];
        }
        return [
            'status' => 1,
            'msg' => '没有权限'
        ];
    }

    /**
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($page=1, $size = 20){
        (new PagingParameter())->goCheck();
//        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    public function delivery($orderNo){

        $order = new OrderService();
        $data = $order->delivery($orderNo);
        return $data;
    }
}