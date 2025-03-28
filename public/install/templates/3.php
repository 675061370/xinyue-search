<?php require './templates/header.php';?>
	<form id="J_install_form" action="index.php?step=4" method="post">
		<div class="section">
			<div class="main server">
				<table width="100%">
					<tr>
						<td class="td1" width="100">数据库信息</td>
						<td class="td1" width="200">&nbsp;</td>
						<td class="td1">&nbsp;</td>
					</tr>
					<?php if(INSTALLTYPE == 'HOST'){?>
					<tr>
						<td class="tar">数据库服务器：</td>
						<td><input type="text" name="dbhost" id="dbhost" value="127.0.0.1" class="input"></td>
						<td><div id="J_install_tip_dbhost"><span class="gray">数据库服务器地址，一般为localhost</span></div></td>
					</tr>
					<tr>
						<td class="tar">数据库端口：</td>
						<td><input type="text" name="dbport" id="dbport" value="3306" class="input"></td>
						<td><div id="J_install_tip_dbport"><span class="gray">数据库服务器端口，一般为3306</span></div></td>
					</tr>
					<tr>
						<td class="tar">数据库用户名：</td>
						<td><input type="text" name="dbuser" id="dbuser" value="" class="input"></td>
						<td><div id="J_install_tip_dbuser"></div></td>
					</tr>
					<tr>
						<td class="tar">数据库密码：</td>
						<td><input type="text" name="dbpw" id="dbpw" value="" class="input" autoComplete="off"></td>
						<td><div id="J_install_tip_dbpw"></div></td>
					</tr>
					<?php }?>
					<? if(INSTALLTYPE == 'HOST' || INSTALLTYPE == 'BAE'){ ?>
					<tr>
						<td class="tar">数据库名：</td>
						<td><input type="text" name="dbname" id="dbname" value="<?php echo $config['dbName'] ?>" class="input"></td>
						<td><div id="J_install_tip_dbname"></div></td>
					</tr>
					<?php } ?>
					<tr>
						<td class="tar">数据库表前缀：</td>
						<td><input type="text" name="dbprefix" id="dbprefix" value="<?php echo $config['dbPrefix'] ?>" class="input"></td>
						<td><div id="J_install_tip_dbprefix"><span class="gray">建议使用默认</span></div></td>
					</tr>
					<tr>
						<td class="tar">测试数据库连接：</td>
						<td><button type="button" class="btn" onclick="TestDbPwd()">测试连接</button></td>
						<td>&nbsp;</td>
					</tr>
				</table>
				<table width="100%">
					<tr>
						<td class="td1" width="100">网站信息</td>
						<td class="td1" width="200">&nbsp;</td>
						<td class="td1">&nbsp;</td>
					</tr>
					<tr>
						<td class="tar">网站名称：</td>
						<td><input type="text" name="sitename" value="<?php echo $config['siteName'] ?>" class="input"></td>
						<td><div id="J_install_tip_sitename"></div></td>
					</tr>
					<tr>
						<td class="tar">管理员帐号：</td>
						<td><input type="text" name="manager" value="" class="input"></td>
						<td><div id="J_install_tip_manager"></div></td>
					</tr>
					<tr>
						<td class="tar">密码：</td>
						<td><input type="text" name="manager_pwd" id="J_manager_pwd" class="input" autoComplete="off"></td>
						<td><div id="J_install_tip_manager_pwd"></div></td>
					</tr>
					<tr>
						<td class="tar">重复密码：</td>
						<td><input type="text" name="manager_ckpwd" class="input" autoComplete="off"></td>
						<td><div id="J_install_tip_manager_ckpwd"></div></td>
					</tr>
				</table>
				<div id="J_response_tips" style="display:none;"></div>
			</div>
		</div>
		<div class="btn-box">
			<a href="./index.php?step=2" class="btn">上一步</a>
			<button type="submit" class="btn btn_submit J_install_btn">创建数据</button>
		</div>
	</form>
	<script src="./templates/js/jquery.js"></script> 
	<script src="./templates/js/validate.js"></script> 
	<script>
		function TestDbPwd(){
			$.ajax({
				type: "POST",
				dataType:'json',
				url: "./index.php?step=3&testdbpwd=1",
				data: {'dbhost':$('#dbhost').val(),'dbuser':$('#dbuser').val(),'dbpw':$('#dbpw').val(),'dbname':$('#dbname').val(),'dbport':$('#dbport').val()},
				success: function(data){
					if(data.status != 1){
						$('#'+ data.type).focus();
					}
					alert(data.info);
				},
				error:function(){
					alert('数据库链接配置失败');
					$('#dbpw').focus();
				}
			});
		}
	$(function(){
		//聚焦时默认提示
		var focus_tips = {
			dbhost : '数据库服务器地址，一般为localhost',
			dbport : '数据库服务器端口，一般为3306',
			dbuser : '请输入数据库用户名',
			dbpw : '请输入数据库密码',
			dbname : '请输入数据库名',
			dbprefix : '建议使用默认，同一数据库安装多个时需修改',
			manager : '创始人帐号，拥有站点后台所有管理权限',
			manager_pwd : '请输入管理员密码',
			manager_ckpwd : '请再次输入管理员密码',
			sitename : '请再次输入网站名称',
			sitekeywords : '',
			siteinfo : '',
			manager_email : ''
		};


		var install_form = $("#J_install_form"),
			response_tips = $('#J_response_tips');				//后端返回提示

		install_form.validate({
			//debug : true,
			//onsubmit : false,
			errorPlacement: function(error, element) {
				//错误提示容器
				$('#J_install_tip_'+ element[0].name).html(error);
			},
			errorElement: 'span',
			errorClass : 'tips_error',
			validClass		: 'tips_error',
			onkeyup : false,
			focusInvalid : false,
			rules: {
				dbhost: {
					required	: true
				},
				dbport:{
					required	: true
				},
				dbuser: {
					required	: true
				},
				dbname: {
					required	: true
				},
				dbprefix : {
					required	: true
				},
				manager: {
					required	: true
				},
				manager_pwd: {
					required	: true,
					minlength   : 6
				},
				sitename: {
					required	: true,
				},
				manager_ckpwd: {
					required	: true,
					equalTo : '#J_manager_pwd'
				},
				manager_email: {
					required	: true,
					email : true
				}
			},
			highlight	: false,
			unhighlight	: function(element, errorClass, validClass) {
				var tip_elem = $('#J_install_tip_'+ element.name);

					tip_elem.html('<span class="'+ validClass +'" data-text="text"><span>');

			},
			onfocusin	: function(element){
				var name = element.name;
				$('#J_install_tip_'+ name).html('<span data-text="text">'+ focus_tips[name] +'</span>');
				$(element).parents('tr').addClass('current');
			},
			onfocusout	:	function(element){
				var _this = this;
				$(element).parents('tr').removeClass('current');
				
				if(element.name === 'email') {
					//邮箱匹配点击后，延时处理
					setTimeout(function(){
						_this.element(element);
					}, 150);
				}else{
				
					_this.element(element);
					
				}
				
			},
			messages: {
				dbhost: {
					required	: '数据库服务器地址不能为空'
				},
				dbport:{
					required	: '数据库服务器端口不能为空'
				},
				dbuser: {
					required	: '数据库用户名不能为空'
				},
				dbname: {
					required	: '数据库名不能为空'
				},
				dbprefix : {
					required	: '数据库表前缀不能为空'
				},
				sitename : {
					required	: '请再次输入网站名称'
				},
				manager: {
					required	: '管理员帐号不能为空'
				},
				manager_pwd: {
					required	: '密码不能为空',
					minlength   : '密码至少6位'
				},
				manager_ckpwd: {
					required	: '请再次输入管理员密码',
					equalTo : '两次输入的密码不一致。请重新输入'
				},
				manager_email: {
					required	: 'Email不能为空',
					email : '请输入正确的电子邮箱地址'
				}
			},
			submitHandler:function(form) {
				$.ajax({
					type: "POST",
					dataType:'json',
					url: "./index.php?step=3&testdbpwd=1",
					data: {'dbhost':$('#dbhost').val(),'dbuser':$('#dbuser').val(),'dbpw':$('#dbpw').val(),'dbname':$('#dbname').val(),'dbport':$('#dbport').val()},
					success: function(data){
						if(data.status != 1){
							alert(data.info);
							$('#'+ data.type).focus();
							return;
						}
						form.submit();
						return true;
					},
					error:function(){
						alert('数据库链接配置失败');
						$('#dbpw').focus();
						return;
					}
				});
			}
		});
	});
	</script> 
<?php require './templates/footer.php';?>