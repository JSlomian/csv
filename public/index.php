<?php

declare(strict_types=1);

use Jslomian\Csv\Controller\HomeController;
use Jslomian\Csv\Router;
use Jslomian\Csv\Service\CsvSalesReader;
use Jslomian\Csv\Service\EventReportService;
use Jslomian\Csv\View\ViewRenderer;

require dirname(__DIR__) . '/vendor/autoload.php';

$csvPath = dirname(__DIR__) . '/data/sales.csv';

$router = new Router();
$controller = new HomeController(
    new EventReportService(new CsvSalesReader($csvPath)),
    new ViewRenderer(dirname(__DIR__) . '/templates'),
);

$router->get('/', [$controller, 'index']);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);



