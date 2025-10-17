<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class FactProvider 
{

    protected $client;
    protected $baseUrl;
    protected $maxRetries = 3;
    protected $attempt = 0;
    
    public function __construct()
    {
        $this->baseUrl = env('CAT_FACT_BASEURI', 'https://catfact.ninja/fact');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }

    public function generateCatFacts() {

        while($this->attempt < $this->maxRetries) { 
            try {
                $response = $this->client->get($this->baseUrl);

                return json_decode($response->getBody()->getContents(), true);
    
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() === 429) {
                    $this->attempt++;
                    if($this->attempt < $this->maxRetries) {
                        sleep(pow(2, $this->attempt));
                    }else{
                        throw new Exception("Rate limited. Please try again later: {$e->getMessage()}");
                    }
                }else {
                    throw new Exception("Error generating cat facts: {$e->getMessage()}");
                    Log::error("Error generating fact: {$e->getMessage()}");
                }
            }
        }

    }
}
