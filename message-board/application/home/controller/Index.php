<?php
namespace app\home\controller;

use app\home\model\Message;
use think\Controller;
use think\App;
use think\Db;

class Index extends Controller
{
    /**
     * Check login
     */
    private function checkLogin()
    {
        if (!session('user.userId'))
        {
            $this->error('请登录', url('user/login'));
        }
    }

    /**
     * Message board list
     */
    public function index()
    {
        // $model = new Message();
        // $count = $model->count();
        // $page = new Paginator($count, 10);
        // $show = $page->show();
        $list = Db::table('messages')
        ->alias('m')
        ->join('users', 'users.user_id = m.user_id')
        ->order('message_id desc')
        ->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * Publish message
     */
    public function post()
    {
        $this->checkLogin();
        return $this->fetch();
    }

    /**
     * Message processing
     */
    public function do_post()
    {
        $this->checkLogin();
        $content = input('content');
        if (empty($content))
        {
            $this->error('留言内容不能为空');
        }

        if (mb_strlen($content, 'utf-8') > 100)
        {
            $this->error('留言内容最多100字');
        }

        $userId = session('user.userId');
        $data = array(
            'content' => $content,
            'created_at' => time(),
            'user_id' => $userId
        );
        if (!(Db::table('messages')->insert($data) && Db::table('messages')->getLastInsID()))
        {
            $this->error('留言失败');
        }
        $this->success('留言成功', url('index/index'));
    }

    /**
     * Delete message
     */
    public function delete()
    {
        $id = input('id');
        if (empty($id))
        {
            $this->error('缺少参数');
        }
        $this->checkLogin();
        $result = Db::table('messages')->where(array('message_id' => $id, 'user_id' => session('user.userId')))->find();
        if (!$result)
        {
            $this->error('删除失败');
        }

        $this->success('删除成功', url('index'));
    }
}