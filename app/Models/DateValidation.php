<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateValidation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inputDate',
    ];
    
    /**
     * array of months with less than 31 days
     * 
     * @var array
     */
    protected $shortMonths = ['02', '04', '06', '09', '11'];

    /**
     * Public function that does all necessary date validations
     */
    public function validate() {
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->inputDate)) { // Apply general RegEx check for the inputted date
            return 'Inputted date does not hold up to a valid YYYY-MM-DD format.';
        }

        $dateValues = explode('-', $this->inputDate);

        if (in_array($dateValues[1], $this->shortMonths, true) && !$this->checkShortMonths($dateValues[2])) {
            return "Inputted month {$dateValues[1]} does not have 31 days.";
        }

        if ($dateValues[1] == '02' && !$this->checkFebruary($dateValues[2], $dateValues[0])) {
            return "February {$dateValues[0]} does not have {$dateValues[2]} days.";
        }

        return true;
    }

    /**
     * Private function that covers for strtotime's inability to tell invalid dates like November 31st from December 1st
     */
    private function checkShortMonths($day) {
        // Return boolean informing if the provided day and month are a valid combination
        return $day <= 30;
    }

    /**
     * Private function that verifies the Gregorian Calendar rules for February
     * If the year is perfectly divisible by 4 but not 100, February has 29 days
     * The exception to the exception is when the year is perfectly divisible by 400
     */
    private function checkFebruary($day, $year) {
        if ($day > 29) { // rule out February 30th
            return false;
        } elseif ($day < 29) { // Any day number below 29 is automatically valid
            return true;
        }
        // February 29th was inputted, run the Gregorian Calendar rules to it
        return $year % 4 == 0 && $year % 100 != 0 ? true : $year % 400 == 0;
    }
}
