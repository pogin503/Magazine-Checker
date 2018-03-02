<?php
require_once(dirname(__FILE__). '/../vendor/autoload.php');

use Controllers\MigrationController;

$migration = new MigrationController();
$migration->execute();

?>