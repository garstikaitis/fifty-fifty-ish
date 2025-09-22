<?php

namespace App\DTOs;

readonly class SplitResultDTO
{
    /**
     * @param ExpenseSplitDTO[] $splits
     * @param SplitTotalsDTO $totals
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
