# CLAUDE.md (owc-api)

Backend standards — the Laravel 13 conventions to follow when adding or changing API code. The repo-root [CLAUDE.md](../CLAUDE.md) covers commands, the auth flow, and the domain model. Stack: Laravel 13, PHP 8.3+ (8.5 in the Sail container), Sanctum, PostgreSQL.

## Control flow — no `else`

Never write `else` / `elseif`. Use **guard clauses + early returns** and ternaries. The codebase has zero `else` — keep it that way.

```php
if (! $code) {
    return response()->json(['message' => 'Missing code'], 422);
}
// happy path continues, unindented
```

## Controllers stay thin

Controllers coordinate; they don't hold business rules. Validate → authorize → delegate → respond.

- **Validation lives in a FormRequest** (one per write endpoint, e.g. [StorePlaySessionRequest](app/Http/Requests/StorePlaySessionRequest.php)). Rules are the single source of truth; by the time the controller body runs, input is valid. Never inline `$request->validate()`.
- **Authorization is enforced, never assumed.** Scope by the user relationship (`$request->user()->playSessions()->findOrFail($id)`) or the existing inline guard (`if ($x->user_id !== $request->user()->id) return 404` — 404, not 403, so existence doesn't leak). **Never trust client-supplied ids** — derive `user_id` from `$request->user()`, never the request body.
- **Business logic over ~15 lines → an Action/Service class** (`app/Actions/…`, invokable `__invoke`); the controller method becomes a one-liner that calls it. Single-purpose endpoints → single-action controllers.

## Responses

- Shape output with **API Resources** (`php artisan make:resource`) — explicit fields, no accidental leaks, safe schema evolution. Current endpoints return raw models; convert an endpoint to a Resource when you next touch it.
- **Always paginate** list endpoints. One consistent JSON shape across the API.

## Models, enums, migrations

- Typed **Enums** in [app/Enums/](app/Enums/), cast on the model. Keep enum *logic* (computed values, label/role maps) in the enum, not in controllers.
- New game-related migrations follow the `2026_02_11_*` numbering so foreign keys resolve in order.

## Testing — behavior, not the framework

- **Every endpoint gets a Feature test** covering: auth required, ownership/authorization (404 for another user's resource), validation boundaries, and the happy-path response shape. **This rule replaces a coverage %** — there is no coverage gate.
- AAA structure; `RefreshDatabase`; assert the real HTTP contract (status, JSON shape, DB state). A token test must prove the token *works* (hit a protected route), not just that a string came back.
- **Do not test the framework.** No tests for Eloquent casts, `hasMany`/`belongsTo`, FK cascades, factories, or native enum `cases()`/`from()`. Those test Laravel. Unit-test only genuine logic (computed attributes, enum helper methods, seeders as data contracts).

## No comments

Default to zero — see [../CLAUDE.md](../CLAUDE.md). Apply the same to tests: a clear `test_*` name replaces a comment.

## Best-practice references

- Laravel 13 docs (verify before guessing): [Controllers](https://laravel.com/docs/13.x/controllers), [Validation / FormRequest](https://laravel.com/docs/13.x/validation), [Eloquent: API Resources](https://laravel.com/docs/13.x/eloquent-resources), [Testing](https://laravel.com/docs/13.x/testing).
- Community: [alexeymezenin/laravel-best-practices](https://github.com/alexeymezenin/laravel-best-practices), [benjamincrozat.com/laravel-best-practices](https://benjamincrozat.com/laravel-best-practices).
