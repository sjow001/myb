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

$cookie = "Qs_lvt_220478=1531895850; Qs_pv_220478=4444168644555327500; Hm_lvt_f2de904b45de5fa1c111346641b534e8=1531895851; Hm_lpvt_f2de904b45de5fa1c111346641b534e8=1531895851; _ga=GA1.2.2144711309.1531895852; _gid=GA1.2.942109393.1531895852; spoor_uid=62c9f5d69c9a4b758f4c4206dbfc555c; ticket=a7ef172b-b11d-41e5-b4a1-ce4efd96ff63; spoor_login_account_code=CLERKSLOGINACCOUNT2017062913031605463449; spoor_company_code=COMPANIESCOMPANY2017062913031606253451; spoor_shop_code=-; JSESSIONID=71D37B45C6E984EC06DB111D702A4EB1; Hm_lvt_4e5bdf78b2b9fcb88736fc67709f2806=1531895855; Hm_lpvt_4e5bdf78b2b9fcb88736fc67709f2806=1531895974";
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
    $shopname = '18602900617';
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