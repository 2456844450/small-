<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//Banner
use think\Route;

Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');  //已检验
//horn
Route::get('api/:version/horn', 'api/:version.Horn/getHorn');


//Theme
// 如果要使用分组路由，建议使用闭包的方式，数组的方式不允许有同名的key
//Route::group('api/:version/theme',[
//    '' => ['api/:version.Theme/getThemes'],
//    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct'],
//    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct']
//]);

Route::group('api/:version/theme',function(){
    Route::get('', 'api/:version.Theme/getSimpleList'); //已检验
    Route::get('/:id', 'api/:version.Theme/getComplexOne'); //已检验
    Route::post(':t_id/product/:p_id', 'api/:version.Theme/addThemeProduct');
    Route::delete(':t_id/product/:p_id', 'api/:version.Theme/deleteThemeProduct');
});
Route::group('api/:version/manage',function(){
    Route::post('user', 'api/:version.Manage/getUserList'); //已检验

});

//统计base_count

Route::get('api/:version/statistics', 'api/:version.Statistics/getCount');

//Token
Route::post('api/:version/token/user', 'api/:version.Token/getToken'); //已验证

Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

//Product
Route::post('api/:version/product/save', 'api/:version.Product/createOne');
Route::post('api/:version/product/search', 'api/:version.Product/searchProduct');
Route::post('api/:version/product/set_sale_status', 'api/:version.Product/setSaleStatus');
Route::delete('api/:version/product/:id', 'api/:version.Product/deleteOne');
Route::post('api/:version/product/add_category', 'api/:version.Product/addCategory');
Route::post('api/:version/product/by_category/paginate', 'api/:version.Product/getByCategory');
Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id', 'api/:version.Product/getOne',[],['id'=>'\d+']);  //已检验
Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');  //已检验
Route::post('api/:version/product/upload', 'api/:version.Product/upload');
Route::post('api/:version/product/deletefile', 'api/:version.Product/deleteFile');
Route::post('api/:version/product/richtext_img_upload', 'api/:version.Product/richtextImgUpload');
Route::post('api/:version/product/detail', 'api/:version.Product/getOneProduct');  //已检验



//Category
Route::get('api/:version/category', 'api/:version.Category/getCategory'); //已检验
Route::get('api/:version/category/getall', 'api/:version.Category/getCategories'); //已检验

// 正则匹配区别id和all，注意d后面的+号，没有+号将只能匹配个位数
//Route::get('api/:version/category/:id', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
//Route::get('api/:version/category/:id/products', 'api/:version.Category/getCategory',[], ['id'=>'\d+']);
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories'); //已检验
Route::post('api/:version/category/update_category', 'api/:version.category/updateCategory');
//Address
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address', 'api/:version.Address/getUserAddress');


//Order
Route::post('api/:version/order', 'api/:version.Order/placeOrder');
Route::get('api/:version/order/:id', 'api/:version.Order/getDetail',[], ['id'=>'\d+']);
Route::post('api/:version/order/delivery', 'api/:version.Order/delivery');
Route::post('api/:version/order/list', 'api/:version.Order/getOrderList');
Route::post('api/:version/order/detail', 'api/:version.Order/getOrderDetail');
//不想把所有查询都写在一起，所以增加by_user，很好的REST与RESTFul的区别
Route::get('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');
Route::get('api/:version/order/paginate', 'api/:version.Order/getSummary');
//Pay
Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
Route::post('api/:version/pay/re_notify', 'api/:version.Pay/redirectNotify');
Route::post('api/:version/pay/concurrency', 'api/:version.Pay/notifyConcurrency');
//Message
Route::post('api/:version/message/delivery', 'api/:version.Message/sendDeliveryMsg');
//