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
    * URL resource
    * 
    * @var string
    */
   private $url;

   /**
   * Message in case the request fails
   * 
   * @var string
   */
  private $failMessage;

   /**
    * Name of cache resource where a GET request will be saved
    * 
    * @var ?string
    */
   private $cacheName;

   /**
    * Lifetime in seconds of the cache resource from a GET request
    * 
    * @var ?int
    */
   private $cacheLifetime;

   public function __construct(string $url, string $failMessage = 'Could not retrieve resource', $cacheName = null, $cacheLifetime = null) {
    $this->url = $url;
    $this->failMessage = $failMessage;
    $this->cacheName = $cacheName;
    $this->cacheLifetime = $cacheLifetime;
   }

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
