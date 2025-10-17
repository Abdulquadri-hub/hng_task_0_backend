# 🚀 Building a Dynamic Profile API with Laravel - Backend Wizards Stage 0

🎉 I built a RESTful API endpoint that fetches user profile data and integrates with an external cat facts API.

## What I Built

Created a `GET https://hngtask0backend.pxxl.click/api/me` endpoint that returns:

- User profile information (name, email, tech stack)
- Current UTC timestamp in ISO 8601 format
- A random cat fact fetched from an external API (https://catfact.ninja/fact)
- Every request fetches a NEW fact (no caching)

## Tech Stack Used

- **Backend**: Laravel 12 with PHP 8.2
- **HTTP Client**: Guzzle for external API calls
- **Database**: SQLite
- **Hosting**: PXXL App

## Key Learnings

### 1️⃣ Service Layer Architecture
Separated concerns using service classes (`UserService`, `FactProvider`) to keep controllers lean and business logic testable.

### 2️⃣ External API Integration
Implemented proper error handling with exponential backoff retry logic for rate limiting (429 errors). This ensures resilience when third-party APIs are slow or rate-limited.

### 3️⃣ Graceful Error Handling
Wrapped all operations in try-catch blocks with meaningful error messages. API remains stable even when external services fail.

### 4️⃣ Dynamic Timestamps
Used Carbon library with UTC timezone to ensure consistency across all environments: `Carbon::now('UTC')->toIso8601String()`

### 5️⃣ Dependency Injection
Laravel's service container handles dependency injection automatically, making code more testable and maintainable.

## Response Example

```json
{
  "status": "success",
  "message": "User profile generated successfully",
  "data": {
    "user": {
      "name": "Oluwadamilare Quadri Bolaji",
      "email": "abdulquadri.aq@gmail.com",
      "stack": "PHP & Laravel"
    },
    "timestamp": "2025-10-17T14:30:45.123Z",
    "fact": "Approximately 1/3 of cat owners think their pets can read their minds."
  }
}
```

## The Challenge

The main challenge was handling API timeouts and rate limiting gracefully. I implemented:
- 10-second timeout for external API calls
- Exponential backoff retry (2s, 4s, 8s delays)
- Max 3 retries before returning error
- Comprehensive logging for debugging

## What This Taught Me

✅ Importance of proper error handling in production APIs  
✅ How to effectively integrate third-party services  
✅ Value of clean architecture and separation of concerns  
✅ Why environment configuration matters for scalability  

## GitHub & Deployment

- **Repository**: https://github.com/Abdulquadri-hub/hng_task_0_backend.git
- **Live API**: https://hngtask0backend.pxxl.click/api/me
- **Setup**: Simple `composer install` → `php artisan migrate` → `php artisan serve`

Testing the endpoint multiple times confirms the timestamp updates dynamically and a new cat fact is fetched on every request.

Ready for Stage 1! 

#BackendDeveloper #Laravel #API #RESTful #HNGInternship #PHP #APIDevelopment #WebDevelopment
