<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\ExpenseSplitDTO;
use App\Services\ExpenseSplitter;
use App\Utils\Currency;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExportExpenses
{
    private int $chunkSize = 1000;

    private ?Builder $query = null;

    private int $partyATotalAmount = 0;

    private int $partyBTotalAmount = 0;

    private int $totalAmount = 0;

    public function withChunkSize(int $size): self
    {
        $this->chunkSize = $size;

        return $this;
    }

    public function withQuery(Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function handle(): StreamedResponse
    {
        if (! $this->query instanceof Builder) {
            throw new Exception('No query set. Call withQuery() first');
        }

        $responseHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return new StreamedResponse(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $this->getRowHeaders(), escape: '\\');

            $this->query->chunk($this->chunkSize, function ($models) use ($handle): void {
                $expenses = new ExpenseSplitter()->split($models);
                $this->partyATotalAmount += Currency::toMinorUnits($expenses->totals->partyATotal);
                $this->partyBTotalAmount += Currency::toMinorUnits($expenses->totals->partyBTotal);
                $this->totalAmount += Currency::toMinorUnits($expenses->totals->totalAmount);
                foreach ($expenses->splits as $expense) {
                    fputcsv($handle, $this->formatRow($expense), escape: '\\');
                }

                if (ob_get_level() !== 0) {
                    ob_flush();
                }
                flush();
            });

            $this->appendTotalsRow($handle);

            fclose($handle);
        }, 200, $responseHeaders);
    }

    private function appendTotalsRow($handle): void
    {
        fputcsv($handle, [
            'Total: ',
            Currency::toMajorUnits($this->totalAmount),
            Currency::toMajorUnits($this->partyATotalAmount),
            Currency::toMajorUnits($this->partyBTotalAmount),
        ], escape: '\\');
    }

    private function formatRow(ExpenseSplitDTO $expense): array
    {
        return [
            $expense->title,
            $expense->totalAmount,
            $expense->partyAAmount,
            $expense->partyBAmount,
        ];
    }

    private function getRowHeaders(): array
    {
        return [
            'Expense',
            'Amount',
            // We can hardcode these names since they match the ones in database
            'Driver #1',
            'Driver #2',
        ];
    }
}
