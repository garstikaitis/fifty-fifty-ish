<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class SplitResultDTO
{
    /**
     * @param  ExpenseSplitDTO[]  $splits
     */
    public function __construct(
        public array $splits,
        public SplitTotalsDTO $totals,
    ) {}

    public function toArray(): array
    {
        return [
            'splits' => $this->splits,
            'totals' => $this->totals->toArray(),
        ];
    }
}
