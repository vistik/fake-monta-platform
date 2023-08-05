<?php

namespace Domains\Pricing;

use App\Domains\Pricing\Exceptions\SpotPriceAlreadyExistsException;
use App\Domains\Pricing\Exceptions\SpotPriceShouldBeRoundHoursException;
use App\Domains\Pricing\Point;
use App\Domains\Pricing\SpotPrices;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SpotPricesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2023-08-03 10:00:00');
    }

    /**
     * @test
     */
    public function it_can_add_spot_price_data_to_value_object(): void
    {
        // Given
        $spotPrices = new SpotPrices();

        // When
        $point = new Point(now(), 1234.123);

        $spotPrices->add($point);

        // Then
        $this->assertEquals(1, $spotPrices->asCollection()->count());
        $this->assertTrue($spotPrices->asCollection()->first()->equals($point));
    }

    /**
     * @test
     */
    public function it_can_add_multiple_spot_prices_at_once(): void
    {
        // Given
        $spotPrices = new SpotPrices();

        // When
        $points = collect([
            new Point(now(), 1234.123),
            new Point(now()->addHours(1), 1234.123),
            new Point(now()->addHours(2), 1234.123),
            new Point(now()->addHours(3), 1234.123),
        ]);

        $spotPrices->addMany($points);

        // Then
        $this->assertEquals(4, $spotPrices->asCollection()->count());
        $this->assertTrue($spotPrices->asCollection()->get(0)->equals($points->get(0)));
        $this->assertTrue($spotPrices->asCollection()->get(1)->equals($points->get(1)));
        $this->assertTrue($spotPrices->asCollection()->get(2)->equals($points->get(2)));
        $this->assertTrue($spotPrices->asCollection()->get(3)->equals($points->get(3)));
    }

    /**
     * @test
     */
    public function it_throw_exception_if_spot_prices_are_overlapping(): void
    {
        // Given
        $this->expectException(SpotPriceAlreadyExistsException::class);
        $this->expectExceptionMessage('Spot price for 2023-08-03 10:00:00 already exists');
        $spotPriceData = new SpotPrices();

        // When
        $points = collect([
            new Point(now(), 1234.123),
            new Point(now(), 1234.123),
        ]);

        $spotPriceData->addMany($points);

        // Then
    }

    /**
     * @test
     */
    public function it_will_always_store_data_sort_by_time(): void
    {
        // Given
        $spotPrices = new SpotPrices();

        // When
        $points = collect([
            new Point(now()->addHours(10), 4),
            new Point(now()->addHours(5), 3),
            new Point(now()->addHours(2), 1),
            new Point(now()->addHours(3), 2),
        ]);

        $spotPrices->addMany($points);

        // Then
        $this->assertTrue($spotPrices->asCollection()->get(0)->equals($points->get(0)));
        $this->assertTrue($spotPrices->asCollection()->get(1)->equals($points->get(1)));
        $this->assertTrue($spotPrices->asCollection()->get(2)->equals($points->get(2)));
        $this->assertTrue($spotPrices->asCollection()->get(3)->equals($points->get(3)));

        $this->assertEquals(4, $spotPrices->asCollection()->count());

    }
    
    /**
    * @test
    */
    public function it_only_allowed_rounded_hours(): void
    {
        $this->expectException(SpotPriceShouldBeRoundHoursException::class);
        $this->expectExceptionMessage('Spot prices should only contain round hours');
        $spotPriceData = new SpotPrices();

        // When
        $points = collect([
            new Point(now(), 1234.123),
            new Point(now()->addHours(1)->addMinute(), 1234.123),
        ]);

        $spotPriceData->addMany($points);
        
        // Then
    }
}
