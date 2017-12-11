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

$cookie = "spoor_uid=731173e3998e4388b2599d122d5d898a; JSESSIONID=A8673246EB050EB130A747D5DC4CAC91; ticket=8e48c4ac-2b33-485f-8dfd-292ad35d647d; spoor_login_account_code=LOGINACCOUNTUSERS98100; spoor_company_code=COMPANYCOMPANIES2681; spoor_shop_code=-";
if($_GET['action'] == "code"){//获取验证码
	$token = '75300908-86b0-4030-8f45-51159de724b3';
	$curl -> url = "http://sh.imeiyebang.com/manage/checknum.jpg?token=$token";
	echo $curl -> get_code();
}else if($_GET['action'] == "login"){
	$token = '95bb19ac3-6bbd-463b-8bde-10dd9c834cef';
	$login = urlencode($_POST['login']);
	$passwd = $_POST['passwd'];
	$rand = $_POST['rand'];
	$params = "token={$token}&username={$login}&password={$passwd}&ckecknum={$rand}";
	$curl -> url = "http://sh.imeiyebang.com/manage/login.jhtml";
	$curl -> params = $params;
	$result = $curl -> login();
	$result = json_decode($result,true);
	if($result['code'] == 1000000){
		echo 1;
	}else{
		echo "账户密码错误！";
	}
}else if($_GET['action'] == 'curlmember'){
	$shopname = $_REQUEST['shopname'];
	$data = '';

    //获取总数
    $curl -> url = "http://sh.imeiyebang.com/report/customer/list.jhtml";
    $rs = $curl -> curl($cookie);
	//$rs=iconv( "GBK","UTF-8",$rs);
	preg_match('/recordCount=\"(.*)\"/isU', $rs, $totals);
	$totals = isset($totals[1])?$totals[1]:100;

    //总页数
    $pages = ceil($totals/100);
	//$pages = 1;

	for($i=1; $i<=$pages; $i++){
		$params = "pageIndex=$i&pageSize=400";
		$curl -> params = $params;
		$curl -> url = "http://sh.imeiyebang.com/report/customer/list.jhtml";
		$pagesData = $curl -> getMembersPage($cookie);
		$data .= $curl ->getMembersInfo($pagesData, $i);
	};

    if($data == '') {
        header('Location: index.php');
    }
	$curl -> downMembersCvs($data, $shopname,$cookie);
}else if($_GET['action'] == 'curlpackage'){
	$shopname = $_REQUEST['shopname'];
	$data = '';

	//获取总数
	$curl -> url = "http://sh.imeiyebang.com/report/customer/list.jhtml";
	$rs = $curl -> curl($cookie);
	//$rs=iconv( "GBK","UTF-8",$rs);
	preg_match('/recordCount=\"(.*)\"/isU', $rs, $totals);
	$totals = isset($totals[1])?$totals[1]:100;

	//总页数
	$pages = ceil($totals/100);
	//$pages = 1;

	for($i=1; $i<=$pages; $i++){
		$params = "pageIndex=$i&pageSize=400";
		$curl -> params = $params;
		$curl -> url = "http://sh.imeiyebang.com/report/customer/list.jhtml";
		$pagesData = $curl -> getMembersPage($cookie);
		$data .= $curl ->getMembersInfo($pagesData, $i);
	};

	if($data == '') {
		header('Location: index.php');
	}
	$curl -> downPackageCvs($data, $shopname,$cookie);
}else if($_GET['action'] == 'curlstaff'){
	$shopname = $_REQUEST['shopname'];
	$data = '';

	//获取员工数据
	$curl -> url = "http://vip8.meiguanjia.net/shair/employee!employeeInfo.action?set=manage&r=0.5704847458180489";
	$rs = $curl -> curl();

	$rsBlank = preg_replace("/\s\n\t/","",$rs);
	//$rsBlank = str_replace(' ', '', $rsBlank);
	preg_match_all("/table_fixed_head.*>(.*)<\/form>/isU", $rsBlank ,$tables);

    if(count($tables[0]) == 0) {
        header('Location: index.php');
    }
	$curl -> downStaffCvs($tables[1][0], $shopname);
}
?>