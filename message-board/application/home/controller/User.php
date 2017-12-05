<?php
namespace app\home\controller;

use think\Controller;
use think\Db;
use think\facade\Session;

class User extends Controller
{
    /**
     * 注册表单
     */
    public function register()
    {
        return $this->fetch();
    }

    /**
     * 注册处理
     */
    public function do_register()
    {
        $username = input('username');
        $password = input('password');
        $repassword = input('repassword');
        if (empty($username))
        {
            $this->error('用户名不能为空');
        }

        if (empty($password))
        {
            $this->error('密码不能为空');
        }

        if ($password != $repassword)
        {
            $this->error('确认密码错误');
        }

        //检测用户是否已注册
        $user = Db::table('users')->where('user_name', $username)->select();
        if (!empty($user))
        {
            $this->error('用户名已存在');
        }

        $data = array(
            'user_name' => $username,
            'password' => md5($password),
            'created_at' => time()
        );
        if (!(Db::table('users')->insert($data, true) && Db::table('users')->getLastInsID()))
        {
            $this->error('注册失败！' . $model->getDbError());
        }
        $this->success('注册成功，请登录', url('login'));
    }

    /**
     * 用户登录
     */
    public function login()
    {
        return $this->fetch();
    }

    /**
     * 登录处理
     */
    public function do_login()
    {
        $username = input('username');
        $password = input('password');
        $user = Db::table('users')->where(array('user_name' => $username))->find();
        var_dump($user);
        if (empty($user) || $user['password'] != md5($password))
        {
            $this->error('账号或密码错误');
        }

        //写入session
        session('user.userId', $user['user_id']);
        session('user.username', $user['user_name']);
        //跳转首页
        $this->redirect('home/index/index');
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        if (!session('user.userId'))
        {
            $this->error('请登录');
        }
        Session::flush();
        $this->success('退出登录成功', url('login'));
    }
}