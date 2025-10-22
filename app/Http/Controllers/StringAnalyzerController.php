<?php

namespace App\Http\Controllers;

use App\Services\StringAnalyzerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StringAnalyzerController extends Controller
{
    public function __construct(protected StringAnalyzerService $analyzer){}
    
    public function save(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'value' => "required|string"
            ]);

            if($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "error" => $validator->errors()
                ], 400);
            }

            $stringValue = $request->value;

            $analyzedProperties = $this->analyzer->analyzeString($stringValue);

            $hash = $analyzedProperties['sha256_hash'];
            if($this->analyzer->checkIfStringExists($hash)){
                return response()->json([
                    "status" => false,
                    "error" => "string already exists in the system"
                ], 409);
            }

            $savedString = $this->analyzer->saveToDb($analyzedProperties, $hash, $stringValue);
            if(!$savedString) {
                return response()->json([
                    "status" => false,
                    "error" => "error saving analyzed strings"
                ], 422);
            }

            return response()->json([
                'id' => $savedString->id,
                'value' => $savedString->value,
                'properties' => [
                    'length' => $savedString->length,
                    'is_palindrome' => $savedString->is_palindrome,
                    'unique_characters' => $savedString->unique_characters,
                    'word_count' => $savedString->word_count,
                    'sha256_hash' => $savedString->sha256_hash,
                    'character_frequency_map' => $savedString->character_frequency_map,
                ],
                'created_at' => $savedString->created_at->toIso8601String(),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 422);
        }
    }

    public function fetch(string $stringValue) {
        try {
            if(empty($stringValue)) {
                return response()->json([
                    'status' => false,
                    'error' => 'string value is required'
                ], 400);
            }

            $string = $this->analyzer->fetchString($stringValue);
            if(!$string) {
                return response()->json([
                    'error' => 'String does not exist in the system'
                ], 404);
            }

            return response()->json([
                'id' => $string->id,
                'value' => $string->value,
                'properties' => [
                    'length' => $string->length,
                    'is_palindrome' => $string->is_palindrome,
                    'unique_characters' => $string->unique_characters,
                    'word_count' => $string->word_count,
                    'sha256_hash' => $string->sha256_hash,
                    'character_frequency_map' => $string->character_frequency_map,
                ],
                'created_at' => $string->created_at->toIso8601String(),
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 422);
        }
    }

    public function all(Request $request) {
        try {

            $request->merge([
                'is_palindrome' => filter_var($request->is_palindrome, FILTER_VALIDATE_BOOLEAN)
            ]);
            
            $validator = Validator::make($request->all(), [
                'is_palindrome' => 'sometimes|boolean',
                'min_length' => 'sometimes|integer|min:0',
                'max_length' => 'sometimes|integer|min:0',
                'word_count' => 'sometimes|integer|min:0',
                'contains_character' => 'sometimes|string|size:1',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => 'Invalid query parameter values or types'
                ], 400);
            }

           $filters = $request->only([
                'is_palindrome',
                'min_length',
                'max_length',
                'word_count',
                'contains_character'
           ]);

           $this->analyzer->validateFilters($filters);

           $strings = $this->analyzer->applyFilters($filters);

            return response()->json([
                'data' => $strings,
                'count' => $strings->count(),
                'filters_applied' => $filters,
            ], 200);

    
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 422);
        }
    }

    public function fetchByNaturalLanguage(Request $request) {
        try {
            $query = $request->input('query');
            if(empty($query)) {
                return response()->json([
                    "status" => false,
                    "error" => "query parameter is required"
                ], 400);
            }

            $filters = $this->analyzer->parseNaturalLanguageQuery($query);

            if (empty($filters)) {
                return response()->json([
                    'error' => 'Unable to parse natural language query'
                ], 400);
            }

            $this->analyzer->validateFilters($filters);

            $strings = $this->analyzer->applyFilters($filters);
            return response()->json([
                'data' => $strings,
                'count' => $strings->count(),
                'interpreted_query' => [
                    'original' => $query,
                    'parsed_filters' => $filters,
                ],
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 422);
        }
    }

    public function destroy(string $stringValue)
    {
        try {
            $string = $this->analyzer->fetchString($stringValue);
            if (!$string) {
                return response()->json([
                    'error' => 'String does not exist in the system'
                ], 404);
            }
    
            $string->delete();
    
            return response()->json(null, 204);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 422);
        }

    }
}
