<?php
$shopname = isset($_REQUEST['shopname'])?$_REQUEST['shopname']:'';
$murl = "login.php?action=curlmember&shopname=".$shopname;
$purl = "login.php?action=curlpackage&shopname=".$shopname;
$surl = "login.php?action=curlstaff&shopname=".$shopname;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="public/bootstrap.css">

<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="public/bootstrap-theme.css">

<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="public/jquery.js"></script>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="public/bootstrap.js"></script>
<title>美管家移动商务平台会员信息采集</title>
</head>

<body>

<nav class="navbar navbar-default" role="navigation">
   <div class="navbar-header" style="margin-left: 300px;">
      <a class="navbar-brand">美管家移动商务平台会员信息采集</a>
   </div>
</nav>

<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header" style="margin-left: 300px;">
		<a class="navbar-brand" href="<?php echo $murl;?>" target="_blank">下载会员信息</a>
		<a class="navbar-brand" href="<?php echo $purl;?>" target="_blank">下载套餐信息</a>
        <a class="navbar-brand" href="<?php echo $surl;?>" target="_blank">下载员工信息</a>
	</div>
</nav>

<!-- 按钮触发模态框 -->
<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" style="display:none">
   请输入验证码
</button>

</body>
</html>