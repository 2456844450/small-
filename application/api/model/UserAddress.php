<?php


namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden = ['delete_time', 'user_id'];

    public static function getSummaryByPage($page,$size){
        $pagingData = self::order('id asc')
            ->paginate($size, false, ['page' => $page]);
        return $pagingData ;
    }
}