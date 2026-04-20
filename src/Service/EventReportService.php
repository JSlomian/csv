<?php

declare(strict_types=1);

namespace Jslomian\Csv\Service;

use Jslomian\Csv\Dto\DashboardData;
use Jslomian\Csv\Dto\EventFilters;
use Jslomian\Csv\Dto\EventSummary;
use Jslomian\Csv\Dto\SaleRecord;
use Jslomian\Csv\Dto\UtmCampaignSummary;

final readonly class EventReportService
{
    public function __construct(private CsvSalesReader $csvSalesReader)
    {
    }

    public function buildDashboard(EventFilters $filters): DashboardData
    {
        $records = $this->csvSalesReader->read();

        return new DashboardData(
            $this->buildEventSummaries($records, $filters),
            $this->buildTopCampaigns($records),
            $this->buildAvailableCities($records),
        );
    }

    /**
     * @param list<SaleRecord> $records
     * @return list<EventSummary>
     */
    private function buildEventSummaries(array $records, EventFilters $filters): array
    {
        $groupedEvents = [];

        foreach ($records as $record) {
            if (!array_key_exists($record->eventId, $groupedEvents)) {
                $groupedEvents[$record->eventId] = [
                    'event_id' => $record->eventId,
                    'event_date' => $record->eventDate->format('Y-m-d'),
                    'city' => $record->city,
                    'category' => $record->category,
                    'confirmed_ticket_qty' => 0,
                ];
            }

            if ($record->isConfirmed()) {
                $groupedEvents[$record->eventId]['confirmed_ticket_qty'] += $record->ticketQty;
            }
        }

        $summaries = [];

        foreach ($groupedEvents as $event) {
            $summary = new EventSummary(
                $event['event_id'],
                $event['event_date'],
                $event['city'],
                $event['category'],
                $event['confirmed_ticket_qty'],
            );

            if ($this->matchesFilters($summary, $filters)) {
                $summaries[] = $summary;
            }
        }

        usort($summaries, static function (EventSummary $left, EventSummary $right): int {
            return [$left->eventDate, $left->city, $left->category, $left->eventId]
                <=> [$right->eventDate, $right->city, $right->category, $right->eventId];
        });

        return $summaries;
    }

    /**
     * @param list<SaleRecord> $records
     * @return list<UtmCampaignSummary>
     */
    private function buildTopCampaigns(array $records): array
    {
        $campaignTotals = [];

        foreach ($records as $record) {
            if (!$record->isConfirmed()) {
                continue;
            }

            $campaignTotals[$record->utmCampaign] = ($campaignTotals[$record->utmCampaign] ?? 0) + $record->ticketQty;
        }

        $campaigns = [];

        foreach ($campaignTotals as $campaign => $confirmedTicketQty) {
            $campaigns[] = new UtmCampaignSummary($campaign, $confirmedTicketQty);
        }

        usort($campaigns, static function (UtmCampaignSummary $left, UtmCampaignSummary $right): int {
            $byVolume = $right->confirmedTicketQty <=> $left->confirmedTicketQty;

            return $byVolume !== 0 ? $byVolume : strcmp($left->campaign, $right->campaign);
        });

        return array_slice($campaigns, 0, 10);
    }

    /**
     * @param list<SaleRecord> $records
     * @return list<string>
     */
    private function buildAvailableCities(array $records): array
    {
        $cities = [];

        foreach ($records as $record) {
            $cities[$record->city] = true;
        }

        $availableCities = array_keys($cities);
        sort($availableCities);

        return $availableCities;
    }

    private function matchesFilters(EventSummary $summary, EventFilters $filters): bool
    {
        if ($filters->city !== null && $summary->city !== $filters->city) {
            return false;
        }

        if ($filters->category !== null && $summary->category !== $filters->category) {
            return false;
        }

        if ($filters->dateFrom !== null && $summary->eventDate < $filters->dateFrom->format('Y-m-d')) {
            return false;
        }

        if ($filters->dateTo !== null && $summary->eventDate > $filters->dateTo->format('Y-m-d')) {
            return false;
        }

        return true;
    }
}
