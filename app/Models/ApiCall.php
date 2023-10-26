<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ApiCall extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'failMessage',        
        'cacheName',
        'cacheLifetime',
    ];

    /**
     * Execute a GET request.
     */
    public function getResource() {
        $apiGet = Cache::remember($this->cacheName, $this->cacheLifetime, function () {
            try {
                $response = Http::get($this->url);
                if ($response->ok()) {
                    return $response->json();
                }
            } catch (ConnectionException $e) {
                return 'Connection error, call the Network administrator. '.$this->failMessage;
            }
            return $this->failMessage;
        });

        return $apiGet;
    }
}
