<?php declare(strict_types=1);


namespace App\Domains\Pricing;

use Carbon\Carbon;


readonly class Point
{
    public float $value;

    public function __construct(public Carbon $time, float $value)
    {
        $this->value = round($value, 4);
    }

    public function toArray(): array
    {
        return [
            'time' => $this->time->toIso8601String(),
            'value' => $this->value,
        ];
    }

    public function equals(Point $point): bool
    {
        return $this->time->eq($point->time) && $this->value === $point->value;
    }

    public function atSameTime(Point $point): bool
    {
        return $this->time->eq($point->time);
    }

    public function __toString(): string
    {
        return "Time: {$this->time}: {$this->value}";
    }
}
