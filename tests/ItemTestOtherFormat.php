<?php

/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 3/18/2015
 * Time: 6:17 PM
 */

use Dricle\Cart\Cart;
use Dricle\Tests\Helpers\SessionMock;
use Mockery as m;

class ItemTestOtherFormat extends PHPUnit\Framework\TestCase
{
    /**
     * @var Dricle\Cart\Cart
     */
    protected $cart;

    protected function setUp(): void
    {
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $events->shouldReceive('dispatch');

        $this->cart = new Cart(
            new SessionMock,
            $events,
            'shopping',
            'SAMPLESESSIONKEY',
            [
                'format_numbers' => true,
                'decimals' => 3,
                'dec_point' => ',',
                'thousands_sep' => '.',
            ]
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function test_item_get_sum_price_using_property()
    {
        $this->cart->add(455, 'Sample Item', 100.99, 2, []);

        $item = $this->cart->get(455);

        $this->assertEquals('201,980', $item->getPriceSum(), 'Item summed price should be 201.98');
    }

    public function test_item_get_sum_price_using_array_style()
    {
        $this->cart->add(455, 'Sample Item', 100.99, 2, []);

        $item = $this->cart->get(455);

        $this->assertEquals('201,980', $item->getPriceSum(), 'Item summed price should be 201.98');
    }
}
