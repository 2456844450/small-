<?php


namespace app\api\model;


class Product extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';
    protected $hidden = [
        'delete_time', 'main_img_id', 'pivot', 'category_id',
        'create_time', 'update_time', 'summary'];

    /**
     * 图片属性
     */
    public function imgs()
    {
        return $this->hasMany('ProductImage', 'product_id', 'id');
    }

    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }


    public function properties()
    {
        return $this->hasMany('ProductProperty', 'product_id', 'id');
    }

    /**
     * 获取某分类下商品
     * @param $categoryID
     * @param int $page
     * @param int $size
     * @param bool $paginate
     * @return \think\Paginator
     */
    public static function getProductsByCategoryID(
        $categoryID, $paginate = true, $page = 1, $size = 30)
    {
        if($categoryID == -1){
            $query = self::order('id asc')->paginate(
                $size, false, [
                'page' => $page
            ]);
            return $query;
        }
        $query = self::
        where('category_id', '=', $categoryID)
            ->where('from','=',1);
        if (!$paginate)
        {
            return $query->select();
        }
        else
        {
            // paginate 第二参数true表示采用简洁模式，简洁模式不需要查询记录总数
            return $query->paginate(
                $size, false, [
                'page' => $page
            ]);
        }
    }

    /**
     * 获取商品详情
     * @param $id
     * @return null | Product
     */
    public static function getProductDetail($id)
    {
        //千万不能在with中加空格,否则你会崩溃的
        //        $product = self::with(['imgs' => function($query){
        //               $query->order('index','asc');
        //            }])
        //            ->with('properties,imgs.imgUrl')
        //            ->find($id);
        //        return $product;

        $product = self::with(
            [
                'imgs' => function ($query)
                {
                    $query->with(['imgUrl'])
                        ->order('order', 'asc');
                }])
            ->with('properties')
            ->find($id);
        return $product;
    }
    public static function getOneProduct($id){
        $product = self::find($id);
        $url = $product['main_img_url'];
        $out = explode('images', $url);
        //产品信息包括id name price stock category_id main_img_url from create_time update_time summary img_id
        $data = [
            'id' => $product['id'],
            'categoryId' => $product['category_id'],
            'name' => $product['name'],
            'subtitle' => $product['summary'],
            'imageHost' => 'http://z.cn/images',
            'mainImage' => $out[1],
            'subImages' => [
                $out[1]


            ],
            'price' => $product['price'],
            'stock' => $product['stock'],
            'status' => $product['from'],
            'createTime' => $product['create_time'],
            'updateTime' => $product['update_time']
        ];
        return [
            'status' => 0,
            'data' => $data
        ];
    }

    public static function getMostRecent($count)
    {
        $products = self::where('from','=','1')->limit($count)
            ->order('create_time desc')
            ->select();
        return $products;
    }

    public static function getProductsByProductId(
        $productId,$page = 1, $size = 30)
    {

        $query = self::where('id','=',$productId)->paginate(
            $size, false, ['page' => $page
        ]);
        return $query;



    }

    public static function getProductsByProductName(
        $productName,$page = 1, $size = 30)
    {

        $query = self::where('name','like',$productName.'%')->
        whereOr(function ($query) use ($productName){
            $query->where('name','like','%'.$productName.'%');
        })->
        whereOr(function ($query) use ($productName){
            $query->where('name','like','%'.$productName);
        })->
        paginate(
            $size, false, ['page' => $page
        ]);

        return $query;



    }
}