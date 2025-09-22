<?php

use App\Services\ExpenseSplitter;
use App\Utils\Currency;
use Illuminate\Support\Collection;

function createExpense(string $title, int $amount): object
{
    return (object)[
        'title' => $title,
        'amount' => $amount,
    ];
}


describe('ExpenseSplitter Service', function () {

    test('handles empty expense collection', function ($data) {
        $result = new ExpenseSplitter()->split($data);

        expect($result->splits)->toBeEmpty()
            ->and($result->totals->totalAmount)->toBe(0.00)
            ->and($result->totals->partyATotal)->toBe(0.00)
            ->and($result->totals->partyBTotal)->toBe(0.00)
            ->and($result->totals->difference)->toBe(0.00);
    })->with([collect()]);

    test('splits even amounts correctly', function ($data) {
        $result = new ExpenseSplitter()->split($data);

        expect($result->splits)->toHaveCount(1);

        $split = $result->splits[0];
        expect($split->title)->toBe('Fuel')
            ->and($split->totalAmount)->toBe(100.00)
            ->and($split->partyAAmount)->toBe(50.00)
            ->and($split->partyBAmount)->toBe(50.00)
            ->and($result->totals->totalAmount)->toBe(100.00)
            ->and($result->totals->partyATotal)->toBe(50.00)
            ->and($result->totals->partyBTotal)->toBe(50.00)
            ->and($result->totals->difference)->toBe(0.00);
    })->with([collect([
        createExpense('Fuel', 10000)
    ])]);

    test('handles single cent remainder correctly', function () {
        $expenses = collect([
            createExpense('Fuel', 10001),
        ]);

        $result = new ExpenseSplitter()->split($expenses);

        $split = $result->splits[0];
        expect($split->title)->toBe('Fuel')
            ->and($split->totalAmount)->toBe(100.01)
            ->and($split->partyAAmount)->toBe(50.01)
            ->and($split->partyBAmount)->toBe(50.00)
            ->and($result->totals->difference)->toBe(0.01)
            ->and($result->totals->partyATotal)->toBeGreaterThan($result->totals->partyBTotal);
    });

    test('distributes multiple remainders fairly-ish', function ($data) {

        $result = new ExpenseSplitter()->split($data);

        expect($result->splits)->toHaveCount(3)
            ->and($result->totals->difference)->toBe(0.01);

        $splits = $result->splits;

        expect($splits[0]->title)->toBe('Fuel')
            ->and($splits[0]->totalAmount)->toBe(100.01)
            ->and($splits[0]->partyAAmount)->toBe(50.01)
            ->and($splits[0]->partyBAmount)->toBe(50.00)
            ->and($splits[1]->title)->toBe('Insurance')
            ->and($splits[1]->totalAmount)->toBe(27.27)
            ->and($splits[1]->partyAAmount)->toBe(13.64)
            ->and($splits[1]->partyBAmount)->toBe(13.63)
            ->and($splits[2]->title)->toBe('Oil')
            ->and($splits[2]->totalAmount)->toBe(117.17)
            ->and($splits[2]->partyAAmount)->toBe(58.58)
            ->and($splits[2]->partyBAmount)->toBe(58.59)
            ->and($result->totals->partyATotal)->toBeGreaterThan($result->totals->partyBTotal)
            ->and($result->totals->difference)->toBe(0.01);
    })->with([collect([
        createExpense('Fuel', 10001),
        createExpense('Insurance', 2727),
        createExpense('Oil', 11717),
    ])]);

    test('total always matches original amounts', function () {
        $amounts = [12345, 6789, 23456, 8912, 34567];

        $expenses = collect();
        foreach ($amounts as $amount) {
            $expenses->push(createExpense("Expense {$amount}", $amount));
        }

        $result = new ExpenseSplitter()->split($expenses);

        $originalTotal = Currency::toMajorUnits(array_sum($amounts));
        $splitTotal = $result->totals->partyATotal + $result->totals->partyBTotal;

        expect($result->totals->totalAmount)->toBe($originalTotal)
            ->and($splitTotal)->toBe($originalTotal);
    });

    test('handles zero amounts', function ($data) {

        $result = new ExpenseSplitter()->split($data);

        $split = $result->splits[0];
        expect($split->title)->toBe('Free Item')
            ->and($split->totalAmount)->toBe(0.00)
            ->and($split->partyAAmount)->toBe(0.00)
            ->and($split->partyBAmount)->toBe(0.00);
    })->with([ collect([
        createExpense('Free Item', 0),
    ])]);
});
