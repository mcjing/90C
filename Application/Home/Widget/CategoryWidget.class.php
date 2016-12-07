<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Widget;
use Think\Controller;

/**
 * 分类widget
 * 用于动态调用分类信息3
 */

class CategoryWidget extends Controller{
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function lists($cate, $child = false){
		$field = 'id,name,pid,title,link_id';
		$child = $cate['pid']==0? true:false;
		
		if($child){
		   
			$category = D('Category')->getTree($cate['id'], $field);
			
			$category = $category['_'];

		} else {
		    
		    $f_category = D('category')->info($cate['pid']);
		    $category = D('Category')->getTree($f_category['id'], $field);
			$category = $category['_'];
			
		}
		$this->assign('category', $category);
		$this->assign('current', $cate['id']);
		$this->display('Category/lists');
	}
	
}
