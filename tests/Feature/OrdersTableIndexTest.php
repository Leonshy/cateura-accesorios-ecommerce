<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrdersTableIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_table_has_an_index_on_payment_status(): void
    {
        $indexes = collect(Schema::getIndexes('orders'));

        $hasPaymentStatusIndex = $indexes->contains(
            fn ($index) => $index['columns'] === ['payment_status']
        );

        $this->assertTrue($hasPaymentStatusIndex, 'Se esperaba un índice sobre orders.payment_status.');
    }

    public function test_orders_table_has_an_index_on_status(): void
    {
        $indexes = collect(Schema::getIndexes('orders'));

        $hasStatusIndex = $indexes->contains(
            fn ($index) => in_array('status', $index['columns'], true)
        );

        $this->assertTrue($hasStatusIndex, 'Se esperaba un índice que incluya orders.status.');
    }
}
