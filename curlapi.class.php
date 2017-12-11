<?php
/**************************************
* Project Name:盛传移动商务平台
* Time:2016-03-22
* Author:MarkingChanning QQ:380992882
**************************************/
error_reporting(0);
require 'querylist/phpQuery.php';
require 'querylist/QueryList.php';
use QL\QueryList;

class curlapi{
	public $url; //提交地址
	public $params; //登入的post数据
	public $cookies=""; //cookie
	public $referer=""; //http referer
	
	/*
		获取验证码
	*/
	public function get_code(){
		$ch = curl_init($this -> url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		preg_match("/Cookie:(.*);/siU", $output, $arr);

		$cookies = $arr[1];

		//cookies存SESSION
		session_start();
		$_SESSION['cookies'] = $cookies;
		//截取GIF二进制图片
		$explode = explode("GMT",$output);

		return $explode = trim($explode[2]);
	}
	
	/*
		模拟登陆
	*/
	public function login(){
		session_start();
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$this -> url);
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
		curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	/*
		curl模拟采集数据
	*/
	public function curl($cookie){
		session_start();
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$this -> url);
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_COOKIE,$cookie);
		curl_setopt ($ch, CURLOPT_REFERER,$this -> referer);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/*
    curl模拟采集数据，会员数据
	*/
	public function getMembersPage($cookie){
		session_start();
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$this -> url);
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_COOKIE,$cookie);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
		curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
		curl_setopt ($ch, CURLOPT_REFERER,$this -> referer);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/*
	curl模拟采集数据，会员一些详细数据
	*/
	public function getMembersInfos(){
		session_start();
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$this -> url);
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
		curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/**分析会员数据
	 * @param $rs
	 * @param $page
	 * @return mixed|string
	 */
	public function getMembersInfo($rs, $page){
		$rsBlank = preg_replace("/\s\n\t/","",$rs);
		//$rsBlank = str_replace(' ', '', $rsBlank);
		preg_match_all("/table-striped.*>(.*)<\/table>/isU", $rsBlank ,$tables);
		if(isset($tables[1][0])) {
			if($page>1) {
				return preg_replace("/<thead[^>]*>.*<\/thead>/isU", '', $tables[1][0]);
			} else {
				return preg_replace("/<thead[^>]*>.*<\/thead>/isU", '', $tables[1][0]);
				//return $tables[1][0];
			}
		} else {
			return '';
		}
		return $tables[1][0];
	}

    /**
     * 获取会员信息下载到CVS
     * @param $html
     * @param $shopname
     */
	public function downMembersCvs($html,$shopname,$cookie){
		$rules = array(
			//采集tr中的纯文本内容
			'other' => array('tr','html'),
		);
		$newdata = array();
		$data = QueryList::Query($html, $rules)->data;
		$k = 1;
		foreach ($data as &$item) {
			$other = explode('</td>', $item['other']);
			if(count($other) > 9) {
				//unset($other[0]);//去掉第一空白项
                //unset($other[14]);//去掉14项
                //unset($other[15]);//去掉15项
                //unset($other[18]);//去掉15项
				$item['other'] = $other;

				//获取基本信息
				preg_match('/code=(.*)\"/isU', $other[8], $code);
				$code = $code[1];
				$this -> url = "http://sh.imeiyebang.com/report/customer/info.jhtml?code=$code";
				$baseinfo = $this -> curl($cookie);
				//卡信息
				$this -> url = "http://sh.imeiyebang.com/report/customer/info.jhtml?code=$code&type=balance";
				$balance = $this -> curl($cookie);
				$balance = $this->getMembersInfo($balance, 1);
				$balance = explode('</td>', $balance);
				foreach ($balance as &$vb) {
					$vb = strip_tags($vb);;
					$vb = preg_replace("/\s\n\t/","",$vb);
					$vb = str_replace(' ', '', $vb);
					$vb= trim(str_replace(PHP_EOL, '', $vb));
				}

				foreach ($other as &$v1) {
					$v1 = strip_tags($v1);;
					$v1 = preg_replace("/\s\n\t/","",$v1);
					$v1 = str_replace(' ', '', $v1);
					$v1= trim(str_replace(PHP_EOL, '', $v1));
				}


				ksort($other);
				$newdata[$k][0] = "\t".$k; //卡号
				$newdata[$k][1] = $other[2]; //姓名
				$newdata[$k][2] = $other[4]; //手机号

				//性别
				//$newdata[$k][3] = $other[3] == '男'?0:1; //性别
				$newdata[$k][3] = 1; //性别
				if(preg_match("/女/",$baseinfo)){
					$newdata[$k][3] = 1; //性别
				}
				if(preg_match("/男/",$baseinfo)){
					$newdata[$k][3] = 0; //性别
				}
				//卡类型
				$newdata[$k][4] = ''; //卡类型

				$newdata[$k][5] = ''; //折扣

				//卡金余额信息,
				$newdata[$k][6] = (int)$balance[2]; //卡余额
				$newdata[$k][12] = (int)$balance[4]; //欠款
				$newdata[$k][7] = (int)$balance[0]; //充值总额
				$newdata[$k][9] = 0; //消费总额
				$newdata[$k][10] = 0; //赠送金
				$newdata[$k][8] = 0; //消费次数
				$newdata[$k][11] = 0; //积分
				$newdata[$k][13] = date('Y-m-d', strtotime($other[7])); //开卡时间

				$newdata[$k][14] = ''; //最后消费时间
				$br = explode("(",$other[3]);
				$newdata[$k][15] = $br[0]; //生日
				$newdata[$k][16] = '1'; //生日类型（1阳历 公里，0阴历 农历）
				if(preg_match("/阴历/", $other[3])){
					$newdata[$k][16] = '0';
				}
				$newdata[$k][17] = ''; //会员备注
				ksort($newdata[$k]);

				//调试
				if($other[2] == '陈瑶'){
				}
				//调试

				$k++;
			}
		}

		//导出CVS
		$cvsstr = "卡号(必填[唯一]),姓名(必填),手机号(必填[唯一]),性别(必填[“0”代表男，“1”代表女]),卡类型(必填[系统编号]),折扣(必填),卡金余额(必填),充值总额,消费次数,消费总额,赠送金,积分,欠款,开卡时间(格式：YYYY-mm-dd),最后消费时间(格式：YYYY-mm-dd),生日(格式：YYYY-mm-dd),生日类型（1阳历，0阴历）,会员备注\n";
		$filename = $shopname.'_会员信息.csv';
		$cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);

		foreach($newdata as &$v){
			foreach($v as $k=>&$v1){
				//转码
				$cvsdata = iconv('utf-8','gb2312//ignore',$v1);
				$cvsstr .= $cvsdata; //用引文逗号分开
				if($k < 19) {
					$cvsstr .= ","; //用引文逗号分开
				}
			}
			$cvsstr .= "\n";
		}
		header("Content-type:text/csv");
		header("Content-Disposition:attachment;filename=".$filename);
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0');
		header('Pragma:public');
		echo $cvsstr;
	}

	/*
	curl模拟采集数据，会员套餐数据
	*/
	public function getPackagePage(){
		session_start();
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$this -> url);
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_COOKIE,$_SESSION['cookies']);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$this -> params);
		curl_setopt ($ch, CURLOPT_REFERER,$this -> url);
		curl_setopt ($ch, CURLOPT_REFERER,$this -> referer);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}

    /**
     *获取套餐页面数据
     */
    public function getPackageInfo($rs, $page){
        $rsBlank = preg_replace("/\s\n\t/","",$rs);
        //$rsBlank = str_replace(' ', '', $rsBlank);
        preg_match_all("/table-responsive.*>(.*)<\/form>/isU", $rsBlank ,$tables);
        if(isset($tables[1][0])) {
            if($page>1) {
                return preg_replace("/<thead[^>]*>.*<\/thead>/isU", '', $tables[1][0]);
            } else {
                return $tables[1][0];
            }
        } else {
            return '';
        }
        return $tables[1][0];
    }

    /**
     * 获取会员套餐信息下载到CVS
     * @param $html
     * @param $shopname
     */
    public function downPackageCvs($html,$shopname, $cookie){

		$rules = array(
			//采集tr中的纯文本内容
			'other' => array('tr','html'),
		);
		$newdata = array();
		$data = QueryList::Query($html, $rules)->data;
		$k = 1;
		foreach ($data as &$item) {
			$other = explode('</td>', $item['other']);
			if(count($other) > 9) {
				//unset($other[0]);//去掉第一空白项
				//unset($other[14]);//去掉14项
				//unset($other[15]);//去掉15项
				//unset($other[18]);//去掉15项
				$item['other'] = $other;

				//获取基本信息
				preg_match('/code=(.*)\"/isU', $other[8], $code);
				$code = $code[1];
				//卡信息
				$this -> url = "http://sh.imeiyebang.com/report/customer/info.jhtml?code=$code&type=cards";
				$cards = $this -> curl($cookie);

				$rules = array(
					//采集tr中的纯文本内容
					'other' => array('tr','html'),
				);
				$cards = QueryList::Query($cards, $rules)->data;
				unset($cards[0]);

				foreach ($other as &$v1) {
					$v1 = strip_tags($v1);;
					$v1 = preg_replace("/\s\n\t/","",$v1);
					$v1 = str_replace(' ', '', $v1);
					$v1= trim(str_replace(PHP_EOL, '', $v1));
				}

				foreach($cards as $card){
					$card = explode('</td>', $card['other']);
					foreach ($card as &$vc) {
						$vc = strip_tags($vc);;
						$vc = preg_replace("/\s\n\t/","",$vc);
						$vc = str_replace(' ', '', $vc);
						$vc= trim(str_replace(PHP_EOL, '', $vc));
					}

					$newA[0] = $other[4]; //手机号
					$newA[1] = "\t".$k; //卡号
					$newA[2] = $other[2]; //姓名
					$newA[3] = ''; //卡名称
					$newA[4] = ''; //卡类型

					//$v2 .= "#";
					//获取项目套餐信息
					$newA[5] = '';//项目编号
					$newA[6] = $card[0];//项目名称
					$newA[7] = $card[3];//总次数
					$newA[8] = $card[5];//剩余次数
					$newA[9] = intval($card[2]/$card[3]); //单次消费金额
					$newA[10] = intval($newA[9]*$card[5]); //剩余金额
					$newA[11] = '';//失效日期

					$newA[12] = $newA[8];//总剩余次数
					$newA[13] = $newA[10]; //总剩余金额
					$newA[14] = '';
					$newdata[] = $newA;
				}
				$k++;
			}
		}

		//导出CVS
		$cvsstr = "手机号,卡号,姓名,卡名称,卡类型,项目编号,项目名称,总次数,剩余次数,单次消费金额,剩余金额,失效日期,总剩余次数,总剩余金额\n";
		$filename = $shopname.'_会员套餐信息.csv';
		$cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);
		foreach($newdata as &$v){
			foreach($v as $k=>&$v1){
				//时间转换
				if($k == 5 || $k == 19) {
					//$v1 = strtotime($v1);
				}
				//转码
				$cvsdata = iconv('utf-8','gb2312//ignore',$v1);
				$cvsstr .= $cvsdata; //用引文逗号分开
				if($k < 14) {
					$cvsstr .= ","; //用引文逗号分开
				}
			}
			$cvsstr .= "\n";
		}
		header("Content-type:text/csv");
		header("Content-Disposition:attachment;filename=".$filename);
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0');
		header('Pragma:public');
		echo $cvsstr;
    }

	/**
	 * 获取员工信息下载到CVS
	 * @param $html
	 * @param $shopname
	 */
	public function downStaffCvs($html,$shopname){
		$rules = array(
			//采集tr中的纯文本内容
			'other' => array('tr','html'),
		);
		$newdata = array();
		$data = QueryList::Query($html, $rules)->data;
		foreach ($data as $k=>&$item) {
			$other = explode('</td>', $item['other']);
			if(count($other) > 8) {
				//unset($other[0]);//去掉第一空白项
				$item['other'] = $other;
				foreach ($other as $k1 => &$v1) {
					$v1 = strip_tags($v1);;
					$v1 = preg_replace("/\s\n\t/","",$v1);
					$v1 = str_replace(' ', '', $v1);
					$v1= trim(str_replace(PHP_EOL, '', $v1));
				}

				$date1 = substr($other[11], 0, 3).' '.substr($other[11], 3, 3).' '.substr($other[11], 19, 4);
				$date1 = date('Y-m-d', strtotime($date1));
				$newdata[$k][0] = "\t".$other[1];
				$newdata[$k][1] = $other[2];
				$newdata[$k][2] = $other[3];
				$newdata[$k][3] = preg_match('/男/', $other[4])?0:1;
				$newdata[$k][4] = $other[9];
				$newdata[$k][5] = str_replace('阴', '', $other[10]);
				$newdata[$k][5] = str_replace('阳', '', $newdata[$k][5]);
				$newdata[$k][5] = str_replace('"', '', $newdata[$k][5]);
				$newdata[$k][6] = $date1;
				$newdata[$k][7] = $other[8];
				$newdata[$k][8] = '';

				//日期格式含有1900，设置为空
				if(preg_match("/1900/isU", $newdata[$k][5])) {
					$newdata[$k][5] = '';
				}
			}
		}
		unset($newdata[count($newdata)]);
		unset($newdata[count($newdata)]);

		//导出CVS
		$cvsstr = "编号(必填[唯一]),姓名(必填),级别(必填),性别,手机号码,生日,入职时间,身份证号,银行账号\n";
		$filename = $shopname.'_员工信息.csv';
		$cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);

		foreach($newdata as &$v){
			foreach($v as $k=>&$v1){
				//转码
				$cvsdata = iconv('utf-8','gb2312//ignore',$v1);
				$cvsstr .= $cvsdata; //用引文逗号分开
				if($k < 8) {
					$cvsstr .= ","; //用引文逗号分开
				}
			}
			$cvsstr .= "\n";
		}
		header("Content-type:text/csv");
		header("Content-Disposition:attachment;filename=".$filename);
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0');
		header('Pragma:public');
		echo $cvsstr;
	}
}

?>