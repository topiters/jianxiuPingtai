/*!
 * ======================================================
 * FeedBack Template For MUI (http://dev.dcloud.net.cn/mui)
 * =======================================================
 * @version:1.0.0
 */
//var _data="";
//(function() {
	var imgArrs = [];
	var index = 1;
	var size = null;
	var imageIndexIdNum = 0;
	var starIndex = 0;
	var feedback = {
		//question: document.getElementById('question'), 
		//contact: document.getElementById('contact'), 
		imageList: document.getElementById('image-list'),
		submitBtn: document.getElementById('saveMsg')
	};
//	var url = 'http://test.cnceshi.com/index.php?m=Api&c=shops&a=addgoods';
	feedback.files = [];
	feedback.uploader = null;  
	feedback.deviceInfo = null; 
	mui.plusReady(function() {
		//设备信息，无需修改
		feedback.deviceInfo = {
			appid: plus.runtime.appid, 
			imei: plus.device.imei, //设备标识
			images: feedback.files, //图片文件
			p: mui.os.android ? 'a' : 'i', //平台类型，i表示iOS平台，a表示Android平台。
			md: plus.device.model, //设备型号
			app_version: plus.runtime.version,
			plus_version: plus.runtime.innerVersion, //基座版本号
			os:  mui.os.version,
			net: ''+plus.networkinfo.getCurrentType()
		}
	});
	/**
	 *提交成功之后，恢复表单项 
	 */
	feedback.clearForm = function() {
//		feedback.question.value = '';
//		feedback.contact.value = '';
		feedback.imageList.innerHTML = '';
		feedback.newPlaceholder();
		feedback.files = [];
		index = 0;
		size = 0;
		imageIndexIdNum = 0;
		starIndex = 0;
		//清除所有星标
		mui('.icons i').each(function (index,element) {
			if (element.classList.contains('mui-icon-star-filled')) {
				element.classList.add('mui-icon-star')
	  			element.classList.remove('mui-icon-star-filled')
			}
		})
	};
	feedback.getFileInputArray = function() {
		return [].slice.call(feedback.imageList.querySelectorAll('.file'));
	};
	feedback.addFile = function(path) {
		feedback.files.push({name:"images"+index,path:path});
		index++;
	};
	/**
	 * 初始化图片域占位
	 */
	feedback.newPlaceholder = function() {
		var fileInputArray = feedback.getFileInputArray();
		if (fileInputArray &&
			fileInputArray.length > 0 &&
			fileInputArray[fileInputArray.length - 1].parentNode.classList.contains('space')) {
			return;
		};
		imageIndexIdNum++;
		var placeholder = document.createElement('div');
		placeholder.setAttribute('class', 'image-item space');
		var up = document.createElement("div");
		up.setAttribute('class','image-up')
		//删除图片
		var closeButton = document.createElement('div');
		closeButton.setAttribute('class', 'image-close');
		closeButton.innerHTML = 'X';
		//小X的点击事件
		closeButton.addEventListener('tap', function(event) {
			setTimeout(function() {
				feedback.imageList.removeChild(placeholder);
			}, 0);
			return false;
		}, false);
		
		//
		var fileInput = document.createElement('div');
		fileInput.setAttribute('class', 'file');
		fileInput.setAttribute('id', 'image-' + imageIndexIdNum);
		fileInput.addEventListener('tap', function(event) {
			var self = this;
			var index = (this.id).substr(-1);
			
			plus.gallery.pick(function(e) {
//				console.log("event:"+e);
				var name = e.substr(e.lastIndexOf('/') + 1);
				console.log("name:"+name);
					
				plus.zip.compressImage({
					src: e,
					dst: '_doc/' + name,
					overwrite: true,
					quality: 50
				}, function(zip) {
					size += zip.size  
					console.log("filesize:"+zip.size+",totalsize:"+size);
					if (size > (10*1024*1024)) {
						return mui.toast('文件超大,请重新选择~');
					}
					if (!self.parentNode.classList.contains('space')) { //已有图片
						feedback.files.splice(index-1,1,{name:"images"+index,path:e});
					} else { //加号
						placeholder.classList.remove('space');
						feedback.addFile(zip.target);
						feedback.newPlaceholder();
					}
					up.classList.remove('image-up');
					placeholder.style.backgroundImage = 'url(' + zip.target + ')';
					console.log(zip.target);
				}, function(zipe) {
					mui.toast('压缩失败！')
				});
			}, function(e) {
				mui.toast(e.message);
			},{});
		}, false);
		placeholder.appendChild(closeButton);
		placeholder.appendChild(up);
		placeholder.appendChild(fileInput);
		feedback.imageList.appendChild(placeholder);
	};
	feedback.newPlaceholder();
	feedback.submitBtn.addEventListener('tap', function(event) {
//		if (feedback.question.value == '' ||
//			(feedback.contact.value != '' &&
//				feedback.contact.value.search(/^(\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+)|([1-9]\d{4,9})$/) != 0)) {
//			return mui.toast('信息填写不符合规范');
//		}
//		if (feedback.question.value.length > 200 || feedback.contact.value.length > 200) {
//			return mui.toast('信息超长,请重新填写~')
//		}
//		//判断网络连接
		if(plus.networkinfo.getCurrentType()==plus.networkinfo.CONNECTION_NONE){
			return mui.toast("连接网络失败，请稍后再试");
		}
		console.log(feedback.files)
		feedback.send(mui.extend({}, feedback.deviceInfo, {
//			content: feedback.question.value,      
//			contact: feedback.contact.value,
			images: feedback.files,
			score:''+starIndex
		}))
	}, false)
	var url = 'http://test.cnceshi.com/index.php?m=Api&c=shops&a=uploads';
	feedback.send = function(content) {
		feedback.uploader = plus.uploader.createUpload(url, {
			method: 'POST'
		}, function(upload, status) {
//			plus.nativeUI.closeWaiting()
			console.log("upload cb:"+upload );
			if(status==200){
				var data = JSON.parse(upload.responseText);
				//上传成功，重置表单   
				console.log(upload.responseText);
				var imgArrs=[];
				for(var i in data){
					_data = "Upload/"+data[i].savepath + data[i].savename;
					imgArrs.push(_data);
					console.log(_data);
				}
				/**  
				 * 发布检修任务
				 */
				
				console.log(imgArrs);
				
				var shopImg = document.getElementById("head-img1").getAttribute("src");
				var shopCompany = document.getElementById("shopCompany").value;
				var goodsCatId1 = document.getElementById("goodsCatId1").value;
				var shoptotal = document.getElementById("shoptotal").value;
				var shopInfo = document.getElementById("area").value;
				var shopArea = document.getElementById("shopArea").value;
				var shopAddr = document.getElementById("shopAddr").value;
				var shopUrl = document.getElementById("shopUrl").value;
				var shopHost = document.getElementById("shopHost").value;
				var manufacturerInfo = document.getElementById("area").value;
				var btnArray = ['否', '是'];
				mui.confirm('确认保存？', '提示', btnArray, function(e) {
					if (e.index == 1) {
						mui.ajax('http://test.cnceshi.com/index.php?m=Api&c=shops&a=addByUser',{
							data:{
								shopImg: shopImg,
								shopCompany: shopCompany,
								goodsCatId1: goodsCatId1,
								shopTotal: shoptotal,
								shopInfo: shopInfo,
								shopArea: shopArea,
								shopAddr: shopAddr,
								shopUrl: shopUrl,
								manufacturerInfo: manufacturerInfo,
								latitude: "",
								longitude: "",
								shopHost: shopHost,
								shopIdentimg: imgArrs
							},
							dataType:'json',//服务器返回json格式数据
							type:'post',//HTTP请求类型
							timeout:10000,//超时时间设置为10秒；
							success:function(data){
								//服务器返回响应，根据响应结果，分析是否获取数据成功；
								if( data.status == 200 ){
									mui.toast(data.msg.msg);
									mui.openWindow({
										url: "index-home.html"
									});	
								}else{
									mui.toast(data.msg.msg);
			//						mui.toast("数据请求失败，请刷新页面")
								}   
							},
							error:function(xhr,type,errorThrown){
								//异常处理；
								console.log(type);
							}
						});			
					} else {
						     
					}
				})
				
				if (data.ret === 0 && data.desc === 'Success') {
//					mui.toast('反馈成功~')
					console.log("upload success");
//					feedback.clearForm();
				}
			}else{
				console.log("upload fail");
			}
		});
		//添加上传数据
		mui.each(content, function(index, element) {
			if (index !== 'images') {
				console.log("addData:"+index+","+element);
//				console.log(index);
				feedback.uploader.addData(index, element)
			} 
		});
		//添加上传文件
		mui.each(feedback.files, function(index, element) {
			var f = feedback.files[index];
			console.log("addFile:"+JSON.stringify(f));
			feedback.uploader.addFile(f.path, {
				key: f.name
			});
		});
		//开始上传任务
		feedback.uploader.start();
//		mui.alert("感谢反馈，点击确定关闭","问题反馈","确定",function () {
//			feedback.clearForm();
//			mui.back();
//		});
//		plus.nativeUI.showWaiting();
	};
	
	 //应用评分
//	 mui('.icons').on('tap','i',function(){
//	  	var index = parseInt(this.getAttribute("data-index"));
//	  	var parent = this.parentNode;
//	  	var children = parent.children;
//	  	if(this.classList.contains("mui-icon-star")){
//	  		for(var i=0;i<index;i++){
//				children[i].classList.remove('mui-icon-star');
//				children[i].classList.add('mui-icon-star-filled');
//	  		}
//	  	}else{
//	  		for (var i = index; i < 5; i++) {
//	  			children[i].classList.add('mui-icon-star')
//	  			children[i].classList.remove('mui-icon-star-filled')
//	  		}
//	  	}
//	  	starIndex = index;
//});
  	//选择快捷输入
	mui('.mui-popover').on('tap','li',function(e){
	  document.getElementById("question").value = document.getElementById("question").value + this.children[0].innerHTML;
	  mui('.mui-popover').popover('toggle')
	}) 
	
//})();

