<?php
namespace Api\Action;
/**
*  商品控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月3日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
class GoodsAction extends BaseAction {
	
	
	/**
	 * 推荐任务
	 * 
	 * 
	 */
	public function getGoodsIndex(){
		 $result=D('Api/goods')->getGoodsIndex();
		 if($result){
		 	//$data["msg"] = '数据载入成功!';
		 	$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$result);
		 	$this->stringify($data);
		 	
		 	
		 }else{
		 	
		 	$data["msg"] = '数据暂时没有请求的数据!';
		 	$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$data);
		 	$this->stringify($data);
		 	
		 }
		
	}
	
	//推荐任务详情
	
	
	public function getGoodsIndexDetails(){
		$result=D('Api/goods')->getGoodsDetails();
		if($result){
			//$data["msg"] = '数据载入成功!';
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$result);
			$this->stringify($data);
	
	
		}else{
	
			$data["msg"] = '数据暂时没有请求的数据!';
			$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$data);
			$this->stringify($data);
	
		}
	
	}
	
//采购任务
	public function gooodsPurchase(){
		$goodsModel=D('Api/goods');
		$result=$goodsModel->gooodsPurchase();
		if($result){
			//$data["msg"] = '数据载入成功!';
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$result);
			$this->stringify($data);
		}else{
		
			$data["msg"] = '数据暂时没有请求的数据!';
			$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$data);
			$this->stringify($data);
		
		}
		
	}  
	
	
	//采购详情
    public function gooodsPurchaseDetails() {
        $goodsModel = D('Api/goods');
        $result = $goodsModel->gooodsPurchaseDetails();
        if ($result) {
            $record = D('Supplier')->offerRecord();
            if ($record) {
                $result[0]['record'] = $record;
            }
//                dump($result);die;
            $data = array('status' => self::API_REQUEST_SUCCESS , 'msg' => $result);
            $this->stringify($data);
        } else {
            $data["msg"] = '数据暂时没有请求的数据!';
            $data = array('status' => self::API_DATA_NOT_EXISTS , 'msg' => $data);
            $this->stringify($data);
        }
    }
	
	
	
	
    public function getGoodsList(){
   		$mgoods = D('Api/Goods');
   		
   		$result=$mgoods->getGoodsList();
    if($result['total']){
			//$data["msg"] = '数据载入成功!';
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$result);
			$this->stringify($data);
	
	
		}else{
	
			$data["msg"] = '数据暂时没有请求的数据!';
			$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$data);
			$this->stringify($data);
	
		}
   		
   	
    }
    
  
	/**
	 * 查询商品详情
	 * 
	 */
	public function getGoodsDetails(){

		$goods = D('Api/Goods');

		//查询商品详情		
		$goodsId = (int)I("goodsId");
		$obj["goodsId"] = $goodsId;	
	
		
		$goodsDetails = $goods->getGoodsDetails($obj);
		
			
	   $goodsDetails['goodsDesc'] = htmlspecialchars_decode($goodsDetails['goodsDesc']);
		 if($goodsDetails){
			//$data["msg"] = '数据载入成功!';
			$data = array('status'=>self::API_REQUEST_SUCCESS,'msg'=>$goodsDetails);
			$this->stringify($data);
	
	
		}else{
	
			$data["msg"] = '数据暂时没有请求的数据!';
			$data = array('status'=>self::API_DATA_NOT_EXISTS,'msg'=>$goodsDetails);
			$this->stringify($data);
	
		}	
			
			
		
			

	}
	
	/**
	 * 获取商品库存
	 * 
	 */
	public function getGoodsStock(){
		$data = array();
		$data['goodsId'] = (int)I('goodsId');
		$data['isBook'] = (int)I('isBook');
		$data['goodsAttrId'] = (int)I('goodsAttrId');
		$goods = D('Api/Goods');
		$goodsStock = $goods->getGoodsStock($data);
		echo json_encode($goodsStock);
		
	}
	
	/**
	 * 获取服务社区
	 * 
	 */
	public function getServiceCommunitys(){
		
		$areas = D('Api/Areas');
		$serviceCommunitys = $areas->getShopCommunitys();
		echo json_encode($serviceCommunitys);
	}
	
   /**
	* 分页查询-出售中的商品
	*/
	public function queryOnSaleByPage(){
		$this->isShopLogin();
		$USER = session('WST_USER');
		//获取商家商品分类
		$m = D('Api/ShopsCats');
		$this->assign('shopCatsList',$m->queryByList($USER['shopId'],0));
		$m = D('Api/Goods');
    	$page = $m->queryOnSaleByPage($USER['shopId']);
    	$pager = new \Think\Page($page['total'],$page['pageSize']);
    	$page['pager'] = $pager->show();
    	$this->assign('Page',$page);
    	$this->assign("umark","queryOnSaleByPage");
    	$this->assign("shopCatId2",I('shopCatId2'));
    	$this->assign("shopCatId1",I('shopCatId1'));
    	$this->assign("goodsName",I('goodsName'));
        $this->display("default/shops/goods/list_onsale");
	}
   /**
	* 分页查询-仓库中的商品
	*/
	public function queryUnSaleByPage(){
		$this->isShopLogin();
		$USER = session('WST_USER');
		//获取商家商品分类
		$m = D('Api/ShopsCats');
		$this->assign('shopCatsList',$m->queryByList($USER['shopId'],0));
		$m = D('Api/Goods');
    	$page = $m->queryUnSaleByPage($USER['shopId']);
    	$pager = new \Think\Page($page['total'],$page['pageSize']);
    	$page['pager'] = $pager->show();
    	$this->assign('Page',$page);
    	$this->assign("umark","queryUnSaleByPage");
    	$this->assign("shopCatId2",I('shopCatId2'));
    	$this->assign("shopCatId1",I('shopCatId1'));
    	$this->assign("goodsName",I('goodsName'));
        $this->display("default/shops/goods/list_unsale");
	}
   /**
	* 分页查询-在审核中的商品
	*/
	public function queryPenddingByPage(){
		$this->isShopLogin();
		$USER = session('WST_USER');
		//获取商家商品分类
		$m = D('Api/ShopsCats');
		$this->assign('shopCatsList',$m->queryByList($USER['shopId'],0));
		$m = D('Api/Goods');
    	$page = $m->queryPenddingByPage($USER['shopId']);
    	$pager = new \Think\Page($page['total'],$page['pageSize']);
    	$page['pager'] = $pager->show();
    	$this->assign('Page',$page);
    	$this->assign("umark","queryPenddingByPage");
    	$this->assign("shopCatId2",I('shopCatId2'));
    	$this->assign("shopCatId1",I('shopCatId1'));
    	$this->assign("goodsName",I('goodsName'));
        $this->display("default/shops/goods/list_pendding");
	}
	/**
	 * 跳到新增/编辑商品
	 */
    public function toEdit(){
		$this->isShopLogin();
		$USER = session('WST_USER');
		//获取商品分类信息
		$m = D('Api/GoodsCats');
		$this->assign('goodsCatsList',$m->queryByList());
		$sm = D('Api/ShopsCats');
		$pkShopCats = $sm->getCatAndChild($USER['shopId']);
		$this->assign('pkShopCats',$pkShopCats);
		//获取商家商品分类
		$m = D('Api/ShopsCats');
		$this->assign('shopCatsList',$m->queryByList($USER['shopId'],0));
		//获取商品类型
		$m = D('Api/AttributeCats');
		$this->assign('attributeCatsCatsList',$m->queryByList());
		$m = D('Api/Goods');
		$object = array();
		$goodsId = (int)I('id',0);
    	if($goodsId>0){
    		$object = $m->get();
    		$packages = $m->getGoodsPackages($goodsId);
    		$this->assign('packages',$packages);
    	}else{
    		$object = $m->getModel();
    	}
    	$this->assign('object',$object);
    	$this->assign("umark",I('umark'));
        $this->display("default/shops/goods/edit");
	}
	/**
	 * 新增商品
	 */
	public function edit(){
		$this->isShopLogin();
		$m = D('Api/Goods');
    	$rs = array();
    	if((int)I('id',0)>0){
    		$rs = $m->edit();
    	}else{
    		$rs = $m->insert();
    	}
    	$this->ajaxReturn($rs);
	}
	/**
	 * 删除商品
	 */
	public function del(){
		$this->isShopLogin();
		$m = D('Api/Goods');
		$rs = $m->del();
		$this->ajaxReturn($rs);
	}
	/**
	 * 批量设置商品状态
	 */
	public function goodsSet(){
		$this->isShopLogin();
		$m = D('Api/Goods');
		$rs = $m->goodsSet();
		$this->ajaxReturn($rs);
	}
	/**
	 * 批量删除商品
	 */
	public function batchDel(){
		$this->isShopLogin();
		$m = D('Api/Goods');
		$rs = $m->batchDel();
		$this->ajaxReturn($rs);
	}
	/**
	 * 修改商品上架/下架状态
	 */
	public function sale(){
		$this->isShopLogin();
		$m = D('Api/Goods');
		$rs = $m->sale();
		$this->ajaxReturn($rs);
	}
	
	
	
	/**
	 * 核对商品信息
	 */
	public function checkGoodsStock(){
	
		$m = D('Api/Cart');
		$catgoods = $m->checkGoodsStock();
		$this->ajaxReturn($catgoods);
	
	}
	
	/**
	 * 获取验证码
	 */
	public function getGoodsVerify(){
		$data = array();
		$data["status"] = 1;
		$verifyCode = md5(base64_encode("wstmall".date("Y-m-d")));
		$data["verifyCode"] = $verifyCode;
		$this->ajaxReturn($data);
	}
	
	/**
	 * 查询商品属性价格及库存
	 */
    public function getPriceAttrInfo(){
    	$goods = D('Api/Goods');
		$rs = $goods->getPriceAttrInfo();
		$this->ajaxReturn($rs);
    }
	/**
	 * 修改商品库存
	 */
    public function editStock(){
    	$this->isShopLogin();
    	$m = D('Api/Goods');
    	$rs = $m->editStock();
    	$this->ajaxReturn($rs);
    }
    
    /**
     * 修改商品库存,商品编号,价格
     */
    public function editGoodsBase(){
    	$this->isShopLogin();
    	$m = D('Api/Goods');
    	$rs = $m->editGoodsBase();
    	$this->ajaxReturn($rs);
    }
    
    /**
     * 获取商品搜索提示列表
     */
    public function getKeyList(){
    	$m = D('Api/Goods');
    	$areaId2 = $this->getDefaultCity();
    	$rs = $m->getKeyList($areaId2);
    	$this->ajaxReturn($rs);
    }
    
    /**
     * 修改 推荐/精品/新品/热销/上架
     */
    public function changSaleStatus(){
    	$this->isShopLogin();
    	$m = D('Api/Goods');
    	$rs = $m->changSaleStatus();
    	$this->ajaxReturn($rs);
    }
    
    /**
     * 上传商品数据
     */
    public function importGoods(){
    	$this->isShopLogin();
    	$config = array(
		        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
		        'exts'          =>  array('xls','xlsx','xlsm'), //允许上传的文件后缀
		        'rootPath'      =>  './Upload/', //保存根路径
		        'driver'        =>  'LOCAL', // 文件上传驱动
		        'subName'       =>  array('date', 'Y-m'),
		        'savePath'      =>  I('dir','uploads')."/"
		);
		$upload = new \Think\Upload($config);
		$rs = $upload->upload($_FILES);
		$rv = array('status'=>-1);
		if(!$rs){
			$rv['msg'] = $upload->getError();
		}else{
			$m = D('Api/Goods');
    	    $rv = $m->importGoods($rs);
		}
    	$this->ajaxReturn($rv);
    }
    
    public function getGoodsByCat() {
    	$this->isShopLogin();
    	$m = D('Api/Goods');
    	$rs = $m->getGoodsByCat();
    	$this->ajaxReturn($rs);
    }
    
    public function getPackageGoods(){
    	$this->isShopLogin();
    	$m = D('Api/Goods');
    	$rs = $m->getPackageGoods();
    	$this->ajaxReturn($rs);
    }
    
    public function editGoodsPackages(){
    	$this->isShopLogin();
    	$m = D('Api/Goods');
    	$rs = $m->editGoodsPackages();
    	$this->ajaxReturn($rs);
    }
	
}