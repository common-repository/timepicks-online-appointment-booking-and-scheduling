<?php
$email = $_GET['email'];
$domain = "http://www.timepicks.com/";
$param="?email={$email}";
		$apiurl = $domain."js/httpRequests/checkIfEmailExists.php";
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
		$retArr=json_decode($pageresults,true);
 
 echo json_encode($retArr);
                
?>