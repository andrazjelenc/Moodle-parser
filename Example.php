<?php
//error_reporting(E_ERROR);

require_once '\Moodle.php';
require_once '\Parser.php';

$prs = new Parser();
$md = new Moodle();

$md->username = 'usr';
$md->password = 'pswd';
$md->cert = getcwd() . '\cacert.pem';

$oldNews = -1;
$oldLinks = -1;

while(true)
{
	$md->login();
	$html = $md->parse(75);

	//$prs->html = file_get_contents('page.html');//$html;
	$prs->html = $html;
	$news = $prs->getNews();
	$links = $prs->getMainLinks();
	$md->logout();
	
	$status = -1;
	if($oldNews == -1 && $oldLinks == -1)
	{
		$status = 0;
	}
	else if($oldNews == $news && $oldLinks == $links)
	{
		$status = 1;
	}
	else
	{
		$status = 2;
	}
	
	$oldNews = $news;
	$oldLinks = $links;
	
	$output = date("Y-m-d H:i:s").' ';
	if($status == 0)
	{
		$output .= 'First run, initialization...';
	}
	else if($status == 1)
	{
		$output .= 'No changes...';
	}
	else if($status == 2)
	{
		$output .= 'Possible changes detected...';
	}
	$output .= "\r\n";
	echo $output;
	
	sleep(15*60); //sleep 15 minutes
}


//print_r($news);
//print_r($links);



?>
