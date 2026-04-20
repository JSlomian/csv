<?php

declare(strict_types=1);

namespace Jslomian\Csv\Dto;

use DateTimeImmutable;

final readonly class SaleRecord
{
    public function __construct(
        public string $eventId,
        public DateTimeImmutable $eventDate,
        public string $city,
        public string $category,
        public string $orderId,
        public int $ticketQty,
        public string $status,
        public string $utmSource,
        public string $utmCampaign,
        public string $utmContent,
        public bool $soldOut,
    ) {
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
