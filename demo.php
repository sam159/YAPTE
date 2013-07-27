<?php

ini_set('display_errors', True);
error_reporting(E_ALL);
date_default_timezone_set('Europe/London');

define('DIR_TEMPLATES', dirname(__FILE__).DIRECTORY_SEPARATOR.'DemoTemplates');
define('DIR_TEMPLATES_COMPILED', false);

require('Template.php');

$t = Template::GetInstance();

$data = array(
	'world' => 'You!'
);

echo $t->Render('helloworld',$data);