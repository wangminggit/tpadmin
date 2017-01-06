<?php
/**
 * Created by PhpStorm.
 * User: wm
 * Date: 2017/1/5
 * Time: 15:27
 */
namespace application\admin\controller;
class NewsController extends CommonController
{
    public function index()
    {
        return $this->fetch();
    }
}
