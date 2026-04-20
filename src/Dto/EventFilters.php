<?php

declare(strict_types=1);

namespace Jslomian\Csv\Dto;

use DateTimeImmutable;

final readonly class EventFilters
{
    private function __construct(
        public ?string $city,
        public ?string $category,
        public ?DateTimeImmutable $dateFrom,
        public ?DateTimeImmutable $dateTo,
        public string $dateFromRaw,
        public string $dateToRaw,
    ) {
    }

    /**
     * @param array<string, mixed> $query
     */
    public static function fromQuery(array $query): self
    {
        $city = self::normalizeString($query['city'] ?? null);
        $category = self::normalizeCategory($query['category'] ?? null);
        $dateFromRaw = self::normalizeString($query['date_from'] ?? null) ?? '';
        $dateToRaw = self::normalizeString($query['date_to'] ?? null) ?? '';

        return new self(
            $city,
            $category,
            self::parseDate($dateFromRaw),
            self::parseDate($dateToRaw),
            $dateFromRaw,
            $dateToRaw,
        );
    }

    private static function normalizeString(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private static function normalizeCategory(mixed $value): ?string
    {
        $normalized = self::normalizeString($value);

        if ($normalized === null) {
            return null;
        }

        $normalized = strtolower($normalized);

        return in_array($normalized, ['kids', 'adults'], true) ? $normalized : null;
    }

    private static function parseDate(string $value): ?DateTimeImmutable
    {
        if ($value === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value ? $date : null;
    }
}
