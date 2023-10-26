<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filterable',
        'inputValue',        
        'targetKey',
        'operation',
    ];

    public function apply() {
        $this->filterable = array_values(array_filter($this->filterable, [$this, $this->operation]));
        return $this->filterable;
    }

    private function isEqual($item) {
        return $this->inputValue == $item[$this->targetKey];
    }

    private function isBefore($item) {
        return strtotime($this->inputValue) >= strtotime($item[$this->targetKey]);
    }
}
