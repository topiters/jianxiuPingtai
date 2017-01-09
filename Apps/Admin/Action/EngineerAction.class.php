<?php
 namespace Admin\Action;;
/**
 * ============================================================================
 * WSTMall开源商城
 * 官网地址:http://www.wstmall.net
 * 联系QQ:707563272
 * ============================================================================
 * 店铺控制器
 */
class EngineerAction extends BaseAction{
	/**
	 * 跳到新增/编辑页面
	 */
	public function toEdit(){
		$this->isLogin();
		//获取商品分类信息
		$m = D('Admin/GoodsCats');
		$this->assign('goodsCatsList',$m->queryByList());
		//获取地区信息
		$m = D('Admin/Areas');
		$this->assign('areaList',$m->queryShowByList(0));
		//获取银行列表
		$m = D('Admin/Banks');
		$this->assign('bankList',$m->queryByList(0));
		//获取商品信息
	    $m = D('Admin/Engineer');
    	$object = array();
    	if(I('id',0)>0){
    		$this->checkPrivelege('jxgcslb_02');
    		$object = $m->get();
    	}else{
    		$this->checkPrivelege('jxgcslb_01');
    		$object = $m->getModel();
    	}
    	
    	$this->assign('object',$object);
    	$this->assign('src',I('src','index'));
		$this->view->display('/engineer/edit');
	}
	
	/**
	 * 查询店铺名称是否存在
	 */
	public function checkShopName(){
		$m = D('Admin/Engineer');
		$rs = $m->checkShopName(I('supperlierName'),(int)I('id'));
		echo json_encode($rs);
	}
	
	/**
	 * 新增/修改操作
	 */
	public function edit(){
		$this->isLogin();
		$m = D('Admin/Engineer');
    	$rs = array();
    	if(I('id',0)>0){
    		$this->checkPrivelege('jxgcslb_02');
    		if(I('supperlierStatus',0)<=-1){
    			$rs = $m->reject();
    		}else{
    		    $rs = $m->edit();
    		}
    	}else{
    		$this->checkPrivelege('jxgcslb_01');
    		$rs = $m->insert();
    	}
    	$this->ajaxReturn($rs);
	}
	/**
	 * 删除操作
	 */
	public function del(){
		$this->isLogin();
		$this->checkPrivelege('jxgcslb_03');
		$m = D('Admin/Engineer');
    	$rs = $m->del();
    	$this->ajaxReturn($rs);
	}
   /**
	 * 查看
	 */
	public function toView(){
		$this->isLogin();
		$this->checkPrivelege('jxgcslb_00');
		$m = D('Admin/Engineer');
		if(I('id')>0){
			$object = $m->get();
			$this->assign('object',$object);
		}
		$this->view->display('/engineer/view');
	}
	/**
	 * 分页查询
	 */
	public function index(){
		$this->isLogin();
		$this->checkPrivelege('jxgcslb_00');
		//获取地区信息
		$m = D('Admin/Areas');
		$this->assign('areaList',$m->queryShowByList(0));
		$m = D('Admin/Engineer');
    	$page = $m->queryByPage();
    	$pager = new \Think\Page($page['total'],$page['pageSize'],I());// 实例化分页类 传入总记录数和每页显示的记录数
    	$page['pager'] = $pager->show();
    	
    	$this->assign('Page',$page);
    	$this->assign('supperlierName',I('supperlierName'));
    	$this->assign('supperlierSn',I('supperlierSn'));
    	$this->assign('areaId1',I('areaId1',0));
    	$this->assign('areaId2',I('areaId2',0));
        $this->display("/engineer/list");
	}
    /**
	 * 分页查询[待审核列表]
	 */
	public function queryPeddingByPage(){
		$this->isLogin();
		$this->checkPrivelege('jxgcssh_00');
		//获取地区信息
		$m = D('Admin/Areas');
		$this->assign('areaList',$m->queryShowByList(0));
		$m = D('Admin/Engineer');
    	$page = $m->queryPeddingByPage();
    	$pager = new \Think\Page($page['total'],$page['pageSize'],I());
    	$pager->setConfig('header','');
    	$page['pager'] = $pager->show();
    	$this->assign('Page',$page);
    	$this->assign('supperlierName',I('supperlierName'));
    	$this->assign('supperlierSn',I('supperlierSn'));
    	$this->assign('supperlierStatus',I('supperlierStatus',-999));
    	$this->assign('areaId1',I('areaId1',0));
    	$this->assign('areaId2',I('areaId2',0));
        $this->display("/engineer/list_pendding");
	}
	/**
	 * 列表查询
	 */
    public function queryByList(){
    	$this->isLogin();
		$m = D('Admin/Engineer');
		$list = $m->queryList();
		$rs = array();
		$rs['status'] = 1;
		$rs['list'] = $list;
		$this->ajaxReturn($rs);
	}
    /**
	 * 获取待审核的店铺数量
	 */
	public function queryPenddingGoodsNum(){
		$this->isLogin();
    	$m = D('Admin/Engineer');
    	$rs = $m->queryPenddingEngineerNum();
    	$this->ajaxReturn($rs);
	}
};
?>