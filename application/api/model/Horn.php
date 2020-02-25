<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/4
 * Time: 13:47
 */

namespace app\api\model;


use think\Model;

class Horn extends Model
{
    public static function getHornInfos()
    {
        $page = 1;
        $size = 30;
        $query = self::paginate(
            $size, false, ['page' => $page
        ]);
        return $query;



    }
}