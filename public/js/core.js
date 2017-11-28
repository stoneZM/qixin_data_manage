/* 扩展QinfoCMS对象 */
(function($) {
	/**
	 * 获取QinfoCMS基础配置
	 * @type {object}
	 */
	var QinfoCMS = window.Qinfo;

	/* 基础对象检测 */
	QinfoCMS || $.error("QinfoCMS基础配置没有正确加载！");

	/**
	 * 解析URL
	 * @param  {string} url 被解析的URL
	 * @return {object}     解析后的数据
	 */
	QinfoCMS.parse_url = function(url) {
		var parse = url.match(/^(?:([a-z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
		parse || $.error("url格式不正确！");
		return {
			"scheme": parse[1],
			"host": parse[2],
			"port": parse[3],
			"path": parse[4],
			"query": parse[5],
			"fragment": parse[6]
		};
	}

	QinfoCMS.parse_str = function(str) {
		var value = str.split("&"),
			vars = {},
			param;
		for (val in value) {
			param = value[val].split("=");
			vars[param[0]] = param[1];
		}
		return vars;
	}

	QinfoCMS.parse_name = function(name, type) {
		if (type) {
			/* 下划线转驼峰 */
			name.replace(/_([a-z])/g, function($0, $1) {
				return $1.toUpperCase();
			});

			/* 首字母大写 */
			name.replace(/[a-z]/, function($0) {
				return $0.toUpperCase();
			});
		} else {
			/* 大写字母转小写 */
			name = name.replace(/[A-Z]/g, function($0) {
				return "_" + $0.toLowerCase();
			});

			/* 去掉首字符的下划线 */
			if (0 === name.indexOf("_")) {
				name = name.substr(1);
			}
		}
		return name;
	}

	//scheme://host:port/path?query#fragment
	QinfoCMS.U = function(url, vars, suffix) {
		var info = this.parse_url(url),
			path = [],
			param = {},
			reg;

		/* 验证info */
		info.path || $.error("url格式错误！");
		url = info.path;

		/* 组装URL */
		if (0 === url.indexOf("/")) { //路由模式
			this.MODEL[0] == 0 && $.error("该URL模式不支持使用路由!(" + url + ")");

			/* 去掉右侧分割符 */
			if ("/" == url.substr(-1)) {
				url = url.substr(0, url.length - 1)
			}
			url = ("/" == this.DEEP) ? url.substr(1) : url.substr(1).replace(/\//g, this.DEEP);
			url = "/" + url;
		} else { //非路由模式
			/* 解析URL */
			path = url.split("/");
			path = [path.pop(), path.pop(), path.pop()].reverse();
			path[1] || $.error("QinfoCMS.U(" + url + ")没有指定控制器");

			if (path[0]) {
				param[this.VAR[0]] = this.MODEL[1] ? path[0].toLowerCase() : path[0];
			}

			param[this.VAR[1]] = this.MODEL[1] ? this.parse_name(path[1]) : path[1];
			param[this.VAR[2]] = path[2].toLowerCase();

			url = "?" + $.param(param);
		}

		/* 解析参数 */
		if (typeof vars === "string") {
			vars = this.parse_str(vars);
		} else if (!$.isPlainObject(vars)) {
			vars = {};
		}

		/* 解析URL自带的参数 */
		info.query && $.extend(vars, this.parse_str(info.query));

		if (vars) {
			url += "&" + $.param(vars);
		}

		if (0 != this.MODEL[0]) {
			url = url.replace("?" + (path[0] ? this.VAR[0] : this.VAR[1]) + "=", "/")
				.replace("&" + this.VAR[1] + "=", this.DEEP)
				.replace("&" + this.VAR[2] + "=", this.DEEP)
				.replace(/(\w+=&)|(&?\w+=$)/g, "")
				.replace(/[&=]/g, this.DEEP);
			if ("/" == url.substr(-1)) {
				url = url.substr(0, url.length - 1)
			}

			/* 添加伪静态后缀 */
			if (false !== suffix) {
				suffix = suffix || this.MODEL[2].split("|")[0];
				if (suffix) {
					url += "." + suffix;
				}
			}
		}

		url = this.APP + url;
		return url;
	}

	/* 设置表单的值 */
	QinfoCMS.setValue = function(name, value) {
		var first = name.substr(0, 1),
			input, i = 0,
			val;
		if (value === "") return;
		if ("#" === first || "." === first) {
			input = $(name);
		} else {
			input = $("[name='" + name + "']");
		}

		if (input.eq(0).is(":radio")) { //单选按钮
			input.filter("[value='" + value + "']").each(function() {
				this.checked = true
			});
		} else if (input.eq(0).is(":checkbox")) { //复选框
			if (!$.isArray(value)) {
				val = new Array();
				val[0] = value;
			} else {
				val = value;
			}
			for (i = 0, len = val.length; i < len; i++) {
				input.filter("[value='" + val[i] + "']").each(function() {
					this.checked = true
				});
			}
		} else { //其他表单选项直接设置值
			input.val(value);
		}
	}

})(jQuery);

//dom加载完成后执行的js
;
$(function() {

	window.Modal = function () {  
		var reg = new RegExp("\\[([^\\[\\]]*?)\\]", 'igm');  
		var alr = $("#com-alert");  
		var ahtml = alr.html();  
	  
		var _tip = function (options, sec) {  
			alr.html(ahtml);    // 复原  
			alr.find('.ok').hide();  
			alr.find('.cancel').hide();  
			alr.find('.modal-content').width(500);  
			_dialog(options, sec);  
			  
			return {  
				on: function (callback) {  
				}  
			};  
		};  
	  
		var _alert = function (options) {  
		  alr.html(ahtml);  // 复原  
		  alr.find('.ok').removeClass('btn-success').addClass('btn-primary');  
		  alr.find('.cancel').hide();  
		  _dialog(options);  
	  
		  return {  
			on: function (callback) {  
			  if (callback && callback instanceof Function) {  
				alr.find('.ok').click(function () { callback(true) });  
			  }  
			}  
		  };  
		};  
	  
		var _confirm = function (options) {  
		  alr.html(ahtml); // 复原  
		  alr.find('.ok').removeClass('btn-primary').addClass('btn-success');  
		  alr.find('.cancel').show();  
		  _dialog(options);  
	  
		  return {  
			on: function (callback) {  
			  if (callback && callback instanceof Function) {  
				alr.find('.ok').click(function () { callback(true) });  
				alr.find('.cancel').click(function () { return; });  
			  }  
			}  
		  };  
		};  
	  
		var _dialog = function (options) {  
		  var ops = {  
			msg: "提示内容",  
			title: "操作提示",  
			btnok: "确定",  
			btncl: "取消"  
		  };  
	  
		  $.extend(ops, options);  
	  
		  var html = alr.html().replace(reg, function (node, key) {  
			return {  
			  Title: ops.title,  
			  Message: ops.msg,  
			  BtnOk: ops.btnok,  
			  BtnCancel: ops.btncl  
			}[key];  
		  });  
			
		  alr.html(html);  
		  alr.modal({  
			width: 250,  
			backdrop: 'static'  
		  });  
		}  
		return {  
			tip: _tip,  
			alert: _alert,  
			confirm: _confirm  
		}  
	}();  
	
	//全选的实现
	var icheck_input = $('input[type="checkbox"].check-all,input[type="checkbox"].ids').iCheck({
	  checkboxClass: 'icheckbox_minimal-blue',
	});
	icheck_input.iCheck('uncheck');

	
	var checkAll = $('input[type="checkbox"].check-all');
	var checkboxes = $('input[type="checkbox"].ids');
	checkAll.on('ifChecked ifUnchecked', function(event) {
		if (event.type == 'ifChecked') {
		checkboxes.iCheck('check');
		} else {
		checkboxes.iCheck('uncheck');
		}
	});
	checkboxes.on('ifChanged', function(event){
		if(checkboxes.filter(':checked').length == checkboxes.length) {
		checkAll.prop('checked', 'checked');
		} else {
		checkAll.prop('checked','');
		}
		checkAll.iCheck('update');
	});
		
	//ajax get请求
	$('.ajax-get').click(function() {
		var target;
		var that = this;
		var get_data = function(){
			$.get(target).success(function(data) {
				//var data = JSON.parse(data);
				if (data.code == 1) {
					if (data.url) {
						updateAlert(data.msg + ' 页面即将自动跳转~', 'success');
					} else {
						updateAlert(data.msg, 'success');
					}
					setTimeout(function() {
						if (data.url) {
							location.href = data.url;
						} else if ($(that).hasClass('no-refresh')) {
							$('#top-alert').find('button').click();
						} else {
							location.reload();
						}
					}, 1500);
				} else {
					updateAlert(data.msg);
					setTimeout(function() {
						//location.reload();
						// if (data.url) {
						// 	location.href = data.url;
						// } else {
						// 	$('#top-alert').find('button').click();
						// }
					}, 1500);
				}
			});
		}
		if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
			if ($(this).hasClass('confirm')) {
				Modal.confirm({msg: "确认要执行该操作吗?" }).on( function (e) {
					if(e==true){
						get_data();
					}	
				})
			}else{
				get_data();
			}
		}
		return false;
	});

	//ajax post submit请求
	$('.ajax-post').click(function() {
		var target, query, form;
		var target_form = $(this).attr('target-form');
		var that = this;
		var nead_confirm = false;
		
		var post_data = function(){
			$(that).addClass('disabled').attr('autocomplete', 'off').prop('disabled', true);
			$.post(target, query).success(function(data) {
				//var data = JSON.parse(data);
				if (data.code == 1) {
					if (data.url) {
						updateAlert(data.msg + ' 页面即将自动跳转~', 'success');
					} else {
						updateAlert(data.msg, 'success');
					}
					setTimeout(function() {
						$(that).removeClass('disabled').prop('disabled', false);
						if (data.url) {
							location.href = data.url;
						} else if ($(that).hasClass('no-refresh')) {
							$('#top-alert').find('button').click();
						} else {
							location.reload();
						}
					}, 1500);
				} else {
					updateAlert(data.msg, 'danger');
					setTimeout(function() {
						$(that).removeClass('disabled').prop('disabled', false);
						//location.reload();
						// if (data.url) {
						// 	location.href = data.url;
						// } else {
						// 	$('#top-alert').find('button').click();
						// }
					}, 1500);
				}
			});
		}
		
		if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
			
				
			form = $('.' + target_form);
			if ($(this).attr('hide-data') === 'true') { //无数据时也可以使用的功能
				form = $('.hide-data');
				query = form.serialize();
				
			} else if (form.get(0) == undefined) {
				
				return false;
			} else if (form.get(0).nodeName == 'FORM') {
				
				if ($(this).attr('url') !== undefined) {
					target = $(this).attr('url');
				} else {
					target = form.get(0).action;
				}
				query = form.serialize();
				if ($(this).hasClass('confirm')) {
					Modal.confirm({msg: "确认要执行该操作吗?" }).on( function (e) {
						if(e==true){
							post_data();
						}	
					})
				}else{
					post_data();
				}
			} else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
				form.each(function(k, v) {
					if (v.type == 'checkbox' && v.checked == true) {
						nead_confirm = true;
					}
				})
				
				query = form.serialize();
				if (nead_confirm) {
					if ($(this).hasClass('confirm')) {
						Modal.confirm({msg: "确认要执行该操作吗?" }).on( function (e) {
							if(e==true){
								post_data();
							}	
						})
					}else{
						post_data();
					}
				}else{
					$.messager.show('请选择数据!', {placement: 'center',type:'danger'});
				}
			} else {
				query = form.find('input,select,textarea').serialize();
				if ($(this).hasClass('confirm')) {
					Modal.confirm({msg: "确认要执行该操作吗?" }).on( function (e) {
						if(e==true){
							post_data();
						}	
					})
				}else{
					post_data();
				}
			}
			
		}
		return false;
	});

	window.updateAlert = function(text, c) {
		if (typeof c != 'undefined') {
			var msg = $.messager.show(text, {
				placement: 'center',
				type: c
			});
		} else {
			var msg = $.messager.show(text, {
				placement: 'center'
			})
		}
		msg.show();
	};
});

/**
 * 置顶函数
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function go_to_top(obj){
	var scrTop = $(window).scrollTop();
	var windowTop = $(window).height();
	if ((windowTop-300)<scrTop){
		$("#"+obj).fadeIn("slow");
	}else{
		$("#"+obj).fadeOut("slow");
	}	
}