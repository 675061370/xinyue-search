<?php require './templates/header.php';?>
	<div class="section">
		<div class="main">
			<div class="success_tip cc"> 
				<a href="../qfadmin">安装完成，进入后台管理</a>
				<?php if(INSTALLTYPE == 'HOST'){ ?>
				<p><?php echo $config['alreadyInstallInfo']?><p>
				<?php } else if(INSTALLTYPE == 'SAE'){ ?>
				<p><?php echo $config['alreadySaeInstallInfo']?><p>	
				<?php } else if(INSTALLTYPE == 'BAE'){ ?>
				<p><?php echo $config['alreadyBaeInstallInfo']?><p>
				<?php  } ?>
			</div>
		</div>
	</div>
	<div class="btn-box">
		<a href="../index" class="btn">进入前台</a>
		<a href="../qfadmin" class="btn">进入后台</a>
	</div>
<?php require './templates/footer.php';?>