<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WxController extends Controller
{

    public $app_id;
    public $app_secret;

    public function __construct()
    {
        //龙须陈密钥
        //$this->app_id     = 'wx275f6355eb3ab218';
        //$this->app_secret = 'e36f06024482c1ef6d9d5a23cd22cd49';

        //测试号信息
        $this->app_id = 'wx06116bc9687e9c65';
        $this->app_secret = '88dfc10cd4f5597ed4c0939443797460';
    }

    //test
    public function index()
    {
        dd(111);
    }

    //微信分享
    function shareWx()
    {
        $jsapi_ticket = $this->getTicket();

        //获取
        $time = time();
        $nonceStr = $this->random_code();
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REDIRECT_URL'];

        //获取signatrue
        $signature = "jsapi_ticket={$jsapi_ticket}&noncestr={$nonceStr}&timestamp={$time}&url={$url}";
        //dd($signature);
        $signature = sha1($signature);

        $app_id = $this->app_id;

        $data = compact('app_id','time', 'nonceStr', 'signature');

        return view('wx.share', $data);
    }

    public function getTicket()
    {
        $wx_ticket = session('wx_ticket');
        $expires_in = session('expires_in');
        if ($wx_ticket && $expires_in > time()) {
            return $wx_ticket;
        } else {
            $token = $this->getWxAccessToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $token . '&type=jsapi';
            $res = $this->http_curl($url);
            session([
                'wx_ticket' => $res['ticket'],
                'expires_in' => time() + 7000,
            ]);

            return $res['ticket'];
        }
    }

    /**
     * @param $url
     * @param string $type
     * @param string $data_type
     * @param string $arr
     * @return mixed
     */
    public function http_curl($url, $type = 'get', $data_type = 'json', $arr = '')
    {
        //初始化curl
        $ch = curl_init();
        //设置url参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //禁止
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        //采集
        $out_data = curl_exec($ch);

        if ($data_type == 'json') {
            if (curl_errno($ch)) {
                die(curl_error($ch));
            } else {
                return json_decode($out_data, true);
            }
        }
        //关闭curl
        curl_close($ch);
    }

    //获取微信公众号的access_token值
    public function getWxAccessToken()
    {
        //todo 从session获取token信息，判断token是否有效
        $wx_token = session('wx_token') ?? '';
        $wx_expires = session('wx_expires') ?? 0;

        //token 存在且有效，返回
        if ($wx_token && $wx_expires > time()) {
            return $wx_token;
        } else {
            $app_id = $this->app_id;
            $app_secret = $this->app_secret;

            $api_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $app_id . '&secret=' . $app_secret;
            $res = $this->http_curl($api_url);

            //todo 设置session的token值和token有效期
            session(['wx_token' => $res['access_token'], 'wx_expires' => time() + 7000]);

            return session('wx_token');
        }
    }

    public function random_code($length = 16, $chars = null)
    {
        if (empty($chars)) {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }
        $count = strlen($chars) - 1;
        $code = '';
        while (strlen($code) < $length) {
            $code .= substr($chars, rand(0, $count), 1);
        }
        return $code;
    }
}
