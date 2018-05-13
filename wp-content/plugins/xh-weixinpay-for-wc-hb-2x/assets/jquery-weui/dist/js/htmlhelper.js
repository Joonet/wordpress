/**
 * paging helper == must be place end
 *
*/
;(function () {
	"use strict";

	window.htmlhelper = {
		ifnullOrEmpty: function(source, $default) {
			if (htmlhelper.isNullOrEmpty(source)) {
				return $default;
			}

			return source;
		},
		isNullOrEmpty: function(source) {
			return (typeof source == "undefined" || source == null || source.length === 0);
		},
		removeAt: function(array, func) {
			if (htmlhelper.isNullOrEmpty(array)) {
				return array;
			}

			var indexof = htmlhelper.indexOf(array, func);

			if (indexof < 0) {
				return array;
			}

			return array.splice(indexof, 1);
		},
		isNullOrWhitespace: function(source) {
			if (window.htmlhelper.isNullOrEmpty(source)) {
				return true;
			}

			for (var index = 0; index < source.length; index++) {
				if (!window.htmlhelper.isNullOrEmpty(source[index])) {
					return false;
				}
			}

			return true;
		},
		parseInt: function(source, defaultInt) {
			if (typeof defaultInt === 'undefined') {
				defaultInt = 0;
			}
			if (htmlhelper.isNullOrEmpty(source)) {
				return defaultInt;
			}

			var value = parseInt(source);
			if (isNaN(value)) {
				value = defaultInt;
			}

			return value;
		},
		parseFloat: function(source, defaultInt) {
			if (typeof defaultInt === 'undefined') {
				defaultInt = 0;
			}

			if (htmlhelper.isNullOrEmpty(source)) {
				return defaultInt;
			}

			var value = parseFloat(source);
			if (isNaN(value)) {
				value = defaultInt;
			}

			return value;
		},
		ajaxError: function(e) {
			htmlhelper.dialog.alert('系统出现了异常，请稍候重试！');
            if(window.debug){
              window.__error=e;  
            }
		},
		mergerError: function (result, onSuccessCallback) {
            if(window.debug){
              window.__result=result;  
            }
			if (result.success) {
				onSuccessCallback(result);
				return false;
			}

			htmlhelper.dialog.alert(result.message);
			return true;
		},
		firstOrDefault: function(array, where) {
			if (htmlhelper.isNullOrEmpty(array)) {
				return null;
			}

			for (var index = 0; index < array.length; index++) {
				var item = array[index];
				if (!where) {
					return item;
				}

				if (where(item)) {
					return item;
				}
			}

			return null;
		},
		select: function(array, selector) {
			if (htmlhelper.isNullOrEmpty(array)) {
				return null;
			}

			if (array == null) {
				return array;
			}

			var newArray = [];
			for (var index = 0; index < array.length; index++) {
				var item = array[index];
				newArray.push(selector(item));
			}

			return newArray;
		},
		any: function(array, where) {
			if (htmlhelper.isNullOrEmpty(array)) {
				return false;
			}

			if (where == null) {
				return true;
			}

			for (var index = 0; index < array.length; index++) {
				if (where(array[index])) {
					return true;
				}
			}

			return false;
		},
		all: function (array, where) {
			if (htmlhelper.isNullOrEmpty(array)) {
				return false;
			}

			if (where == null) {
				return false;
			}

			for (var index = 0; index < array.length; index++) {
				if (!where(array[index])) {
					return false;
				}
			}

			return true;
		},
		indexOf: function (array, where) {
			if (array == null) {
				return -1;
			}

			if (where == null) {
				return -1;
			}

			for (var index = 0; index < array.length; index++) {
				if (where(array[index])) {
					return index;
				}
			}

			return -1;
		},
		where: function (array, where) {
			if (array == null) {
				return null;
			}

			if (where == null) {
				return null;
			}

			var array1 = [];
			for (var index = 0; index < array.length; index++) {
				if (where(array[index])) {
					array1.push(array[index]);
				}
			}

			return array1;
		},
		integer: function (source) {
			if (typeof source !== 'number') {
				return parseInt(source);
			}

			return source;
		},
		isDecimal: function (source) {
			if (htmlhelper.isNullOrEmpty(source)) {
				return false;
			}

			var regex = /^\d+(\.\d+)?$/;
			return regex.test(source);
		},
		isInteger: function (source) {
			if (htmlhelper.isNullOrEmpty(source)) {
				return false;
			}

			var regex = /^\d+$/;
			return regex.test(source);
		},
		/*
		 @param array Array
		 @param selector function(item,index){}
		*/
		array2string: function (array, selector) {
			var content = '';
			if (array == null) {
				return content;
			}

			if (selector == null) {
				return content;
			}

			for (var i = 0; i < array.length; i++) {
				content += selector(array[i], i);
			}

			return content;
		},
		replace:function(array, newitem, func) {
			if (array == null) {
				return;
			}

			for (var index = 0; index < array.length; index++) {
				if (func(array[index])) {
					array[index] = newitem;
					return;
				}
			}
		} ,
		replaceAll:function(array, newitem, func) {
			if (array == null) {
				return;
			}

			for (var index = 0; index < array.length; index++) {
				if (func(array[index])) {
					array[index] = newitem;
				}
			}
		},
		each: function (array, func) {
		    if (array == null) {
		        return;
		    }

		    for (var index = 0; index < array.length; index++) {
		        func(array[index], index);
		    }
		},
		sum: function (source, selector) {
			if (htmlhelper.isNullOrEmpty(source)) {
				return 0;
			}

			var sum = 0;
			for (var index = 0; index < source.length; index++) {
				sum += selector(source[index]);
			}

			return sum;
		},
        __callWeixin:function(){
            if(!window.__waittingWeixinCallbackEvents){
                return;
            } 
            
            for(var i=0;i<window.__waittingWeixinCallbackEvents.length;window.__waittingWeixinCallbackEvents++){
                window.__waittingWeixinCallbackEvents[i]();
            }
        },
        callWeixin:function(callback){
            if(!htmlhelper.isWeixinClient()){
                htmlhelper.dialog.alert('请在微信客户端打开链接');
                return;
            }
            
            var WeixinJSBridge = window.WeixinJSBridge;
           
            
            if (typeof WeixinJSBridge === "undefined") {
                if(!window.__waittingWeixinCallbackEvents){
                   window.__waittingWeixinCallbackEvents=[];
                }
            
                window.__waittingWeixinCallbackEvents.push(callback);
            
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', htmlhelper.__callWeixin, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', htmlhelper.__callWeixin);
                    document.attachEvent('onWeixinJSBridgeReady', htmlhelper.__callWeixin);
                }
            } else {
                callback();
            }
        },
        isWeixinClient:function(){
            var ua = navigator.userAgent.toLowerCase();
            var isWeixin = ua.indexOf('micromessenger') != -1;
            //var isAndroid = ua.indexOf('android') != -1;
            //var isIos = (ua.indexOf('iphone') != -1) || (ua.indexOf('ipad') != -1);
            return isWeixin;
        }
	};
})();

(function () {
	'use strict';
	window.htmlhelper.dialog = {
		alert: function (message,callback) {
			if (htmlhelper.isNullOrEmpty(message)) {
				message = '系统异常，请稍候重试！';
			}

			if(callback&&typeof callback=='function'){
				$.alert(message,callback);
			}else{
				$.alert(message);
			}
			
			var $msg =$('.weui_dialog.weui_dialog_visible');
			
			var left =($(window).width()-$msg.width())/2;
			$msg.css('left',left+'px');
		},
		confirm: function (message, options) {
			$.confirm(message,
					  options.yes?options.yes:function(){},
					  options.no?options.no:function(){});
		},

		loading: {
			show: function (msg) {
				if(htmlhelper.isNullOrEmpty(msg)){
					msg='加载中...';
				}
				
				$.showLoading(msg);
				$('.weui_toast.weui_loading_toast.weui_toast_visible').click(function(){
					$.hideLoading();
				});
			},
			hide: function () {
				$.hideLoading();
			}
		}
	};
})();
