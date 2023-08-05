<?php

namespace Domains\Pricing;

use App\Domains\Pricing\Point;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

// Not need to make any changes here, this is just to test that the test setup works and
// to showcase how the Point class works
class PointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2023-08-03 10:00:00');
    }
    /**
    * @test
    */
    public function it_can_create_a_point_object(): void
    {
        // Given
        $time = now();
        $value = 123.3210;

        // When
        $p = new Point($time, $value);

        // Then
        $this->assertEquals($p->time, $time);
        $this->assertEquals($p->value, $value);
    }
    
    /**
    * @test
    */
    public function it_can_convert_to_string(): void
    {
        // Given
        $time = now();
        $value = 123.3210;

        // When
        $p = new Point($time, $value);
        
        // Then
        $this->assertEquals('Time: 2023-08-03 10:00:00: 123.321', (string)$p);
    }
    
    /**
    * @test
    */
    public function it_can_check_if_two_points_are_equals(): void
    {
        // Given
        $time = now();
        $value = 123.3210;

        // When
        $p1 = new Point($time, $value);
        $p2 = new Point($time, $value);

        // Then
        $this->assertTrue($p1->equals($p2));
    }

    /**
     * @test
     */
    public function it_can_check_if_two_point_are_at_the_same_time(): void
    {
        // Given
        $time = now();

        // When
        $p1 = new Point($time, 123.321);
        $p2 = new Point($time->copy()->timezone('Europe/Copenhagen'), 321.123);

        // Then
        $this->assertTrue($p1->atSameTime($p2));
    }

    /**
     * @test
     */
    public function it_can_check_if_two_point_are_not_at_the_same_time(): void
    {
        // Given
        $time = now();

        // When
        $p1 = new Point($time, 123.321);
        $p2 = new Point($time->copy()->timezone('Europe/Copenhagen')->addHours(1), 321.123);

        // Then
        $this->assertFalse($p1->atSameTime($p2));
    }
}
