<?php

declare(strict_types=1);

use Jslomian\Csv\Dto\DashboardData;
use Jslomian\Csv\Dto\EventFilters;

/** @var DashboardData|null $dashboard */
/** @var EventFilters $filters */
/** @var string|null $errorMessage */

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$eventCount = $dashboard !== null ? count($dashboard->eventSummaries) : 0;
$campaignCount = $dashboard !== null ? count($dashboard->topCampaigns) : 0;
?>
<!DOCTYPE html>
<html lang="pl" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Sales Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased">
<div class="min-h-full">
    <header class="border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-start justify-between gap-6 px-4 py-8 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Event Sales Dashboard</p>
            </div>
            <dl class="hidden min-w-0 shrink-0 grid-cols-2 gap-x-8 gap-y-4 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-5 text-sm shadow-sm lg:grid">
                <div>
                    <dt class="text-slate-500">Eventy po filtrach</dt>
                    <dd class="mt-1 text-2xl font-semibold tracking-tight text-slate-950"><?= $eventCount ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">Kampanie w topce</dt>
                    <dd class="mt-1 text-2xl font-semibold tracking-tight text-slate-950"><?= $campaignCount ?></dd>
                </div>
            </dl>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <?php if ($errorMessage !== null): ?>
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-900 shadow-sm sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 h-2.5 w-2.5 rounded-full bg-rose-500"></div>
                    <div>
                        <h2 class="font-semibold">Nie udało się wczytać danych</h2>
                        <p class="mt-1 leading-6 text-rose-800"><?= $escape($errorMessage) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-5 sm:px-6">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Filtry listy eventów</h2>
                        <p class="mt-1 text-sm text-slate-500">Filtrowanie działa po mieście, zakresie dat i kategorii. Ranking UTM nie zmienia się wraz z filtrami.</p>
                    </div>
                </div>
            </div>

            <form method="get" action="/" class="px-4 py-5 sm:px-6">
                <div class="grid gap-5 lg:grid-cols-12">
                    <div class="lg:col-span-3">
                        <label for="city" class="block text-sm font-medium text-slate-700">Miasto</label>
                        <select
                            name="city"
                            id="city"
                            class="mt-2 block w-full rounded-xl border-0 bg-white py-2.5 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                        >
                            <option value="">Wszystkie</option>
                            <?php foreach (($dashboard?->availableCities ?? []) as $city): ?>
                                <option value="<?= $escape($city) ?>" <?= $filters->city === $city ? 'selected' : '' ?>>
                                    <?= $escape($city) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="lg:col-span-3">
                        <label for="date_from" class="block text-sm font-medium text-slate-700">Data od</label>
                        <input
                            type="date"
                            name="date_from"
                            id="date_from"
                            value="<?= $escape($filters->dateFromRaw) ?>"
                            class="mt-2 block w-full rounded-xl border-0 px-3 py-2.5 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                        >
                    </div>

                    <div class="lg:col-span-3">
                        <label for="date_to" class="block text-sm font-medium text-slate-700">Data do</label>
                        <input
                            type="date"
                            name="date_to"
                            id="date_to"
                            value="<?= $escape($filters->dateToRaw) ?>"
                            class="mt-2 block w-full rounded-xl border-0 px-3 py-2.5 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                        >
                    </div>

                    <div class="lg:col-span-3">
                        <label for="category" class="block text-sm font-medium text-slate-700">Kategoria</label>
                        <select
                            name="category"
                            id="category"
                            class="mt-2 block w-full rounded-xl border-0 bg-white py-2.5 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                        >
                            <option value="">Wszystkie</option>
                            <option value="kids" <?= $filters->category === 'kids' ? 'selected' : '' ?>>kids</option>
                            <option value="adults" <?= $filters->category === 'adults' ? 'selected' : '' ?>>adults</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-end">
                    <a
                        href="/"
                        class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition hover:bg-slate-50"
                    >
                        Wyczyść
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        Filtruj eventy
                    </button>
                </div>
            </form>
        </section>

        <section class="mt-8 grid gap-8 xl:grid-cols-[minmax(0,1.75fr)_minmax(20rem,1fr)]">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-5 sm:px-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">Lista eventów</h2>
                        </div>
                        <div class="text-sm text-slate-500">
                            Wyników: <span class="font-semibold text-slate-900"><?= $eventCount ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($dashboard === null): ?>
                    <div class="px-4 py-10 text-sm text-slate-500 sm:px-6">Widok zostanie uzupełniony po poprawnym wczytaniu danych CSV.</div>
                <?php elseif ($dashboard->eventSummaries === []): ?>
                    <div class="px-4 py-10 text-sm text-slate-500 sm:px-6">Brak eventów dla wybranych filtrów.</div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-slate-900 sm:px-6">Event ID</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-slate-900">Data</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-slate-900">Miasto</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-slate-900">Kategoria</th>
                                <th scope="col" class="px-4 py-3.5 text-right text-sm font-semibold text-slate-900 sm:px-6">Sprzedane bilety</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                            <?php foreach ($dashboard->eventSummaries as $event): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-slate-900 sm:px-6"><?= $escape($event->eventId) ?></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"><?= $escape($event->eventDate) ?></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600"><?= $escape($event->city) ?></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-600">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium uppercase tracking-wide text-slate-700">
                                            <?= $escape($event->category) ?>
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-semibold text-slate-900 sm:px-6"><?= $event->confirmedTicketQty ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-5 sm:px-6">
                    <h2 class="text-lg font-semibold text-slate-950">Top 10 UTM</h2>
                </div>

                <?php if ($dashboard === null): ?>
                    <div class="px-4 py-10 text-sm text-slate-500 sm:px-6">Ranking pojawi się po poprawnym wczytaniu danych CSV.</div>
                <?php elseif ($dashboard->topCampaigns === []): ?>
                    <div class="px-4 py-10 text-sm text-slate-500 sm:px-6">Brak kampanii z potwierdzoną sprzedażą.</div>
                <?php else: ?>
                    <ul role="list" class="divide-y divide-slate-100">
                        <?php foreach ($dashboard->topCampaigns as $index => $campaign): ?>
                            <li class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6">
                                <div class="flex min-w-0 items-center gap-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-700">
                                        <?= $index + 1 ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-slate-900"><?= $escape($campaign->label()) ?></p>
                                        <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">utm_campaign</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold tracking-tight text-slate-950"><?= $campaign->confirmedTicketQty ?></p>
                                    <p class="text-xs text-slate-500">biletów</p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
</body>
</html>
