<?php
namespace app\home\controller;

use app\home\model\Message;
use think\Controller;
use think\App;
use think\Paginator;

class Index extends Controller
{
    /**
     * Check login
     */
    private function checkLogin()
    {
        if (!session('user.userId'))
        {
            $this->error('请登录', url('User/login'));
        }
    }

    /**
     * Message board list
     */
    public function index()
    {
        $model = new Message();
        $count = $model->count();
        $page = new Paginator($count, 10);
        $show = $page->show();
        $list = $model->order('message_id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * Publish message
     */
    public function post()
    {
        $this->checkLogin();
        $this->display();
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

        $model = App::model('Message');
        $userId = session('user.userId');
        $data = array(
            'content' => $content,
            'created_at' => time(),
            'user_id' => $userId
        );
        if (!($model->create($data) && $model->add()))
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
        $model = App::model('Message');
        $result = $model->where(array('message_id' => $id, 'user_id' => session('user.userId')))->find();
        if (!$result)
        {
            $this->error('删除失败');
        }

        $this->success('删除成功', url('index'));
    }
}