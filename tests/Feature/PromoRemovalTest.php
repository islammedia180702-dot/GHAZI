<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PromoRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_promos_table_is_not_available(): void
    {
        $this->assertFalse(Schema::hasTable('promos'));
    }
}
