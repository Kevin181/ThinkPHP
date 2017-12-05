<?php
namespace app\home\model;

use think\Model;

class Message extends Model
{
    protected $table = 'messages';

    // 直接使用配置参数名
    protected $connection = Db::connect('mysql://root:123456@127.0.0.1:3306/thinkphp#utf8');
}
