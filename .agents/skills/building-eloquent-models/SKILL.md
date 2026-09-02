---
name: building-eloquent-models
description: Standardizes the creation and enhancement of Laravel Eloquent Models. Ensures correct relationships, fillable properties, attributes casting, local scopes, and accessor/mutator patterns. Use when creating, refactoring, or defining relationships for Eloquent models in Laravel.
---

# Building Eloquent Models

## When to use this skill
- Creating new Eloquent models (`app/Models/*.php`).
- Defining relationships (`hasMany`, `belongsTo`, `belongsToMany`, `morphTo`, etc.).
- Adding attributes casting, accessor/mutator methods, soft deletes, or query scopes.

## Workflow Checklist
- [ ] Ensure model extends `Illuminate\Database\Eloquent\Model`.
- [ ] Define `$table` name explicitly if non-standard.
- [ ] Specify `$fillable` or `$guarded` array to protect mass assignment.
- [ ] Define attribute casting in `$casts` or via `protected function casts(): array`.
- [ ] Write typed relationships with return type hints (e.g. `: HasMany`, `: BelongsTo`).
- [ ] Add query scopes using `scope*` naming convention.

## Code Template

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExampleModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'example_models';

    protected $fillable = [
        'user_id',
        'title',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
```

## Guidelines
- Always type-hint relationship return types.
- Avoid using `$guarded = []` in production unless strictly controlled.
- Keep business logic in Services or Actions when models start becoming bloated.
