<?php
require_once '\Moodle.php';
require_once '\Parser.php';

$prs = new Parser();
$md = new Moodle();

$md->username = 'usre';
$md->password = 'pswd';
$md->cert = getcwd() . '\cacert.pem'; //certs

$md->login();
$html = $md->parse(75); //page id to parse

$prs->html = $html;

$news = $prs->getNews();
$links = $prs->getMainLinks();

print_r($news);
print_r($links);

$md->logout();

?>
