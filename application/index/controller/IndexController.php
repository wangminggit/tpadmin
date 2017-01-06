<?php

namespace application\index\controller;

use application\lib\utils\UtilsNet;
use think\Controller;
use think\Loader;

//use extend\utils\UtilsNet;

class IndexController extends Controller
{

    public function index()
    {
        return $this->fetch();
    }

    public function test()
    {
        //   Loader::import('@.utils.UtilsNet');
        $fetch = new UtilsNet();
        echo $fetch->fetch_alt_ip();
        //   echo UtilsNet::fetch_alt_ip();


    }

    public function test2()
    {
        echo 'test2';
    }

}
