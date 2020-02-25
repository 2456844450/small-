<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/4
 * Time: 13:37
 */

namespace app\api\controller\v1;

use app\api\model\Horn as HornModel;
class Horn
{
    public function getHorn()
    {
//        (new IDMustBePositiveInt())->goCheck();

        $HornInfos = HornModel::getHornInfos();
        if (!$HornInfos)
        {
            throw new ThemeException();
        }


        return $HornInfos;
    }
}