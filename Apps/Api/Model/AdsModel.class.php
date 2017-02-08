<?php
namespace Api\Model;
/**
*  广告控制器
* ==============================================
* 版权所有 2010-2016 http://www.chunni168.com
* ----------------------------------------------
* 这不是一个自由软件，未经授权不许任何使用和传播。
* ==============================================
* @date: 2017年1月3日
* @author: top_iter 2504585798@qq.com
* @version:1.0
*/
class AdsModel extends BaseModel {
	/**
	 * 获取广告
	 */
	public function getAds($areaId2,$adType){
		$today = date("Y-m-d");
		$data = S('WST_CACHE_ADS_'.$areaId2."_".$adType);
		if(!$data){
			//获取所在省份
			$sql = "select parentId from __PREFIX__areas where areaId=".$areaId2;
			$areaId1 = $this->queryRow($sql);
			$areaId1 = (int)$areaId1['parentId'];
			$sql = "select adId,adName,adURL,adFile from __PREFIX__ads WHERE (areaId2 = $areaId2 or areaId1 = 0 or (areaId1=".$areaId1." and areaId2=0))
						AND adStartDate<='$today' AND adEndDate >='$today' and adPositionId=".$adType." order by adSort asc";
			$data = $this->query($sql);
			S('WST_CACHE_ADS_'.$areaId2."_".$adType,$data,86400);
		}
		return $data;
	}
	/**
	 * 获取主页分类广告
	 */
	public function getAdsByCat($areaId2){
		$today = date("Y-m-d");
		$data = S('WST_CACHE_ADS_CAT_'.$areaId2);
		if(!$data){
			//获取所在省份
			$sql = "select parentId from __PREFIX__areas where areaId=".$areaId2;
			$rs = $this->queryRow($sql);
			//var_dump($rs);exit;
			$areaId1 = $rs['parentId'];
			$sql = "select adId,adName,adURL,adFile,adPositionId from __PREFIX__ads WHERE (areaId2 = $areaId2 or areaId1 = 0 or areaId1=".$areaId1.")
					AND adStartDate<='$today' AND adEndDate >='$today' and adPositionId='-4' order by adSort asc";
			$rs = $this->query($sql);
			//var_dump($sql);
		//exit;
			$data = array();
			foreach ($rs as $v){
				$data[$v['adPositionId']][] = $v;
			}
			S('WST_CACHE_ADS_CAT_'.$areaId2,$data,86400);
		}
		return $data;
	}
	/**
	 * 广告统计
	 */
	public function statistics($adId){
		$sql = "UPDATE __PREFIX__ads set adClickNum = adClickNum+1 WHERE adId = $adId";		
		$rs = $this->execute($sql);
	}
}