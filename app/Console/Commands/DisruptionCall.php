<?php

namespace App\Console\Commands;

use App\Models\DateValidation;
use App\Models\Disruption;
use App\Models\FilterApplication;
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
     * DateValidation private variable
     * 
     * @var DateValidation
     */
    private $dateValidation;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->category = $this->option('category') ?? null;
        $this->endsBefore = $this->option('endsBefore') ?? null;
        
        // Preemptively validate the date format before making the API call
        if ($this->endsBefore !== null) {
            $this->dateValidation = new DateValidation(['inputDate' => $this->endsBefore]);
            $validationResponse = $this->dateValidation->validate();

            if (gettype($validationResponse) == 'string')  {
                $this->error($validationResponse);
                return;
            }
        }

        $disruption = new Disruption;
        $apiResponse = $disruption->makeAllDisruptionsCall();
        $filteredResponse = null;

        if (gettype($apiResponse) == 'array' && count($apiResponse) > 0) { // API response is valid and not empty, check inside for filters
            $filteredResponse = $apiResponse;
           
            if (!is_null($this->category)) {
                $categoryFilter = new FilterApplication([
                    'filterable' => $filteredResponse,
                    'inputValue' => $this->category,
                    'targetKey' => 'category',
                    'operation' => 'isEqual'
                ]);
                $filteredResponse = $categoryFilter->apply();
            }

            if (!is_null($this->endsBefore)) {
                $dateFilter = new FilterApplication([
                    'filterable' => $filteredResponse,
                    'inputValue' => $this->endsBefore.'T23:59:59Z',
                    'targetKey' => 'endDateTime',
                    'operation' => 'isBefore'
                ]);
                $filteredResponse = $dateFilter->apply();
            }
        } else { // API call failed, print error message
            $this->error($apiResponse);
        }

        if (gettype($apiResponse) == 'array' && count($apiResponse) == 0) { // API response is a valid empty array
            $this->info('No accidents to report!');
        } elseif (gettype($filteredResponse) == 'array' && count($filteredResponse) > 0) { // filtered response is a valid non-empty array
            $this->info(json_encode($filteredResponse));
        } elseif (gettype($filteredResponse) == 'array') { // filtered response is an empty array
            $this->error('Category and/or endsBefore values left out all results.');
        }
    }
}
