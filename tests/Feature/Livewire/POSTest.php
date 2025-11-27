<?php

namespace Tests\Feature\Livewire;

use App\Livewire\POS;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class POSTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(POS::class)
            ->assertStatus(200);
    }
}
