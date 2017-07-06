<?php
namespace app\repair\controller;
use think\Controller;
class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }
}
