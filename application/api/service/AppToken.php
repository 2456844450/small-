<?php


namespace app\api\service;


use app\api\model\ThirdApp;
use app\lib\exception\TokenException;

class AppToken extends Token
{
    public function get($ac, $se)
    {
        $app = ThirdApp::check($ac, $se);
        if(!$app)
        {
            return [
                'status' => 100,
                'msg' => "用户名或密码不存在！",
                'data' => null
            ];
        }
        else{

            $uid = $app->id;
            $username=$app->app_id;
            $password = $app->app_secret;
            $data = [

                'uid' => $uid,
                'username' => $username,
                'password' => $password
            ];

//            $token = $this->saveToCache($values);
            return [
                'status' => 0,
                'msg' => "登录成功",
                'data' => $data

            ];
        }
    }

    private function saveToCache($values){
        $token = self::generateToken();
        $expire_in = config('setting.token_expire_in');
        $result = cache($token, json_encode($values), $expire_in);
        if(!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $token;
    }
}