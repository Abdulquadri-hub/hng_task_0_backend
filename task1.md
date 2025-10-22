# String Analyzer Service - Backend Wizards Stage 1

A RESTful API service that analyzes strings and stores their computed properties.

## Features

- Analyze strings and compute multiple properties (length, palindrome check, character frequency, etc.)
- Store analyzed strings with SHA-256 hash as unique identifier
- Filter strings using query parameters
- Natural language query support
- Full CRUD operations

## Tech Stack

- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL/PostgreSQL/SQLite

## Prerequisites

- PHP >= 8.2
- Composer
- Database (MySQL, PostgreSQL, or SQLite)

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <your-repo-url>
cd string-analyzer-service
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=string_analyzer
DB_USERNAME=root
DB_PASSWORD=your_password
```

For SQLite (simpler for testing):

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Start the Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Endpoints

### 1. Create/Analyze String

**POST** `/api/strings`

```bash
curl -X POST http://localhost:8000/api/strings \
  -H "Content-Type: application/json" \
  -d '{"value": "hello world"}'
```

**Response (201 Created):**
```json
{
  "id": "sha256_hash_value",
  "value": "hello world",
  "properties": {
    "length": 11,
    "is_palindrome": false,
    "unique_characters": 8,
    "word_count": 2,
    "sha256_hash": "b94d27b...",
    "character_frequency_map": {
      "h": 1,
      "e": 1,
      "l": 3,
      "o": 2,
      " ": 1,
      "w": 1,
      "r": 1,
      "d": 1
    }
  },
  "created_at": "2025-10-20T10:00:00Z"
}
```

### 2. Get Specific String

**GET** `/api/strings/{string_value}`

```bash
curl http://localhost:8000/api/strings/hello%20world
```

### 3. Get All Strings with Filters

**GET** `/api/strings?is_palindrome=true&min_length=5&max_length=20&word_count=2&contains_character=a`

```bash
curl "http://localhost:8000/api/strings?is_palindrome=true&word_count=1"
```

**Available Query Parameters:**
- `is_palindrome` (boolean): Filter palindromes
- `min_length` (integer): Minimum string length
- `max_length` (integer): Maximum string length
- `word_count` (integer): Exact word count
- `contains_character` (string): Single character to search for

### 4. Natural Language Filtering

**GET** `/api/strings/filter-by-natural-language?query=all%20single%20word%20palindromic%20strings`

```bash
curl "http://localhost:8000/api/strings/filter-by-natural-language?query=all%20single%20word%20palindromic%20strings"
```

**Supported Natural Language Queries:**
- "all single word palindromic strings"
- "strings longer than 10 characters"
- "palindromic strings that contain the first vowel"
- "strings containing the letter z"
- "two word strings"

### 5. Delete String

**DELETE** `/api/strings/{string_value}`

```bash
curl -X DELETE http://localhost:8000/api/strings/hello%20world
```

**Response (204 No Content)**

## Running Tests

```bash
php artisan test
```

## Project Structure

```
app/
├── Http/
│   └── Controllers/
│       └── StringController.php
├── Models/
│   └── StringModel.php
└── Services/
    └── StringAnalyzerService.php
database/
└── migrations/
    └── 2025_10_20_create_strings_table.php
routes/
└── api.php
```

## Key Files

- **StringAnalyzerService.php**: Core service with all analysis logic
- **StringController.php**: Handles all API endpoints
- **StringModel.php**: Eloquent model for database operations
- **api.php**: API route definitions

## Deployment

### Using Railway

1. Create a new project on Railway
2. Connect your GitHub repository
3. Add MySQL database service
4. Set environment variables:
   ```
   APP_KEY=your_app_key
   DB_CONNECTION=mysql
   DB_HOST=${{MYSQLHOST}}
   DB_PORT=${{MYSQLPORT}}
   DB_DATABASE=${{MYSQLDATABASE}}
   DB_USERNAME=${{MYSQLUSER}}
   DB_PASSWORD=${{MYSQLPASSWORD}}
   ```
5. Deploy!

### Using Heroku

1. Create new Heroku app
2. Add ClearDB MySQL add-on
3. Set buildpacks:
   ```bash
   heroku buildpacks:set heroku/php
   ```
4. Deploy via Git
5. Run migrations:
   ```bash
   heroku run php artisan migrate
   ```

## Environment Variables

Required environment variables:

- `APP_KEY`: Laravel application key
- `DB_CONNECTION`: Database driver (mysql, pgsql, sqlite)
- `DB_HOST`: Database host
- `DB_PORT`: Database port
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password

## Error Responses

- **400 Bad Request**: Invalid request parameters
- **404 Not Found**: String does not exist
- **409 Conflict**: String already exists
- **422 Unprocessable Entity**: Invalid data type or conflicting filters

## Notes

- SHA-256 hash is used as the primary key for each string
- Palindrome check is case-insensitive
- Character frequency includes all characters (including spaces)
- Natural language parsing uses pattern matching and keyword extraction

## Support

For issues or questions, please open an issue on the GitHub repository.

## License

MIT License