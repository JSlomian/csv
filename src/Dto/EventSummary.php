<?php

declare(strict_types=1);

namespace Jslomian\Csv\Dto;

final readonly class EventSummary
{
    public function __construct(
        public string $eventId,
        public string $eventDate,
        public string $city,
        public string $category,
        public int $confirmedTicketQty,
    ) {
    }
}
