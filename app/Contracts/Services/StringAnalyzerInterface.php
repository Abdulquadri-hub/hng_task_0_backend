<?php

namespace App\Contracts\Services;

interface StringAnalyzerInterface
{
    public function calculateLength(string $string): int;
    public function normalizeString(string $string): string;
    public function isPalindrome(string $string): bool;
    public function countUniqueCharacters(string $string): int;
    public function wordCounts(string $string): int;
    public function generateSha256Hash(string $string): string;
    public function getCharacterFrequencyMap(string $string): array;
    public function analyzeString(string $string): array;
    public function checkIfStringExists($hash):bool;
    
}
