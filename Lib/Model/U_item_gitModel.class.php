<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author LW
 */

/**
 * Default Model of webapp.
 */
class U_item_gitModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_item_git';

    //仓库状态
    static $TYPE = array(
        0 => '停用',
        1 => '可用'
        );

    //仓库列表显示
    public function showList($condition = array())
    {
        //数据总的条数
        $totalCount = U_item_gitModel::model()->getDb()
            ->count();
        //查询数据并分页
        $page = new Page($totalCount, 2);

        $git = U_item_gitModel::model()->getDb()
            ->where($condition)
            ->limit($page->limit())
            ->queryAll();

            //根据项目id查询项目名称
            foreach ($git as $key => $value) {
                $itemInfo[$key] = U_itemsModel::model()->getInfo($value['i_id']);
                $git[$key]['i_name'] = $itemInfo[$key]['i_name'];
                $git[$key]['creator'] = U_usersModel::model()->getPublisher($value['u_id']);
                $git[$key]['online'] = self::$TYPE[$value['online']];
            }

        $pageHtml = $page->show();
        return array('git'=>$git, 'totalCount' => $totalCount, 'pageHtml' => $pageHtml);
    }

    //申请仓库
    public function applyGit($post)
    {
    	$data = array(
    		'i_id' => $post['i_id'],
    		'u_id' => $post['u_id'],
    		'name' => $post['name'],
    		'content' => $post['content'],
    		'description' => $post['description']
    		);

    	$result = U_item_gitModel::model()->getDb()
    		->insert($data);

    	arModule('Lib.Msg')->sendSystemMsg($post['u_id'], '你已经提交仓库申请'.$post['name']);
    	return $result;

    }

    //审核仓库申请
    public function changeStatus($post)
    {
        $result = U_item_gitModel::model()->getDb()
            ->where(array('id' => $post['id']))
            ->update(array('status' => $post['status']));

        if ($result) {
            return true;
        }
    }

    //用户停用仓库
    public function disable($post)
    {
        //判断该用户是否是仓库的拥有者
        $condition = array(
            'id' => $post['id'],
            'u_id' => $post['u_id']
            );

    	$result = U_item_gitModel::model()->getDb()
    		->where($condition)
    		->update(array('online' => 0));

    	arModule('Lib.Msg')->sendSystemMsg($post['u_id'], '你停用仓库成功！');

    	return $result;
    }

    //判断用户是否是仓库创建者
    public function check($post)
    {
    	$result = U_item_gitModel::model()->getDb()
    		->select('u_id')
    		->where(array('id' => $post['id']))
    		->queryRow();

    	return $result;
    }

    //查看用户仓库列表
    public function listGit($uid)
    {
        $result = U_item_gitModel::model()->getDb()
            ->select('id, name, i_id, address, description, status, online')
            ->where(array('u_id' => $uid, 'online' => array(0,1)))
            ->queryAll();

        foreach ($result as $key => $value) {
            $result[$key]['id'] = $value['id'];
            $result[$key]['name'] = $value['name'];
            $result[$key]['i_id'] = $value['i_id'];
            $result[$key]['address'] = $value['address'];
            $result[$key]['description'] = $value['description'];
            $result[$key]['status'] = $value['status'];
            $result[$key]['online'] = $value['online'];

            $result[$key]['i_name'] = U_itemsModel::model()->getInfo($value['i_id'])['i_name'];
        }

        return $result;
    }

    //查看仓库详情
    public function getGitInfo($g_id)
    {
        $result = U_item_gitModel::model()->getDb()
            ->where(array('id' => $g_id))
            ->queryRow();
        //仓库状态
        $result['online'] = self::$TYPE[$result['online']];

        return $result;

    }

    //管理员禁用仓库
    public function removeGit($gitId)
    {
        $result = U_item_gitModel::model()->getDb()
            ->where(array('id' => $gitId))
            ->update(array('online' => 2));

        return $result;
    }


}