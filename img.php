<?php

	/**
	* FileMaker PHP Site Assistant Generated File
	*/
	require_once 'fmview.php';
	require_once 'FileMaker.php';
	require_once 'error.php';

	$databaseName = 'Shellac';
	$cgi = new CGI();

	$userName = 'Web';
	$passWord = 'web';

	$fm = & new FileMaker();
	$fm->setProperty('database', $databaseName);
	$fm->setProperty('username', $userName);
	$fm->setProperty('password', $passWord);
	ExitOnError($fm);

	if (isset($_GET['-url'])){
		$url = $_GET['-url'];
		$url = substr($url, 0, strpos($url, "?"));
		$url = substr($url, strrpos($url, ".") + 1);
		if($url == "jpg"){
			header('Content-type: image/jpeg');
		}
		else if($url == "gif"){
			header('Content-type: image/gif');
		}
		else{
			header('Content-type: application/octet-stream');
		}
		echo $fm->getContainerData($_GET['-url']);
	}
?>
