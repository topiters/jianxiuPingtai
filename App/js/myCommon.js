(function(window,undefined){
	
	function commonFn(){
		
/**********加载页面***********************************************************************************************/
		this.loadPage = function(args){

			var _container = args.container;//容器
			var pageUrl = args.pageUrl;//页面路径
			var pageScript = args.pageScript;//页面对应的js文件
			var onLoaded = args.onLoaded;//页面记载完成后回调函数
			
		/***ajax*******************************************************/
			$.ajax({
				url : pageUrl,
				type : 'GET',
				dataType : 'html',
				timeout : 10000,
				success : function(res){
					
//					加载html页面
					var _pageNode = $('<div></div>');
					var _pageNodeTmp = $('<div></div>');
                    _pageNodeTmp.html(res);
                    _pageNode.append(_pageNodeTmp.children());
                    _container.append(_pageNode.children());

//                  加载js文件
                    var pageScriptTmp = document.createElement("script");
                    pageScriptTmp.setAttribute('type','text/javascript');
                    pageScriptTmp.setAttribute("src", pageScript);
                    var documentHead = document.head;
                    if(documentHead == undefined) {
                        documentHead = document.getElementsByTagName("head")[0];
                    }
                    documentHead.appendChild(pageScriptTmp);
                    
//              	js脚本加载完成后回调函数
                    pageScriptTmp.onload = pageScriptTmp.onreadystatechange = function(){
                        if(!this.readyState||this.readyState=='loaded'||this.readyState=='complete'){
                            onLoaded();
                        }
                    }
				},
				error : function(){
					alert('页面出错了');
				}
			});
		/***ajax END************************************************/
			
		}
/*********加载页面  END*********************************************************************************************/



/*******LOADING START**********************************************************************************************/
		this.loadingStart = function(obj){
			if(obj.find('.loadingDiv').length>0){
                return;
            }
			var thisWidth = obj.width()/2-30;
            var thisHeight = obj.height()/2-30;
            obj.css('position','relative');
            var loadingDiv = '<div class="loadingDiv" style="width:'+obj.width()+'px;height:'+obj.height()+'px;"><div id="loading-center-absolute" ><div class="object" id="object_four"></div><div class="object" id="object_three"></div><div class="object" id="object_two"></div><div class="object" id="object_one"></div></div></div>';
			obj.append(loadingDiv);
		}
		
		this.loadingStop = function(obj){
			obj.find('.loadingDiv').remove();
		}
/*******LOADING END************************************************************************************************/



/********判断是否存在***********************************************************************************************/
		this.isEmpty = function(str){
            return (!str || str.length == 0);
        }
/********判断是否存在 END*******************************************************************************************/



/********判断是否为空***********************************************************************************************/
		this.nullToEmpty = function(str){
            if(str==null){
                return "";
            }
            else{
                return str;
            }
        }
/********判断是否为空 END*******************************************************************************************/



/*******JSON数组对象刷值********************************************************************************************/
		this.loadListData = function(args){
			var sourceDataList = args.sourceDataList;//数据源  数组对象格式
            var dataListRootNode = args.dataListRootNode;//数据渲染一级根节点  整个数组
            var dataListChildNode = args.dataListChildNode;//数据渲染二级根节点  数组对象里的一个对象
            var dataAttrName  = args.dataAttrName;//数据刷值的属性名称  根据哪一个属性名来刷值
            var dataHandleFn = args.dataHandleFn;//刷值过程中执行的回调函数  传入的参数:二级子节点 和 该对象在数组中的index

//          遍历JSON数组对象
            for(var w = 0;w<sourceDataList.length;w++){//外层循环
                var dataListChildNodeCopy = dataListChildNode.clone();
                var subDataList = sourceDataList[w];
                for(var z in subDataList){//内层循环
                    var _thisNode = dataListChildNodeCopy.find('['+dataAttrName+'='+z+']');
                    if(!$commonObj.isEmpty(_thisNode)){
                        if(_thisNode.get(0).tagName.toLowerCase()=="input"){
                            _thisNode.val(subDataList[z]);
                        }else{
                            _thisNode.text(subDataList[z]);
                        }
                    }
                }
                $(dataListRootNode).append(dataListChildNodeCopy);
                dataHandleFn(dataListChildNodeCopy,w);
            }
		}
/*******JSON数组对象刷值 END********************************************************************************************/



/*******JSON对象刷值************************************************************************************************/
		this.loadSingleData = function(args){
			var sourceData = args.sourceData;//数据源  对象格式
			var dataRootNode = args.dataRootNode;//数据渲染根节点  整个对象
			var dataAttrName  = args.dataAttrName;//数据刷值的属性名称  根据哪一个属性名来刷值
			var dataHandleFn = args.dataHandleFn;//刷值完成后执行的回调函数
//			遍历对象
			for(var w in sourceData){
				var _thisNode = dataRootNode.find('['+dataAttrName+'='+w+']');
				if(!$commonObj.isEmpty(_thisNode)){
                    if(_thisNode.get(0).tagName.toLowerCase()=="input"){;
                        _thisNode.val(this.nullToEmpty(sourceData[w]));
                    }
                    else{
                        _thisNode.text(this.nullToEmpty(sourceData[w]))
                    }
                }
			}
			dataHandleFn();
		}
/*******JSON对象刷值 END********************************************************************************************/
	}
	var commonFnObj = new commonFn();
	window.$commonObj = commonFnObj;
})(window)
