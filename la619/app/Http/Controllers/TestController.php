<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function index()
    {
        set_time_limit(0);
        //获取超时时间
        $t = ini_get("max_execution_time");
        echo $t;
        //暂停60秒
        sleep(60);
        echo '我暂停了60秒';
        exit;
    }
}
