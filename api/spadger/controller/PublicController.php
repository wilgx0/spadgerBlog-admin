<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace api\spadger\controller;

use cmf\controller\RestBaseController;
use think\Db;
use think\Validate;
use api\portal\model\PortalPostModel;

class PublicController extends RestBaseController
{

    public function test(){
        echo 'test';
    }

    /**
     * 获取文章列表
     */
    public function get_share_list(){
        $PortalPostModel = new PortalPostModel();
        $key = input('key','');
        $where = [];
        $where['post_type'] = 1;        //文章类型:1 文章 2 页面
        $where['post_status'] = 1;      //状态： 1 已发布  2 未发布
        if(!empty($key)){
            $where['post_title|post_keywords'] = ['like','%'.$key.'%'];
        }

        $list = $PortalPostModel
            ->with('user')
            ->order('id desc')
            ->where($where)
            ->field('post_content,post_excerpt',true)
            ->paginate(10);
        if($list->isEmpty()){
            $this->error('没有可以显示的数据!');
        } else {
            $this->success('请求成功', $list);
        }
    }

}
