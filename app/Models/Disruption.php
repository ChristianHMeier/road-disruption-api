<?php

namespace App\Models;

use App\Models\ApiCall;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disruption extends Model
{
    use HasFactory;

    public function makeAllDisruptionsCall () {
        $apiCall = new ApiCall('https://api.tfl.gov.uk/Road/all/Disruption', 'The external Road Disruption API could not be reached.', 'disruptions_all', 3600);
        $disruptions = $apiCall->getResource();

        return $disruptions;
    }
}
