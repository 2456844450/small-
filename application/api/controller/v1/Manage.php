<?php


namespace app\api\controller\v1;


use app\api\model\UserAddress as UserModel;
use app\api\validate\PagingParameter;
use think\Controller;

class Manage extends Controller
{
    public function getUserList($page=1, $size = 10){
        (new PagingParameter())->goCheck();
        $pagingUsers = UserModel::getSummaryByPage($page, $size);
        $data = [];
        if ($pagingUsers->isEmpty())
        {
            return [
                'status' => 0,
//                'current_page' => $pagingOrders->currentPage(),
                'data' => $data
            ];
        }
        $data = $pagingUsers->toArray();
        return [
            'status' => 0 ,
            'data' => $data
        ];
    }
}