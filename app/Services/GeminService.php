<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminService
{
    protected string $gemini_key;
    protected string $Url;

    public function __construct()
    {
        $this->gemini_key=env("GEMINI_API_KEY");
        $this->Url=env("REQUEST_URL");
    }


    protected function Request(string $data)
    {
        
        
        
        $response = Http::withHeaders([
            'x-goog-api-key' => $this->gemini_key,
            'Content-Type'=> 'application/json'
        ])->post($this->Url, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $data
                        ]
                    ]
                ]
            ]
        ]);

      return $response->json();
    }

    public function Response(string $data)
    {

        $data=$this->Request($data);

        return  $data;
        
        
         
        
    }
}
