<?php
require_once '\Moodle.php';

$md = new Moodle();
$md = new Moodle();
$md->username = 'username';
$md->password = 'password';
$md->cert = getcwd() . '\cacert.pem';

$md->login();
var_dump($md->token);
$data = $md->parse(75);

var_dump($data);

$md->logout();

?>
