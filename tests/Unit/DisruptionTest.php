<?php

namespace Tests\Unit;

use App\Models\Disruption;
use Tests\TestCase;

class DisruptionTest extends TestCase
{
    /**
     * Ensure an array was returned from the All Disruptions API call.
     * @test
     */
    public function disruption_call_returns_array(): void
    {
        $disruption = new Disruption;

        $this->assertIsArray($disruption->makeAllDisruptionsCall(), 'Assert an array is returned');
    }
}
