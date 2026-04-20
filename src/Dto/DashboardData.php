<?php

declare(strict_types=1);

namespace Jslomian\Csv\Dto;

final readonly class DashboardData
{
    /**
     * @param list<EventSummary> $eventSummaries
     * @param list<UtmCampaignSummary> $topCampaigns
     * @param list<string> $availableCities
     */
    public function __construct(
        public array $eventSummaries,
        public array $topCampaigns,
        public array $availableCities,
    ) {
    }
}
