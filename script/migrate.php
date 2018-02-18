<?php

require_once(dirname(__FILE__) . '/../lib/Requires.php');

$migration = new MigrationController();
$migration->execute();

?>