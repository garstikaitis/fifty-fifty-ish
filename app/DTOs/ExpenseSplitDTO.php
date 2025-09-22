<?php

namespace App\DTOs;

readonly class ExpenseSplitDTO
{
    public function __construct(
        public string $title,
        public float $totalAmount,
        public float $partyAAmount,
        public float $partyBAmount,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'total_amount' => $this->totalAmount,
            'party_a_amount' => $this->partyAAmount,
            'party_b_amount' => $this->partyBAmount,
        ];
    }
}
