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
	$curl -> url = "http://sh.imeiyebang.com//manage/checknum.jpg?token=302123ff-f94e-4cb3-b71d-a0b220f0abf4";
	echo $curl -> get_code();
}else if($_GET['action'] == "login"){
	$token = '302123ff-f94e-4cb3-b71d-a0b220f0abf4';
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
		echo "密码错误！";
	}
}else if($_GET['action'] == 'curlmember'){
	$shopname = $_REQUEST['shopname'];
	$data = '';

    //获取总数
    $curl -> url = "http://vip8.meiguanjia.net/shair/memberInfo!memberlist.action?set=cash";
    $rs = $curl -> curl();
    preg_match('/共(.*)条/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;
    //总页数
    $pages = ceil($totals/100);
	for($i=1; $i<=$pages; $i++){
		$params = "page.currNum=$i&page.rpp=100&set=cash";
		$curl -> params = $params;
		$curl -> url = "http://vip8.meiguanjia.net/shair/memberInfo!memberlist.action?set=cash";
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
    $curl -> url = "http://vip8.meiguanjia.net/shair/timesItem!initTreat.action?set=cash";
    $rs = $curl -> curl();
    preg_match('/共(.*)条/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;

	//总页数
    $pages = ceil($totals/100);
    for($i=1; $i<=$pages; $i++){
        $params = "page.currNum=$i&page.rpp=100&set=cash&r=0.3421386775783387";
        $curl -> params = $params;
        $curl -> url = "http://vip8.meiguanjia.net/shair/timesItem!initTreat.action";
        $pagesData = $curl -> getPackagePage();
        $data .= $curl ->getPackageInfo($pagesData, $i);
    };
    if($data == '') {
        header('Location: index.php');
    }
    $curl -> downPackageCvs($data, $shopname);
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