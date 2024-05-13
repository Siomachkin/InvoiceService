<?php

require __DIR__ . "/vendor/autoload.php";

use InvoiceService\Application;
use InvoiceService\ServiceContainer;

$container = ServiceContainer::buildContainer();
$app = new Application($container);
$app->run();