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

    /**
     * 获取文章列表
     */
    public function get_share_list(){
        $key = input('key','');
        $articleType = input('articleType','');
        if(!$articleType){
            $this->error('没有文章类型参数!');
        }
        $where = [];
        $where['post_type'] = 1;        //文章类型:1 文章 2 页面
        $where['post_status'] = 1;      //状态： 1 已发布  2 未发布
        if(!empty($key)){
            $where['post_title|post_keywords'] = ['like','%'.$key.'%'];
        }
        $PortalPostModel = new PortalPostModel();
        $pageSize = [1=>10,2=>15];      //不同文章类型的分页大小 (分享10条,问答15条)
        $list = $PortalPostModel
            ->where('id','IN',function($query) use ($articleType){         //子查询
                $query->table('cmf_portal_category_post')->where('category_id',$articleType)->field('id');
            })
            ->with([            //关联查询
                'user'=>function($query){
                    $query->field('id,user_nickname');
                },
                'categories' =>function($query){}
            ])
            ->order('id desc')
            ->where($where)
            ->field('post_content,post_excerpt',true)
            ->paginate($pageSize[$articleType])
            ->each(function($value,$key){
                //$value->data('categories',$value->categories->toArray());     //获取并设置关联数据
                $value->visible(['categories']);            //增加可查询字段

            });

        if($list->isEmpty()){
            $this->error('没有可以显示的数据!');
        } else {
            $this->success('请求成功', $list);
        }
    }

    public function test(){
        $ret = Db::name('portal_post')
            ->field("(
            case post_status
                when 0 then '未审'
                when 1 then '已审'
            end
            ) 'status',count(id)")
            ->group('status')
            ->select();
        var_dump($ret);
    }
}
