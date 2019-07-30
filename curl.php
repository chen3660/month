<?php

$curl = curl_init();

$url = "https://www.17k.com/top/refactor/top100/06_vipclick/06_vipclick_cnl_man_top_100_pc.html";

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

$result = curl_exec($curl);


$req = '#.*<tr>.*<td width="30">1</td>.*<td width="60"><a href=".*" target=".*">(.*)</a></td><td><a class="red" href="(.*)" title="(.*)" target=".*">.*</a></td><td><a href=".*" title=".*" target=".*">.*</a></td>.*</tr>.*#isU';

preg_match_all($req, $result, $arr);

$title = $arr[3];
$href = $arr[2];
$type = $arr[1];

$urlText = "https://www.17k.com/list/1859126.html";

curl_setopt($curl, CURLOPT_URL, $urlText);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

$resultText = curl_exec($curl);

$reqText = '#.*	<a target="_blank"
					   href="(.*)"
					   title=".*">
							<span class="ellipsis ">
																	(.*)
															</span>
					</a>.*#isU';

preg_match_all($reqText, $resultText, $arrText);


$books_section = '';


foreach($arrText[2] as $k => $v){
    $books_section.= $v;
}


$urlDesc= 'https://www.17k.com/chapter/1859126/25186231.html';

curl_setopt($curl, CURLOPT_URL, $urlDesc);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

$resultDesc = curl_exec($curl);

$reqDesc = '#.*<p>(.*)</p>.*#isU';
preg_match_all($reqDesc, $resultDesc, $arrDesc);


$books_desc = '';

for($i = 0;$i<=206;$i++){
    $books_desc.= $arrDesc[1][$i];
}


$dbh = new PDO('mysql:host=localhost;dbname=month', 'root', 'root');
$sql = "insert into `books`(`books_title`,`books_href`,`books_type`,`books_section`,`books_desc`) values";
$val = '';

foreach($title as $k=> $v){

    $val .= "('$v','$href[$k]','$type[$k]','$books_section','$books_desc'),";

}
$val = substr($val,0,-1);

$sql .= $val;



$res = $dbh -> exec($sql);

var_dump($res);





