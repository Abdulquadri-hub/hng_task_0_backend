<?php

use App\Contracts\Services\StringAnalyzerInterface;
use App\Services\StringAnalyzerService;

return  [
    "services" => [
        StringAnalyzerInterface::class => StringAnalyzerService::class
    ]

];