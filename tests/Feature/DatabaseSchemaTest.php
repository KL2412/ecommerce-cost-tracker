<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_tables_have_the_required_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('products', [
            'id',
            'sku',
            'name',
            'description',
            'quantity_on_hand',
            'inventory_value',
            'average_cost',
        ]));

        $this->assertTrue(Schema::hasColumns('inventory_transactions', [
            'id',
            'product_id',
            'created_by',
            'type',
            'transaction_date',
            'quantity',
            'unit_cost',
            'total_cost',
        ]));

        $this->assertFalse(Schema::hasColumn('inventory_transactions', 'quantity_after'));
        $this->assertFalse(Schema::hasColumn('inventory_transactions', 'inventory_value_after'));
        $this->assertFalse(Schema::hasColumn('inventory_transactions', 'average_cost_after'));
    }
}
