<?php

/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 1/12/2015
 * Time: 9:59 PM
 */

use Dricle\Cart\Cart;
use Dricle\Tests\Helpers\SessionMock;
use Mockery as m;

class CartTestOtherFormat extends PHPUnit\Framework\TestCase
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

    public function test_cart_sub_total()
    {
        $items = [
            [
                'id' => 456,
                'name' => 'Sample Item 1',
                'price' => 67.99,
                'quantity' => 1,
                'attributes' => [],
            ],
            [
                'id' => 568,
                'name' => 'Sample Item 2',
                'price' => 69.25,
                'quantity' => 1,
                'attributes' => [],
            ],
            [
                'id' => 856,
                'name' => 'Sample Item 3',
                'price' => 50.25,
                'quantity' => 1,
                'attributes' => [],
            ],
        ];

        $this->cart->add($items);

        $this->assertEquals('187,490', $this->cart->getSubTotal(), 'Cart should have sub total of 187,490');

        // if we remove an item, the sub total should be updated as well
        $this->cart->remove(456);

        $this->assertEquals('119,500', $this->cart->getSubTotal(), 'Cart should have sub total of 119,500');
    }

    public function test_sub_total_when_item_quantity_is_updated()
    {
        $items = [
            [
                'id' => 456,
                'name' => 'Sample Item 1',
                'price' => 67.99,
                'quantity' => 3,
                'attributes' => [],
            ],
            [
                'id' => 568,
                'name' => 'Sample Item 2',
                'price' => 69.25,
                'quantity' => 1,
                'attributes' => [],
            ],
        ];

        $this->cart->add($items);

        $this->assertEquals('273,220', $this->cart->getSubTotal(), 'Cart should have sub total of 273.22');

        // when cart's item quantity is updated, the subtotal should be updated as well
        $this->cart->update(456, ['quantity' => 2]);

        $this->assertEquals('409,200', $this->cart->getSubTotal(), 'Cart should have sub total of 409.2');
    }

    public function test_sub_total_when_item_quantity_is_updated_by_reduced()
    {
        $items = [
            [
                'id' => 456,
                'name' => 'Sample Item 1',
                'price' => 67.99,
                'quantity' => 3,
                'attributes' => [],
            ],
            [
                'id' => 568,
                'name' => 'Sample Item 2',
                'price' => 69.25,
                'quantity' => 1,
                'attributes' => [],
            ],
        ];

        $this->cart->add($items);

        $this->assertEquals('273,220', $this->cart->getSubTotal(), 'Cart should have sub total of 273.22');

        // when cart's item quantity is updated, the subtotal should be updated as well
        $this->cart->update(456, ['quantity' => -1]);

        // get the item to be evaluated
        $item = $this->cart->get(456);

        $this->assertEquals(2, $item['quantity'], 'Item quantity of with item ID of 456 should now be reduced to 2');
        $this->assertEquals('205,230', $this->cart->getSubTotal(), 'Cart should have sub total of 205.23');
    }
}
