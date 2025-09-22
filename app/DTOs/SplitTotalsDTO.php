<?php

namespace App\DTOs;

readonly class SplitTotalsDTO
{
    public function __construct(
        public float $totalAmount,
        public float $partyATotal,
        public float $partyBTotal,
        public float $difference,
    ) {}

    public function toArray(): array
    {
        return [
            'total_amount' => $this->totalAmount,
            'party_a_total' => $this->partyATotal,
            'party_b_total' => $this->partyBTotal,
            'difference' => $this->difference,
        ];
    }

}
