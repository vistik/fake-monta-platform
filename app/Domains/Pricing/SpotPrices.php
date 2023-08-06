<?php declare(strict_types=1);

namespace App\Domains\Pricing;

use App\Domains\Pricing\Exceptions\SpotPriceAlreadyExistsException;
use App\Domains\Pricing\Exceptions\SpotPriceShouldBeRoundHoursException;
use Illuminate\Support\Collection;

/**
 * @template-covariant TValue
 */
class SpotPrices extends Collection
{
    public function asCollection(): self
    {
        return $this;
    }

    /**
     * @param TValue $item
     * @throws SpotPriceAlreadyExistsException
     * @throws SpotPriceShouldBeRoundHoursException
     */
    public function add($item): self
    {
        $this->validate($item);

        parent::add($item);

        $this->items = $this->sortBy->time->all();

        return $this;
    }

    public function addMany(Collection $additions): self
    {
        $additions->each(fn($item) => $this->add($item));

        return $this;
    }

    /**
     * @param TValue $item
     * @throws SpotPriceAlreadyExistsException
     * @throws SpotPriceShouldBeRoundHoursException
     */
    private function validate($item): void
    {
        if ($this->first(fn(Point $point) => $point->time->isSameAs('Y-m-d H:i:s', $item->time))) {
            throw new SpotPriceAlreadyExistsException(
                'Spot price for ' . $item->time->format('Y-m-d H:i:s') . ' already exists'
            );
        }

        if ($item->time->minute !== 0 || $item->time->second !== 0 || $item->time->microsecond !== 0) {
            throw new SpotPriceShouldBeRoundHoursException('Spot prices should only contain round hours');
        }
    }
}
