<?php

declare(strict_types=1);

namespace Jslomian\Csv\Service;

use DateTimeImmutable;
use Jslomian\Csv\Dto\SaleRecord;
use Jslomian\Csv\Exception\CsvDataException;

final readonly class CsvSalesReader
{
    private const array REQUIRED_COLUMNS = [
        'event_id',
        'event_date',
        'city',
        'category',
        'order_id',
        'ticket_qty',
        'status',
        'utm_source',
        'utm_campaign',
        'utm_content',
        'sold_out',
    ];

    public function __construct(private string $csvPath)
    {
    }

    /**
     * @return list<SaleRecord>
     */
    public function read(): array
    {
        if (!is_file($this->csvPath)) {
            throw new CsvDataException(sprintf(
                'Nie znaleziono pliku CSV pod ścieżką: %s',
                $this->csvPath
            ));
        }

        $handle = fopen($this->csvPath, 'rb');

        if ($handle === false) {
            throw new CsvDataException('Nie udało się otworzyć pliku CSV do odczytu.');
        }

        try {
            $header = fgetcsv($handle, escape: '\\');

            if ($header === false) {
                throw new CsvDataException('Plik CSV jest pusty.');
            }

            $headerMap = $this->buildHeaderMap($header);
            $records = [];
            $rowNumber = 1;

            while (($row = fgetcsv($handle, escape: '\\')) !== false) {
                $rowNumber++;

                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $records[] = $this->mapRow($row, $headerMap, $rowNumber);
            }

            return $records;
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param list<string|null> $header
     * @return array<string, int>
     */
    private function buildHeaderMap(array $header): array
    {
        $headerMap = [];

        foreach ($header as $index => $column) {
            if ($column === null) {
                continue;
            }

            $headerMap[trim($column)] = $index;
        }

        foreach (self::REQUIRED_COLUMNS as $requiredColumn) {
            if (!array_key_exists($requiredColumn, $headerMap)) {
                throw new CsvDataException(sprintf(
                    'Brakuje wymaganej kolumny w CSV: %s',
                    $requiredColumn
                ));
            }
        }

        return $headerMap;
    }

    /**
     * @param list<string|null> $row
     * @param array<string, int> $headerMap
     */
    private function mapRow(array $row, array $headerMap, int $rowNumber): SaleRecord
    {
        $eventDate = $this->parseDate($this->value($row, $headerMap, 'event_date'), $rowNumber);
        $ticketQty = $this->parseTicketQty($this->value($row, $headerMap, 'ticket_qty'), $rowNumber);

        return new SaleRecord(
            $this->value($row, $headerMap, 'event_id'),
            $eventDate,
            $this->value($row, $headerMap, 'city'),
            strtolower($this->value($row, $headerMap, 'category')),
            $this->value($row, $headerMap, 'order_id'),
            $ticketQty,
            strtolower($this->value($row, $headerMap, 'status')),
            $this->value($row, $headerMap, 'utm_source'),
            $this->value($row, $headerMap, 'utm_campaign'),
            $this->value($row, $headerMap, 'utm_content'),
            $this->parseBool($this->value($row, $headerMap, 'sold_out'), $rowNumber),
        );
    }

    /**
     * @param list<string|null> $row
     * @param array<string, int> $headerMap
     */
    private function value(array $row, array $headerMap, string $column): string
    {
        $index = $headerMap[$column];

        if (!array_key_exists($index, $row)) {
            return '';
        }

        return trim((string) $row[$index]);
    }

    /**
     * @param list<string|null> $row
     */
    private function isEmptyRow(array $row): bool
    {
        return array_all($row, fn($value) => trim((string)$value) === '');

    }

    private function parseDate(string $value, int $rowNumber): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            throw new CsvDataException(sprintf(
                'Niepoprawna data w wierszu %d: %s',
                $rowNumber,
                $value
            ));
        }

        return $date;
    }

    private function parseTicketQty(string $value, int $rowNumber): int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new CsvDataException(sprintf(
                'Niepoprawna liczba biletów w wierszu %d: %s',
                $rowNumber,
                $value
            ));
        }

        return (int) $value;
    }

    private function parseBool(string $value, int $rowNumber): bool
    {
        return match (strtolower($value)) {
            'true', '1' => true,
            'false', '0' => false,
            default => throw new CsvDataException(sprintf(
                'Niepoprawna wartość pola sold_out w wierszu %d: %s',
                $rowNumber,
                $value
            )),
        };
    }
}
