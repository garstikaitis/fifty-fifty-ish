<?php

declare(strict_types=1);

use App\Models\Expense;
use Symfony\Component\HttpFoundation\StreamedResponse;

describe('ExportExpensesTest', function (): void {
    it('can export expenses as csv', function (): void {
        Expense::factory()->count(5)->create([
            'title' => 'Test Expense',
            'amount' => 10000,
        ]);
        $response = $this->get(route('expenses.export'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="export.csv"');
        expect($response->baseResponse)->toBeInstanceOf(StreamedResponse::class);
        $content = $response->streamedContent();
        $lines = explode("\n", mb_trim($content));
        $headers = str_getcsv($lines[0], escape: '\\');

        expect($headers)->toBe(['Expense', 'Amount', 'Driver #1', 'Driver #2'])
            ->and($content)->toContain('Test Expense')
            ->and($content)->toContain('Total:');
    });

    it('handles chunking correctly with multiple chunks', function (): void {
        // Create 2+ chunks
        Expense::factory()->count(450)->create();

        $response = $this->get(route('expenses.export'));

        $response->assertStatus(200);
        $content = $response->streamedContent();
        $lines = explode("\n", mb_trim($content));

        // Should have header + 450 data rows + totals
        expect(count($lines))->toBeGreaterThanOrEqual(452);
    });
});
