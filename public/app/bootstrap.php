<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../app/src/AppAutoload.php';

spl_autoload_register('AppAutoload::autoload');
