<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../app/bootstrap.php';

use AppCore\Application;

$request = $_REQUEST;

$application = new Application();
$application->build()->run($request);
