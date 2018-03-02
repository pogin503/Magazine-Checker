<?php
require_once(dirname(__FILE__). '/../vendor/autoload.php');

use Libraries\Dispatcher;

$dispatch = new Dispatcher();
$dispatch->execute();

?>