<?php

declare(strict_types=1);

namespace Tests\Unit\Support\ValueObjects;

use InvalidArgumentException;
use Support\ValueObjects\Price;
use Tests\TestCase;

class PriceTest extends TestCase
{
    public function test_price()
    {
        $price = Price::make(10000);

        $this->assertInstanceOf(Price::class, $price);
        $this->assertEquals(100, $price->value());
        $this->assertEquals(10000, $price->raw());
        $this->assertEquals('RUB', $price->currency());
        $this->assertEquals('₽', $price->symbol());
        $this->assertEquals('100.00 ₽', $price);

        $this->expectException(InvalidArgumentException::class);

        Price::make(-10000);
        Price::make(10000, 'USD');
    }
}
