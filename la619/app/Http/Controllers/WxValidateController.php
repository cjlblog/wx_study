<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WxValidateController extends Controller
{
    //验证微信路径
    public function wxValidate(Request $request)
    {
		Log::info("*************************\n\r");
		$timestamp = $request->get('timestamp', '');
		$nonce     = $request->get('nonce', '');
		$token     = 'lxchen';
		$signature = $request->get('signature','');
		$echo_str  = $request->get('echostr', '');

        $arr = array($timestamp, $nonce, $token);
        sort($arr, SORT_STRING);

        $tmp_str = implode($arr);
        $tmp_str = sha1($tmp_str);

		Log::info('echo_str: '. $echo_str);
        if ($tmp_str == $signature && $echo_str) {
			Log::info('验证是否来至微信');
            echo $echo_str;
			exit;
        } else {
            Log::info('关注或其它事件');
            $this->responseMsg();
        }
    }

	//事件或消息回事
    public function responseMsg()
    {
        $post_xml = file_get_contents('php://input');

        //处理数据，并转换成对象数据
        $obj = simplexml_load_string($post_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        //Log::info('[1]obj'.$obj);

        Log::info('[2]MsgType: ' . strtolower($obj->MsgType));
        Log::info('[3]Event: ' . strtolower($obj->Event));

        //todo 判断该数据包是否是“订阅”事件推送
        if (strtolower($obj->MsgType) == 'event') {
            if (strtolower($obj->Event) == 'subscribe') {
                //todo 是否是“关注”事件
                $this->setReplyTextTemp($obj, '欢迎你订阅龙须陈的test公众号');
            } else if (strtolower($obj->Event) == 'unsubscribe') {
                //todo “取消关注”事件
            }
        }


        //todo 图文消息回复
        if (strtolower($obj->MsgType) == 'text') {
            if ($obj->Content == '多图文') {
                $arr = array(
                    [
                        'title' => '你好,世界',
                        'desc' => '第一次来到这个世界',
                        'pic_url' => 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=253474560,3887450681&fm=200&gp=0.jpg',
                        'url' => 'https://www.hao123.com',
                    ],
                    [
                        'title' => '美女的内衣',
                        'desc' => '性感美女，大家都爱看',
                        'pic_url' => 'http://cjl.spcms.cn/wx/img/1.jpg',
                        'url' => 'https://www.baidu.com',
                    ],
                );
                $this->setNewsTemp($obj, $arr);
            } else if ($obj->Content == '单图文') {
                $arr = array(
                    [
                        'title' => '你好,世界',
                        'desc' => '第一次来到这个世界',
                        'pic_url' => 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=253474560,3887450681&fm=200&gp=0.jpg',
                        'url' => 'https://www.hao123.com',
                    ]
                );
                $this->setNewsTemp($obj, $arr);
            }
        }


		//todo 文本消息回复
        if (strtolower($obj->MsgType) == 'text') {
            switch ($obj->Content) {
                case 1:
                    $content = '这是1';
                    break;
                case 2:
                    $content = '这是2';
                    break;
                case 3:
                    $content = '这是3';
                    break;
                case 4:
                    $content = "<a href='https://www.baidu.com'>百度</a>";
                    break;
                case 'cjl':
                    $content = '我是陈敬良';
                    break;
            }
            if ($content) {
                $this->setReplyTextTemp($obj, $content);
            }
        }




    }


    /**
     * 生成文本回复模板
     * @param object $obj 接收的数据包
     * @param string $content 回复内容
     */
    public function setReplyTextTemp($obj, $content = '')
    {
        //回复用户信息
        $toUser = $obj->FromUserName;
        $fromUser = $obj->ToUserName;
        $time = time();
        $msgType = 'text';
        //$content = '欢迎你订阅龙须陈的公众号';

        $template = '<xml>'
            .'<ToUserName><![CDATA[%s]]></ToUserName>'
            .'<FromUserName><![CDATA[%s]]></FromUserName>'
            .'<CreateTime>%s</CreateTime>'
            .'<MsgType><![CDATA[%s]]></MsgType>'
            .'<Content><![CDATA[%s]]></Content>'
            .'</xml>';

        $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        Log::info('[5]info: '.$info);
        echo $info;
    }


	/**
     * 被回复-生成图文的模板
     * @param $obj
     * @param $arr
     */
    public function setNewsTemp($obj, $arr)
    {
        //回复用户信息
        $toUser = $obj->FromUserName;
        $fromUser = $obj->ToUserName;
        $time = time();
        $msgType = 'news';

        $temp = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <ArticleCount>" . count($arr) . "</ArticleCount>
            <Articles>";
        foreach($arr as $v){
            $temp .= "<item>
                <Title><![CDATA[".$v['title']."]]></Title> 
                <Description><![CDATA[".$v['desc']."]]></Description>
                <PicUrl><![CDATA[".$v['pic_url']."]]></PicUrl>
                <Url><![CDATA[".$v['url']."]]></Url>
            </item>";
        }
        $temp .= "</Articles></xml>";

        $info = sprintf($temp, $toUser, $fromUser, $time, $msgType);

        echo $info;
    }
}
