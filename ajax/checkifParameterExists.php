<?php
$fname			=	urlencode($_GET['fname']);
$signupemail            =	urlencode($_GET['signupemail']);
$passwd			=	urlencode($_GET['passwd']);
$signupurl 		= 	urlencode($_GET['signupurl']);
$domain = "http://www.timepicks.com/";
$param="?firstName={$fname}&email={$signupemail}&password={$passwd}&accountURL={$signupurl}&instanceId=''&instance=''&compId=''&origCompId=''";

//die('Param is '.$param);
		$apiurl = $domain."Application/registration/processAccount_wppluginUser.php";
		$apiurl .= $param;
		
		// make call
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_URL, $apiurl);
		curl_setopt($ch,CURLOPT_TIMEOUT, 120);
		$pageresults = curl_exec($ch);
		curl_close($ch);
		//echo $apiurl;
		//echo  $pageresults;
//		$retArr=json_decode($pageresults,true);
 
 echo $pageresults;
          
?>