<?php

/**
 * Created by PhpStorm.
 * User: LW
 * Date: 2017/1/11
 * Time: 10:57
 * 仓库管理
 */
class GitController extends ArController
{
    // 初始化方法
    public function init()
    {
        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => '',
                    'this' => $this
                )
            )
        );

    }

    //仓库列表
    public function showListAction()
    {
        // 搜索仓库
        $keyword = arGet('keyword');
        // 查询条件
        $condition = array('name like ' => '%' . $keyword . '%');

        $result = U_item_gitModel::model()->showList($condition);
        $this->assign(array('title' => '仓库列表'));
        $this->assign(array('keyword' => $keyword));
        $this->assign(array('gits' => $result['git']));
        $this->assign(array('totalCount' => $result['totalCount']));
        $this->assign(array('pageHtml' => $result['pageHtml']));

        $this->display();
    }

    //修改仓库信息
    public function updateGit()
    {
        $post = arRequest();

        $data = array(
            'name' => $post['name'],
            'address' => $post['address'],
            'creator' => $post['creator'],
            'description' => $post['description'],
            'i_name' => $post['i_name'],
            'content' => $post['content'],
            );
    }

    //审核仓库申请
    public function changeStatusAction()
    {
        $post = arRequest();

        $result = U_item_gitModel::model()->changeStatus($post);

        if ($result) {
            $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Git/showList', array('type' => 1))));
        }

    }

    //管理员禁用仓库
    public function removeGitAction()
    {
        $post = arRequest();


    }

    //修改仓库信息
    public function updateGitAction()
    {
        $this->display();
    }
}