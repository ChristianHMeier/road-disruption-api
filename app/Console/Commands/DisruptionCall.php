<?php

namespace App\Console\Commands;

use App\Models\Disruption;
use Illuminate\Console\Command;

class DisruptionCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:disruption-call 
                            {--category= : Name of the category, use quotation marks if it has more than one word}
                            {--endsBefore= : Date before the disruption ends, always write in YYYY-MM-DD format, it will autofill the time to 23:59:59}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call the Traffic Disruption API and present relevant data in the terminal';

    /**
     * Category private variable
     * 
     * @var string
     */
    private $category;

    /**
     * EndsBefore private variable
     * 
     * @var string
     */
    private $endsBefore;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->category = $this->option('category') ?? null;
            $this->endsBefore = $this->option('endsBefore') ?? null;
        } catch (e) {
            this->error('Hier!');
        }

        // Preemptively validate the date format before making the API call
        if ($this->endsBefore !== null && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->endsBefore)) {
            $this->error('The value of endsBefore must be declared in YYYY-MM-DD format.');
            return;
        }

        $disruption = new Disruption;
        $apiResponse = $disruption->makeAllDisruptionsCall();
        $filteredResponse = null;
        
        if (gettype($apiResponse) == 'array' && is_null($this->category) && is_null($this->endsBefore)) { // no filters required, copy directly to filtered response
            $filteredResponse = $apiResponse;
            unset($apiResponse);
        } elseif (gettype($apiResponse) == 'array' && !is_null($this->category) && !is_null($this->endsBefore)) { // both category and endDate were given
            $filteredResponse = array_filter($apiResponse, [$this, 'both']);
        }  elseif (gettype($apiResponse) == 'array' && !is_null($this->category)) { // only category was given
            $filteredResponse = array_filter($apiResponse, [$this, 'categories']);
        }  elseif (gettype($apiResponse) == 'array' && !is_null($this->endsBefore)) { // only endDate was given
            $filteredResponse = array_filter($apiResponse, [$this, 'endDates']);
        } else { // API call failed, print error message
            $this->error($apiResponse);
        }

        if (gettype($filteredResponse) == 'array' && count($filteredResponse) > 0) { // filtered response is a valid array and gave a value greater than zero
            $this->info(json_encode($filteredResponse));
        } else {
            $this->error('Invalid category and/or endsBefore value(s)');
        }
    }

    /*
     * Filter function for requests that provide both a category and an endsBefore parameter
     */
    private function both($item) {
        return $this->category == $item['category'] && strtotime($this->endsBefore.'T23:59:59Z') <= strtotime($item['endDateTime']);
    }

    /*
     * Filter function for category parameter only
     */
    private function categories($item) {
        return $this->category == $item['category'];
    }

    /*
     * Filter function for endsBefore parameter only
     */
    private function endDates($item) {
        return strtotime($this->endsBefore.'T23:59:59Z') <= strtotime($item['endDateTime']);
    }
}
