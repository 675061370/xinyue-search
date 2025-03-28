<?php require './templates/header.php';?>
	<div class="section">
		<div class="main install">
			<ul id="loginner"></ul>
		</div>
	</div>
	<div class="btn-box">
		<a href="javascript:;" class="btn_old" id="installloading">
			<img src="templates/images/loading.gif" align="absmiddle">&nbsp;正在安装...
		</a>
	</div>
	<script src="./templates/js/jquery.js"></script> 
	<script type="text/javascript">
		var n=0;
		var data = <?php echo json_encode($_POST);?>;
		$.ajaxSetup ({ cache: false });
		function reloads(n) {
			var url =	"./index.php?step=4&install=1&n="+n;
			$.ajax({
				type: "POST",		
				url: url,
				data: data,
				dataType: 'json',
				success: function(data){
					$('#loginner').append(data.info);
					if(data.status == 1){
						reloads(data.type);
					}
					if(data.status == 0){
						$('#installloading').removeClass('btn_old').addClass('btn').html('继续安装').unbind('click').click(function(){
							reloads(0);
						});
						alert('安装已停止！');
					}
					if(data.status == 2){
						$('#installloading').removeClass('btn_old').addClass('btn').attr('href','./index.php?step=5').html('安装完成');
						setTimeout(function(){
							window.location.href='./index.php?step=5';
						},5000);
					}
				}
			});
		}
		$(function(){
			reloads(n);
		})
	</script> 
<?php require './templates/footer.php';?>