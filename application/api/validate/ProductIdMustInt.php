<?php


namespace app\api\validate;


class ProductIdMustInt extends BaseValidate
{
    protected $rule = [
        'productId' => 'require|isPositiveInteger',
    ];
}