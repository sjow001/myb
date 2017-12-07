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
<title>盛传移动商务平台 by:Marking</title>
</head>

<body>

<nav class="navbar navbar-default" role="navigation">
   <div class="navbar-header">
      <a class="navbar-brand">盛传移动商务平台会员信息采集</a>
   </div>
</nav>

<!-- 按钮触发模态框 -->
<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" style="display:none">
   请输入验证码
</button>
<div id="content">
	
</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" 
               aria-hidden="true">
            </button>
            <h4 class="modal-title" id="myModalLabel">
               请登陆
            </h4>
         </div>
         <div class="modal-body">
		 <input type="text" class="form-control" id="txtName" placeholder="请输入旺点账号" value=""><br>
		 <input type="text" class="form-control" id="txtPassword" placeholder="请输入旺点账号密码" value=""><br>
            <img src="loginwd.php?action=code"><br><input type="text" class="form-control" id="txtCheckCode" placeholder="请输入上方验证码">
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" 
               data-dismiss="modal">关闭
            </button>
            <button type="button" class="btn btn-primary" id="post_btn" data-loading-text="Loading...">
               提交
            </button>
         </div>
      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</body>
</html>
<script>
	$(function() { 
		$("#myModal").modal('show');
		$("#post_btn").click(function(){
			$(this).button('loading').delay(1000).queue(function() {
				var txtName = $("#txtName").val();
				var txtPassword = $("#txtPassword").val();
				var txtCheckCode = $("#txtCheckCode").val();
				var url = "loginwd.php?action=login"
				$.ajax({
					url:url,
					dataType:"text",
					type:"post",
					data:{"txtName":txtName,"txtPassword":txtPassword,"txtCheckCode":txtCheckCode},
					error:function(request){
						alert(request);
						$("#login_btn").button('reset');
					},
					success:function(msg){
						if(msg == "1"){
							alert("登陆成功，点击确定进入下载页面");
							location.href='dataDown.php?shopname='+$('#login').val();
						}else{
							alert(msg);
							$("#login_btn").button('reset');
						}
					}
				})
			});        
		});
	});
</script>