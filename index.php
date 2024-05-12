<?php

require __DIR__ . "/vendor/autoload.php";

use InvoiceService\Application;

$app = new Application();
$app->run();