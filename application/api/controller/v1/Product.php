<?php


namespace app\api\controller\v1;


use app\api\model\BaseCount;
use app\api\model\Image;
use app\api\model\Product as ProductModel;
use app\api\model\Category as CategoryModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PagingParameter;
use app\api\validate\ProductIdMustInt;
use app\lib\exception\ParameterException;
use app\lib\exception\ProductException;
use app\lib\exception\ThemeException;
use think\Controller;

class Product extends Controller
{
    protected $beforeActionList = [
        'checkSuperScope' => ['only' => 'deleteOne']
    ];

    /**
     * 根据类目ID获取该类目下所有商品(分页）
     * @url /product?id=:category_id&page=:page&size=:page_size
     * @param int $id 商品id
     * @param int $page 分页页数（可选)
     * @param int $size 每页数目(可选)
     * @return array of Product
     * @throws ParameterException
     */
    public function getByCategory($id = -1, $pageNum = 1, $size = 10)
    {
//        (new IDMustBePositiveInt())->goCheck();
        (new PagingParameter())->goCheck();
        $pagingProducts = ProductModel::getProductsByCategoryID(
            $id, true, $pageNum, $size);
        $data = [];
        if ($pagingProducts->isEmpty())
        {
            // 对于分页最好不要抛出MissException，客户端并不好处理
            return [
                'status' => 0 ,
//                'current_page' => $pagingProducts->currentPage(),
                'data' => $data
            ];
        }
        //数据集对象和普通的二维数组在使用上的一个最大的区别就是数据是否为空的判断，
        //二维数组的数据集判断数据为空直接使用empty
        //collection的判空使用 $collection->isEmpty()

        // 控制器很重的一个作用是修剪返回到客户端的结果

        //        $t = collection($products);
        //        $cutProducts = collection($products)
        //            ->visible(['id', 'name', 'img'])
        //            ->toArray();

//        $collection = collection($pagingProducts->items());
        $data = $pagingProducts
            ->toArray();
        // 如果是简洁分页模式，直接序列化$pagingProducts这个Paginator对象会报错
        //        $pagingProducts->data = $data;
        return [
            'status' => 0,
            'data' => $data
        ];
    }

    /**
     * 获取某分类下全部商品(不分页）
     * @url /product/all?id=:category_id
     * @param int $id 分类id号
     * @return \think\Paginator
     * @throws ThemeException
     */
    public function getAllInCategory($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID(
            $id, false);
        if (!$products)
        {
            throw new ThemeException();
        }
        $data = $products;

        return $data;
    }

    /**
     * 获取指定数量的最近商品
     * @url /product/recent?count=:count
     * @param int $count
     * @return mixed
     * @throws ParameterException
     */
    public function getRecent($count = 15)
    {
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);
        if (!$products)
        {
            throw new ProductException();
        }
      /*  $products = $products->hidden(
            [
                'summary'
            ])->toArray()
            ;*/
        return $products;
    }

    /**
     * 获取商品详情
     * 如果商品详情信息很多，需要考虑分多个接口分布加载
     * @url /product/:id
     * @param int $id 商品id号
     * @return Product
     * @throws ProductException
     */
    public function getOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if (!$product)
        {
            throw new ProductException();
        }
        return $product;
    }

    public function getOneProduct($id){
        $product = ProductModel::getOneProduct($id);
        if (!$product)
        {
            throw new ProductException();
        }
        return $product;
    }

    public function createOne($name,$subtitle,$categoryId,$subImages,$price,$stock,$status)
    {
        $categoryId1 = intval($categoryId);
        $price1 = floatval($price);
        $stock1 = intval($stock);
        $status1 = intval($status);
        $data = [
            'name' => $name,
            'summary' => $subtitle,
            'category_id' => $categoryId1,
            'main_img_url' => $subImages,
            'price' => $price1,
            'stock' => $stock1,
            'from' => $status1,
            'img_id' => 0

        ];
        $imageData = [

            'url' => $data['main_img_url'],
            'from' => $data['from']
        ];
        $productName = ProductModel::get(['name' => $data['name']]);
        if($productName){

            //如果产品存在 删除掉产品对应的图片信息 图片本身要删 数据库里的图片信息也要删
            $imageId = ProductModel::get(['name' => $data['name']])->img_id;

            $url = Image::get(['id' => $imageId])->url;
            $out = explode('images', $url);


            $fileName = ROOT_PATH . 'public' . DS . 'images' . $out[1];
            if(file_exists($fileName)){
                unlink($fileName); //删除了图片

            }
            $image = new Image();
            Image::destroy($imageId); //删除了数据库中的图片
            ProductModel::update($data,['name' => $productName->name]);
            $image->save($imageData); //增加数据库中的图片
            $img_id = Image::get(['url' => $imageData['url']])->id;
            ProductModel::update(['img_id' => $img_id],['name' => $data['name']]); //更新产品与图片的关系

            return [
                'status' => 0,
                'data' => '更新产品成功！'
            ];
        }
        else{
            $product = new ProductModel();
            $product->save($data);
            $image = new Image();
            $image->save($imageData);
            $img_id = Image::get(['url' => $imageData['url']])->id;
            ProductModel::update(['img_id' => $img_id],['name' => $data['name']]);
            $count = BaseCount::get(['name' => 'productCount' ])->count;
            $count ++;
            BaseCount::update(['count' => $count],['name'=>'productCount']);

            return [
                'status' => 0,
                'data' => '新增产品成功！'
            ];
        }
        return [
            'status' => 1,
            'data' => '操作失败！'
        ];




    }

    public function deleteOne($id)
    {
        ProductModel::destroy($id);
        //        ProductModel::destroy(1,true);
    }

    public function setSaleStatus($id,$status){
        (new IDMustBePositiveInt())->goCheck();
        $from = ProductModel::get(['id' => $id])->from;
        if (!$from)
        {
            return [
                'status'=> 1,
                'data'=> '修改产品状态失败'
            ];
        }

        if($status != $from)
        {
            ProductModel::where('id',$id)->update([
                'from' => $status
            ]);
            return [
                'status'=> 0,
                'data'=> '修改产品状态成功！'
            ];
        }
        else{
            return [
                'status'=> 1,
                'data'=> '修改产品状态失败'
            ];
        }


    }

    public function searchProduct($productId='',$productName='',$pageNum=1,$size=30){

        $data = [];
        if($productId != null){
            $query = ProductModel::getProductsByProductId($productId,$pageNum, $size);
            if($query->isEmpty())
            {
                return [
                    'status' => 0,
                    'data' => '您要的商品没找到，请仔细检查输入的内容是否正确。'
                ];
            }

            $data = $query -> toArray();

            return [
                'status' => 0,
                'data' => $data
            ];




        }

        if($productName != null){
            $query = ProductModel::getProductsByProductName($productName,$pageNum, $size);

            if($query->isEmpty())
            {
                return [
                    'status' => 10,
                    'data' => '您要的商品没找到，请仔细检查输入的内容是否正确。'
                ];
            }

            $data = $query -> toArray();

            return [
                'status' => 0,
                'data' => $data
            ];
        }


    }

    public function addCategory($categoryName,$subImages){
        $data = [
            'name' => $categoryName,

        ];
        $imageData = [
            'url' => $subImages,
            'from' => 1
        ];
        $query = CategoryModel::get(['name' => $data['name']]);
        if($query){
            return [
                'status' => 0,
                'msg' => '产品分类已存在，请重新输入！'
            ];
        }
        else{
            $category = new CategoryModel();
            $category->save($data);
            $image = new Image();
            $image->save($imageData);
            $img_id = Image::get(['url' => $imageData['url']])->id;
            CategoryModel::update(['topic_img_id' => $img_id],['name' => $data['name']]);

            return [
                'status' => 0,
                'msg' => '新增产品分类成功！'
            ];
        }

    }

    public function upload(){
        $file = request()->file('upload_file');


        $info = $file->validate(['ext'=>'jpg,png,gif']) -> move(ROOT_PATH . 'public' . DS . 'images');
        $imageName = $info->getSaveName();
        $imageName = str_replace('\\','/',$imageName);
        if($info){
//            $filePath = DS . $info->getSaveName();
//            $data = [
//                'url' => $filePath,
////                'create_time' => date('Y-m-d H:i:s')
//            ];
//            Image::insert($data);
            return [
                'status' => 0,
                'data' => [
                    'uri' => $imageName,
                    'url' => 'http://z.cn/images'.$imageName
                ]
            ];
        }
        else{
            return [
              'status' => 1,
                'msg' => '图片上传失败！'
            ];
        }
    }

    public function deleteFile($url){
        $fileName = ROOT_PATH . 'public' . DS . 'images' . DS . $url;
        if(file_exists($fileName)){
            unlink($fileName);
            return [
                'status' => 0,
                'msg' => '删除图片成功！'
            ];
        }
        else{
            return [
                'status' => 0,
                'msg' => '删除图片失败!'
            ];
        }
    }

    public function richtextImgUpload(){
        $file = request()->file('upload_file');
        $url = $file->getInfo();
        $fileName = ROOT_PATH . 'public' . DS . 'images' . DS . $url['name'];
        if(file_exists($fileName)){
            return [
                'success' => false,
                'msg' =>  '图片名称已存在，请重新定义',
                'file_path' =>  '[real file path]'
            ];
        }

        $info = $file->validate(['ext'=>'jpg,png,gif']) -> move(ROOT_PATH . 'public' . DS . 'images');
        if($info){

            return [
                'success' => true,
                'msg' =>  '上传成功！',
                'file_path' =>  'http://z.cn/images/'.$info->getSaveName()

            ];
        }
        else{
            return [
                'success' => false,
                'msg' =>  '上传失败！',
                'file_path' =>  '[real file path]'
            ];
        }
    }
}