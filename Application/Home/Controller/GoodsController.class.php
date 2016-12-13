<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;

/**
 * 快速建站控制器
 * 展示网站的模板
 */
class GoodsController extends HomeController {

	//快速建站列表页
    public function lists(){

        $category = $this->category();
        $category_list = D('category')->getChildrenId($category['id']);

        /* 获取当前分类列表 */
        $Document = D('Document');
        $list = $Document->page(1, $category['list_row'])->lists($category_list);
        if(false === $list){
            $this->error('获取列表数据失败！');
        }
        /* 模板赋值并渲染模板 */
        $this->assign('category', $category);
        $this->assign('list', $list);

        $this->display();
    }


    /* 文档分类检测 */
    private function category($id = 0){
        /* 标识正确性检测 */
        $id = $id ? $id : I('get.category', 0);
        if(empty($id)){
            $this->error('没有指定文档分类！');
        }

        /* 获取分类信息 */
        $category = D('Category')->info($id);
        if($category && 1 == $category['status']){
            switch ($category['display']) {
                case 0:
                    $this->error('该分类禁止显示！');
                    break;
                //TODO: 更多分类显示状态判断
                default:
                    return $category;
            }
        } else {
            $this->error('分类不存在或被禁用！');
        }
    }

}