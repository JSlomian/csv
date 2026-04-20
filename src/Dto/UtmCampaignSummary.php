<?php

declare(strict_types=1);

namespace Jslomian\Csv\Dto;

final readonly class UtmCampaignSummary
{
    public function __construct(
        public string $campaign,
        public int $confirmedTicketQty,
    ) {
    }

    public function label(): string
    {
        return $this->campaign !== '' ? $this->campaign : '(brak kampanii)';
    }
}
