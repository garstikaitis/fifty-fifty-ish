<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ExpenseSplitDTO;
use App\DTOs\SplitResultDTO;
use App\DTOs\SplitTotalsDTO;
use App\Utils\Currency;
use Illuminate\Support\Collection;

final class ExpenseSplitter
{
    private const int PARTIES_COUNT = 2;

    public function split(Collection $expenses): SplitResultDTO
    {
        if ($expenses->isEmpty()) {
            return $this->emptyResult();
        }

        $splits = $this->calculateBaseSplits($expenses);
        $remainderIndexes = $this->findRemainderIndexes($splits);
        $finalSplits = $this->distributeRemainders($splits, $remainderIndexes);
        $totals = $this->calculateTotals($finalSplits);

        return new SplitResultDTO(
            splits: $finalSplits,
            totals: $totals
        );
    }

    private function calculateBaseSplits(Collection $expenses): array
    {
        $splits = [];

        foreach ($expenses as $expense) {
            $totalMinorUnits = $expense->amount;
            $baseAmountMinorUnits = (int) floor($totalMinorUnits / self::PARTIES_COUNT);
            $remainderMinorUnits = $totalMinorUnits - ($baseAmountMinorUnits * self::PARTIES_COUNT);

            $splits[] = [
                'split' => new ExpenseSplitDTO(
                    title: $expense->title,
                    totalAmount: Currency::toMajorUnits($totalMinorUnits),
                    partyAAmount: Currency::toMajorUnits($baseAmountMinorUnits),
                    partyBAmount: Currency::toMajorUnits($baseAmountMinorUnits),
                ),
                'remainder' => $remainderMinorUnits,
            ];
        }

        return $splits;
    }

    private function findRemainderIndexes(array $splits): array
    {
        return array_keys(
            array_filter($splits, fn (array $splitData): bool => $splitData['remainder'] > 0)
        );
    }

    private function distributeRemainders(array $splits, array $remainderIndexes): array
    {
        $totalRemainders = count($remainderIndexes);

        if ($totalRemainders === 0) {
            return array_column($splits, 'split');
        }

        // Party A gets the extra remainder
        $partyAGets = (int) ceil($totalRemainders / 2);

        for ($i = 0; $i < $totalRemainders; $i++) {
            $expenseIndex = $remainderIndexes[$i];
            $currentSplit = $splits[$expenseIndex]['split'];

            if ($i < $partyAGets) {
                $newPartyAAmount = Currency::addMinorUnits($currentSplit->partyAAmount, 1);
                $newPartyBAmount = $currentSplit->partyBAmount;
            } else {
                $newPartyAAmount = $currentSplit->partyAAmount;
                $newPartyBAmount = Currency::addMinorUnits($currentSplit->partyBAmount, 1);
            }

            $splits[$expenseIndex]['split'] = new ExpenseSplitDTO(
                title: $currentSplit->title,
                totalAmount: $currentSplit->totalAmount,
                partyAAmount: $newPartyAAmount,
                partyBAmount: $newPartyBAmount,
            );
        }

        return array_column($splits, 'split');
    }

    private function calculateTotals(array $splits): SplitTotalsDTO
    {
        $partyAMinorUnits = 0;
        $partyBMinorUnits = 0;
        $totalMinorUnits = 0;

        foreach ($splits as $split) {
            $partyAMinorUnits += Currency::toMinorUnits($split->partyAAmount);
            $partyBMinorUnits += Currency::toMinorUnits($split->partyBAmount);
            $totalMinorUnits += Currency::toMinorUnits($split->totalAmount);
        }

        $partyATotal = Currency::toMajorUnits($partyAMinorUnits);
        $partyBTotal = Currency::toMajorUnits($partyBMinorUnits);

        return new SplitTotalsDTO(
            totalAmount: Currency::toMajorUnits($totalMinorUnits),
            partyATotal: $partyATotal,
            partyBTotal: $partyBTotal,
            difference: round(abs($partyATotal - $partyBTotal), 2),
        );
    }

    private function emptyResult(): SplitResultDTO
    {
        return new SplitResultDTO(
            splits: [],
            totals: new SplitTotalsDTO(totalAmount: 0.00, partyATotal: 0.00, partyBTotal: 0.00, difference: 0.00)
        );
    }
}
