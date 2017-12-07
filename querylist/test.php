<?php
header("Content-Type: text/html;charset=utf-8");
require 'phpQuery.php';
require 'QueryList.php';
use QL\QueryList;

$rules = array(
    //采集tr中的纯文本内容
    'other' => array('tr','html'),
);
$newdata = array();
$data = QueryList::Query('http://localhost/scvip/querylist/test.html', $rules)->data;
foreach ($data as $k=>&$item) {
    $other = explode('</td>', $item['other']);
    if(count($other) > 15) {
        $item['other'] = $other;
        foreach ($other as &$v1) {
            $v1 = strip_tags($v1);;
            $v1 = preg_replace("/\s\n\t/","",$v1);
            $v1 = str_replace(' ', '', $v1);
            $v1= trim(str_replace(PHP_EOL, '', $v1));
        }
        $newdata[] = $other;
    }
}

//导出CVS
$cvsstr = '';
$filename = 'test.csv';
$cvsstr = iconv('utf-8','gb2312//ignore',$cvsstr);
foreach($newdata as &$v){
    foreach($v as $k=>&$v1){
        //时间转换
        if($k == 5 || $k == 19) {
            //$v1 = strtotime($v1);
        }
        //转码
        $cvsdata = iconv('utf-8','gb2312//ignore',$v1);
        if($k < 20) {
            $cvsstr .= $cvsdata.","; //用引文逗号分开
        }
    }
    $cvsstr .= "\n";
}
echo "<pre>";
print_r($newdata);
echo "</pre>";
exit;
header("Content-type:text/csv");
header("Content-Disposition:attachment;filename=".$filename);
header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
header('Expires:0');
header('Pragma:public');
echo $cvsstr;
?>