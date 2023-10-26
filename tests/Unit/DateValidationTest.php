<?php

namespace Tests\Unit;

use App\Models\DateValidation;
use Tests\TestCase;

class DateValidationTest extends TestCase
{
    /**
     * Validate Base RegExp for dates satisfies YYYY-MM-DD pattern.
     * @test
     */
    public function validate_pattern(): void
    {
        $dateValidation = new DateValidation(['inputDate' => '9999-99-99']);
        $this->assertStringContainsString('Inputted date does not hold up to a valid YYYY-MM-DD format.', $dateValidation->validate(), 'Asserting a valid YYYY-MM-DD was given.') ;
    }
    /**
     * Validate Short month does not accept 31 days.
     * @test
     */
    public function validate_short_month(): void
    {
        $dateValidation = new DateValidation(['inputDate' => '2020-11-31']);
        $this->assertMatchesRegularExpression('/Inputted month (\d+) does not have 31 days./', $dateValidation->validate(), 'Asserting short months have less than 31 days.') ;
    }
    /**
     * Validate February 29th on regular leap year.
     * @test
     */
    public function validate_leap_february(): void
    {
        $dateValidation = new DateValidation(['inputDate' => '2020-02-29']);
        $this->assertTrue($dateValidation->validate(), 'Asserting February 2020 can accept 29 days.') ;
    }
    /**
     * Validate February never accepts 30 days.
     * @test
     */
    public function validate_30_days_february(): void
    {
        $dateValidation = new DateValidation(['inputDate' => '2020-02-30']);
        $this->assertMatchesRegularExpression('/February (\d+) does not have (\d+) days./', $dateValidation->validate(), 'Asserting February 2020 does not accept 30 days.') ;
    }
    /**
     * Validate February 1900 was not a leap year.
     * @test
     */
    public function validate_february_1900(): void
    {
        $dateValidation = new DateValidation(['inputDate' => '1900-02-29']);
        $this->assertMatchesRegularExpression('/February (\d+) does not have (\d+) days./', $dateValidation->validate(), 'Asserting February 1900 was not leap.') ;
    }
    /**
     * Validate February 2000 as leap year.
     * @test
     */
    public function validate_february_2000(): void
    {
        $dateValidation = new DateValidation(['inputDate' => '2000-02-29']);
        $this->assertTrue($dateValidation->validate(), 'Asserting February 2000 was leap.') ;
    }
}
