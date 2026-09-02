---
name: developing-api-endpoints
description: Standardizes building robust, RESTful, and secure Laravel API endpoints. Covers API controllers, form request validation, API resources, rate limiting, and standard JSON responses. Use when writing or updating API routes and controllers.
---

# Developing API Endpoints

## When to use this skill
- Adding new RESTful API routes (`routes/api.php`).
- Creating API Controllers (`app/Http/Controllers/Api/*`).
- Writing Form Requests for input validation (`app/Http/Requests/*`).
- Transforming responses using Eloquent API Resources (`app/Http/Resources/*`).

## Workflow Checklist
- [ ] Group routes under middleware (e.g. `auth:sanctum`, `throttle:api`).
- [ ] Create dedicated Form Request for request body validation.
- [ ] Wrap response data in an `Eloquent Resource` or `ResourceCollection`.
- [ ] Return consistent standard HTTP status codes (`200 OK`, `201 Created`, `400 Bad Request`, `404 Not Found`, `422 Unprocessable Content`).

## Code Template

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendanceApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $attendances = Attendance::with('user')->latest()->paginate(15);
        return AttendanceResource::collection($attendances);
    }

    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $attendance = Attendance::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attendance logged successfully.',
            'data' => new AttendanceResource($attendance)
        ], 201);
    }
}
```

## Guidelines
- Never return raw Eloquent models directly to clients; always format via API Resources.
- Ensure proper Exception handling and error payload structures.
