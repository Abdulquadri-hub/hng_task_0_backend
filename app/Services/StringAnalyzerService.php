<?php

namespace App\Services;

use Exception;
use App\Models\StringModel;
use App\Contracts\Services\StringAnalyzerInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Soap\Sdl;

class StringAnalyzerService implements StringAnalyzerInterface
{
    public function __construct(){}

    public function calculateLength(string $string): int
    {
        return mb_strlen($string);
    }

    public function isPalindrome(string $string): bool
    {
        $normalizeString = $this->normalizeString($string);
        return $normalizeString === strrev($normalizeString);
    }

    public function countUniqueCharacters(string $string): int
    {
        $characters = mb_str_split($string);
        return count(array_unique($characters));
    }

    public function wordCounts(string $string): int
    {
        $trimmedWords = trim($string);
        if(empty($trimmedWords)) {
            return 0;
        }

        $words = preg_split("/s+/", $trimmedWords);

        return count($words);
    }

    public function generateSha256Hash(string $string): string
    {
        return hash('sha256', $string);
    }

    public function getCharacterFrequencyMap(string $string): array
    {
        $frequencyMap = [];
        $characters = mb_str_split($string);

        foreach($characters as $character) {
            //check if the character is not set then set to 0
            if(!isset($frequencyMap[$character])) {
                $frequencyMap[$character] = 0;
            }

            $frequencyMap[$character]++;
        }

        return $frequencyMap;
    }

    public function analyzeString(string $string): array
    {
        return [
            "length" => $this->calculateLength($string),
            "is_palindrome" => $this->isPalindrome($string),
            "unique_characters" => $this->countUniqueCharacters($string),
            "word_count" => $this->wordCounts($string),
            "sha256_hash" => $this->generateSha256Hash($string),
            "character_frequency_map"  => $this->getCharacterFrequencyMap($string)
        ];
    }

    public function parseNaturalLanguageQuery(string $query): array
    {
        $filters = [];
        $query = strtolower($query);

        // Extract word count
        $wordCount = $this->extractWordCount($query);
        if ($wordCount !== null) {
            $filters['word_count'] = $wordCount;
        }

        // Extract palindrome filter
        $isPalindrome = $this->extractPalindromeFilter($query);
        if ($isPalindrome !== null) {
            $filters['is_palindrome'] = $isPalindrome;
        }

        // Extract length filters
        $lengthFilters = $this->extractLengthFilters($query);
        $filters = array_merge($filters, $lengthFilters);

        // Extract character filter
        $character = $this->extractCharacterFilter($query);
        if ($character !== null) {
            $filters['contains_character'] = $character;
        }

        // Extract vowel filter
        $vowel = $this->extractVowelFilter($query);
        if ($vowel !== null) {
            $filters['contains_character'] = $vowel;
        }
        return $filters;
    }

    public function extractWordCount(string $query): ?int
    {
        // Match "single word", "one word", "two words", etc.
        $wordMap = [
            'single' => 1,
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'four' => 4,
            'five' => 5,
        ];

        foreach ($wordMap as $word => $count) {
            if (preg_match("/\b{$word}\s+word/i", $query)) {
                return $count;
            }
        }

        if (preg_match('/\b(\d+)\s+words?\b/i', $query, $matches)) {
            return (int)$matches[1];
        }

        return null;
    }

    public function extractPalindromeFilter(string $query): ?bool
    {
        if (preg_match('/\bpalindrom(e|ic)\b/i', $query)) {
            return true;
        }
        return null;
    }

    public function extractLengthFilters(string $query): array
    {
        $filters = [];

        if (preg_match('/longer\s+than\s+(\d+)/i', $query, $matches)) {
            $filters['min_length'] = (int)$matches[1] + 1;
        }

        if (preg_match('/at\s+least\s+(\d+)\s+character/i', $query, $matches)) {
            $filters['min_length'] = (int)$matches[1];
        }

        if (preg_match('/shorter\s+than\s+(\d+)/i', $query, $matches)) {
            $filters['max_length'] = (int)$matches[1] - 1;
        }

        if (preg_match('/at\s+most\s+(\d+)\s+character/i', $query, $matches)) {
            $filters['max_length'] = (int)$matches[1];
        }

        return $filters;
    }

    public function extractCharacterFilter(string $query): ?string
    {
        if (preg_match('/contain(?:ing|s)?\s+(?:the\s+)?(?:letter|character)\s+([a-z])/i', $query, $matches)) {
            return strtolower($matches[1]);
        }

        if (preg_match('/with\s+(?:the\s+)?(?:letter|character)\s+([a-z])/i', $query, $matches)) {
            return strtolower($matches[1]);
        }

        return null;
    }

    public function extractVowelFilter(string $query): ?string
    {
        $vowels = ['a', 'e', 'i', 'o', 'u'];
        $vowelMap = [
            'first' => 0,
            'second' => 1,
            'third' => 2,
            'fourth' => 3,
            'fifth' => 4,
        ];

        foreach ($vowelMap as $position => $index) {
            if (preg_match("/\b{$position}\s+vowel\b/i", $query)) {
                return $vowels[$index];
            }
        }

        return null;
    }

    public function checkIfStringExists($hash): bool
    {
        return StringModel::where('sha256_hash', $hash)->exists();
    }

    public function fetchString(string $stringValue):?StringModel {
       return StringModel::where('value', $stringValue)->first();
    }

    public function saveToDb(array $properties, string $hash, string $stringValue): StringModel {

        $saved = DB::transaction(function () use ($properties, $hash, $stringValue) {
            return StringModel::query()->create([
                'id' => $hash,
                'value' => $stringValue,
                'length' => $properties['length'],
                'is_palindrome' => $properties['is_palindrome'],
                'unique_characters' => $properties['unique_characters'],
                'word_count' => $properties['word_count'],
                'sha256_hash' => $properties['sha256_hash'],
                'character_frequency_map' => $properties['character_frequency_map'],
            ]);
        });

        return $saved;
    }

    public function normalizeString(string $string): string {
        return mb_strtolower($string);
    }

    public function validateFilters(array $filters): bool {
        if (isset($filters['min_length']) && isset($filters['mx_length'])) {
            if($filters['min_length'] > $filters['max_length']) {
                throw new InvalidArgumentException("min length cannot be greater than max_length");
            }
        }

        return true;
    }

    public function applyFilters(array $filters) {

        $query = StringModel::query();

        if (isset($filters['is_palindrome'])) {
            $query->where('is_palindrome', filter_var($filters['is_palindrome'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['min_length'])) {
            $query->where('length', '>=', (int)$filters['min_length']);
        }

        if (isset($filters['max_length'])) {
            $query->where('length', '<=', (int)$filters['max_length']);
        }

        if (isset($filters['word_count'])) {
            $query->where('word_count', (int)$filters['word_count']);
        }

        if (isset($filters['contains_character'])) {
            $char = $filters['contains_character'];
            $query->whereRaw('LOWER(value) LIKE ?', ['%' . strtolower($char) . '%']);
        }

        return $query->get()->map(function ($stringModel) {
            return [
                'id' => $stringModel->id,
                'value' => $stringModel->value,
                'properties' => [
                    'length' => $stringModel->length,
                    'is_palindrome' => $stringModel->is_palindrome,
                    'unique_characters' => $stringModel->unique_characters,
                    'word_count' => $stringModel->word_count,
                    'sha256_hash' => $stringModel->sha256_hash,
                    'character_frequency_map' => $stringModel->character_frequency_map,
                ],
                'created_at' => $stringModel->created_at->toIso8601String(),
            ];
        });
    }


}
