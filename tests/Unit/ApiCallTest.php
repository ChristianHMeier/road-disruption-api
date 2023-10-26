<?php

namespace Tests\Unit;

use App\Models\ApiCall;
use Tests\TestCase;

class ApiCallTest extends TestCase
{
    // private $apiCall;

    /**
     * A valid URL example.
     * @test
     */
    public function valid_url(): void
    {
        $validApiCall = new ApiCall('https://api.tfl.gov.uk/Road/all/Disruption', '', 'api_test', 60);
        $response = $validApiCall->getResource();
        $this->assertIsArray($response, 'Asserting a valid response was given');
    }
    /**
     * An invalid URL example should send the correct message.
     * @test
     */
    public function invalid_url(): void
    {
        $invalidApiCall = new ApiCall('https://foo.bar/Fail', 'API does not exist.', 'api_fail_test', 60);
        $response = $invalidApiCall->getResource();
        $this->assertStringEndsWith('API does not exist.', $response, 'Asserting an error message was given');
    }
}
