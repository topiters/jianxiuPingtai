<?php
 namespace Api\Action;;
/**
*  产品控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月17日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
class GoodsCatsAction extends BaseAction{
	/**
	 * 分类列表查询
	 */
	public function  getCat(){
		
		$goodscats=D('Api/GoodsCats');
		$res=$goodscats->cate();
		if($res){
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$res);
			$this->stringify($data);
		}else{
			$data['msg']="暂时没有符合的数据...";
			$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$data);
			$this->stringify($data);
			
			
			
		}
			
		
	
	}
	
	
	
	public function  getType(){
	
		$goodscats=D('Api/GoodsCats');
		$res=$goodscats->getType();
		if($res){
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$res);
			$this->stringify($data);
		}else{
			$data['msg']="暂时没有符合的数据...";
			$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$data);
			$this->stringify($data);
				
				
				
		}
			
	
	}
	
	
	
	
	
	
	
    public function queryByList(){
		$m = D('Admin/GoodsCats');
		$list = $m->queryByList((int)I('id'));
		$rs = array();
		$rs['status'] = 1;
		$rs['list'] = $list;
		$this->ajaxReturn($rs);
	}
};
?>