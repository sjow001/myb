<?php
/**************************************
* Project Name:盛传移动商务平台
* Time:2016-03-22
* Author:MarkingChanning QQ:380992882
**************************************/
set_time_limit(0);
header("Content-Type: text/html;charset=utf-8");
include_once("curlapi.class.php");
$curl = new curlapi();
if($_GET['action'] == "code"){//获取验证码
	$curl -> url = "http://js-6.aimeifa.com:7003/JTMISWebClient/webCheckCode.aspx";
	echo $curl -> get_code();
}else if($_GET['action'] == "login"){
	$txtName = urlencode($_POST['txtName']);
	$txtPassword = $_POST['txtPassword'];
	$txtCheckCode = $_POST['txtCheckCode'];
	$params = "txtName={$txtName}&txtPassword={$txtPassword}&txtCheckCode={$txtCheckCode}";
	$curl -> url = "http://js-6.aimeifa.com:7003/JTMISWebClient/webUserLogin.aspx";
	$curl -> params = $params;
	$result = $curl -> login();
	echo "<pre>";
	print_r($result);
	echo "</pre>";
	exit;
	$result = json_decode($result,true);
	if($result['code'] == 4){
		echo "验证码错误！";
	}else if($result['code'] == 5){
		echo "不存在的账号！";
	}else if($result['code'] == 6){
		echo "密码错误！";
	}else if($result['role']){
		echo 1;
	}
}else if($_GET['action'] == 'curlmember'){
	$shopname = $_REQUEST['shopname'];
	$data = '';

    //获取总数
    $curl -> url = "http://vip8.sentree.com.cn/shair/memberInfo!memberlist.action?set=cash&r=0.3168503969933729";
    $rs = $curl -> curl();
    preg_match('/共(.*)条/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;
    //总页数
    $pages = ceil($totals/100);
	for($i=1; $i<=$pages; $i++){
		$params = "page.currNum=$i&page.rpp=100&set=cash&r=0.3421386775783387";
		$curl -> params = $params;
		$curl -> url = "http://vip8.sentree.com.cn/shair/memberInfo!memberlist.action?set=cash&r=0.3168503969933729";
		$pagesData = $curl -> getMembersPage();
		$data .= $curl ->getMembersInfo($pagesData, $i);
	};
    if($data == '') {
        header('Location: index.php');
    }
	$curl -> downMembersCvs($data, $shopname);
}else if($_GET['action'] == 'curlpackage'){
    $shopname = $_REQUEST['shopname'];
    $data = '';

    //获取总数
    $curl -> url = "http://vip8.sentree.com.cn/shair/timesItem!initTreat.action?set=cash";
    $rs = $curl -> curl();
    preg_match('/共(.*)条/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;
    //总页数
    $pages = ceil($totals/100);
    for($i=1; $i<=$pages; $i++){
        $params = "page.currNum=$i&page.rpp=100&set=cash&r=0.3421386775783387";
        $curl -> params = $params;
        $curl -> url = "http://vip8.sentree.com.cn/shair/timesItem!initTreat.action";
        $pagesData = $curl -> getPackagePage();
        $data .= $curl ->getPackageInfo($pagesData, $i);
    };
    if($data == '') {
        header('Location: index.php');
    }
    $curl -> downPackageCvs($data, $shopname);
}
?>