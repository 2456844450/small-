<?php


namespace app\api\controller\v1;


use app\api\model\BaseCount as CountModel;
use think\Controller;

class Statistics extends Controller
{
    public function getCount(){
        $userCount = CountModel::get(['name' => 'userCount'])->count;

        $orderCount = CountModel::get(['name' => 'orderCount'])->count;
        $productCount = CountModel::get(['name' => 'productCount'])->count;
        $data = [
            'userCount' => $userCount,

            'productCount' => $productCount,
            'orderCount' => $orderCount,

        ];
        return [
            'status' => 0,
            'data' => $data
        ];

    }
}