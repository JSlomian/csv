<?php

declare(strict_types=1);

namespace Jslomian\Csv\Controller;

use Jslomian\Csv\Dto\EventFilters;
use Jslomian\Csv\Exception\CsvDataException;
use Jslomian\Csv\Service\EventReportService;
use Jslomian\Csv\View\ViewRenderer;

final readonly class HomeController
{
    public function __construct(
        private EventReportService $eventReportService,
        private ViewRenderer $viewRenderer,
    ) {
    }

    public function index(): void
    {
        $filters = EventFilters::fromQuery($_GET);

        try {
            $dashboard = $this->eventReportService->buildDashboard($filters);

            echo $this->viewRenderer->render('home', [
                'dashboard' => $dashboard,
                'filters' => $filters,
                'errorMessage' => null,
            ]);
        } catch (CsvDataException $exception) {
            http_response_code(500);

            echo $this->viewRenderer->render('home', [
                'dashboard' => null,
                'filters' => $filters,
                'errorMessage' => $exception->getMessage(),
            ]);
        }
    }
}
