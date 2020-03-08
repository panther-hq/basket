<?php

declare(strict_types=1);

namespace PantherHQ\Basket\Item;

final class Attribute implements \JsonSerializable
{
    /**
     * @var string
     */
    private $promotion;

    /**
     * @var string
     */
    private $description;

    public function promotion(): string
    {
        return $this->promotion;
    }

    public function setPromotion(string $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function hasPromotion(): bool
    {
        return $this->promotion !== null;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function hasDescription(): bool
    {
        return $this->description !== null;
    }

    public function jsonSerialize(): array
    {
        return [
            'promotion' => $this->hasPromotion() ? $this->promotion() : null,
            'description' => $this->hasDescription() ? $this->description() : null,
        ];
    }
}
