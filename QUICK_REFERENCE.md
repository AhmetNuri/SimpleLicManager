# Quick Reference: User Access Control Implementation

## Problem Statement (Türkçe → English Translation)

### Requirements Implemented:
1. **Customer users restricted to own data** - Customers can only see their dashboard and licenses
2. **User profile management** - Users can edit their own information, not others
3. **License viewing restrictions** - Customers see only their licenses, not others
4. **Log access restricted** - Only admins can view logs
5. **Database-driven license types** - License types from table, shown in combobox

## Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                     Authentication                       │
│                    (Laravel Session)                     │
└────────────────┬────────────────────────────────────────┘
                 │
                 ├──────┬──────────────────────────────────┐
                 │      │                                  │
            ┌────▼────┐ │                          ┌──────▼──────┐
            │  Admin  │ │                          │  Customer   │
            └────┬────┘ │                          └──────┬──────┘
                 │      │                                 │
        ┌────────▼──────▼─────────┐           ┌──────────▼──────────┐
        │   Admin Middleware      │           │   Auth Middleware   │
        │   (admin role check)    │           │   (logged in check) │
        └────────┬────────────────┘           └──────────┬──────────┘
                 │                                       │
        ┌────────▼────────────────┐           ┌─────────▼──────────┐
        │ Admin Routes:           │           │ User Routes:       │
        │ • /admin/users          │           │ • /dashboard       │
        │ • /admin/licenses       │           │ • /dashboard/licenses│
        │ • /admin/logs           │           │ • /dashboard/profile│
        └─────────────────────────┘           └────────────────────┘
```

## Route Changes

### Before:
```php
// routes/web.php
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('licenses', LicenseController::class);
    Route::get('logs', [LicLogController::class, 'index']);
});
```

### After:
```php
// routes/web.php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Same routes but now protected by admin middleware
});

// NEW customer routes:
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/licenses', [UserLicenseController::class, 'index']);
    Route::get('/licenses/{id}', [UserLicenseController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});
```

## Navigation Changes

### Before:
```blade
<!-- All users saw all links -->
<a href="{{ route('admin.users.index') }}">Kullanıcılar</a>
<a href="{{ route('admin.licenses.index') }}">Lisanslar</a>
<a href="{{ route('admin.logs.index') }}">Loglar</a>
```

### After:
```blade
@if(Auth::user()->isAdmin())
    <a href="{{ route('admin.users.index') }}">Kullanıcılar</a>
    <a href="{{ route('admin.licenses.index') }}">Lisanslar</a>
    <a href="{{ route('admin.logs.index') }}">Loglar</a>
@else
    <a href="{{ route('dashboard.licenses.index') }}">Lisanslarım</a>
@endif
```

## Data Access Patterns

### Admin Access (Full):
```php
// Can see all licenses
$licenses = License::with('user')->latest()->paginate(15);

// Can see all users
$users = User::withCount('licenses')->latest()->paginate(15);

// Can see all logs
$logs = LicLog::with(['license', 'user'])->latest()->paginate(30);
```

### Customer Access (Restricted):
```php
// Only sees their own licenses
$licenses = Auth::user()->licenses()->latest()->paginate(15);

// Only sees their own license detail
$license = Auth::user()->licenses()->findOrFail($id);

// Cannot access logs at all (route blocked by middleware)
```

## License Type Management

### Before (Hardcoded):
```php
// In controller
'license_type' => 'required|in:demo,monthly,yearly,lifetime',

// In view
<option value="demo">Demo</option>
<option value="monthly">Aylık</option>
<option value="yearly">Yıllık</option>
<option value="lifetime">Ömür Boyu</option>
```

### After (Database-Driven):
```php
// In controller
$licenseTypeCodes = LicenseType::where('active', true)->pluck('code')->toArray();
'license_type' => 'required|in:' . implode(',', $licenseTypeCodes),

// In view
@foreach($licenseTypes as $type)
    <option value="{{ $type->code }}">{{ $type->name }}</option>
@endforeach
```

## Security Implementation

### Vertical Privilege Escalation Prevention
```php
// Middleware checks role BEFORE controller
public function handle(Request $request, Closure $next): Response
{
    if (!$request->user() || !$request->user()->isAdmin()) {
        abort(403, 'Bu sayfaya erişim yetkiniz yok.');
    }
    return $next($request);
}
```

### Horizontal Privilege Escalation Prevention
```php
// Eloquent relationship automatically filters by user_id
public function show($id)
{
    $user = Auth::user();
    // This query automatically adds WHERE user_id = ?
    $license = $user->licenses()->findOrFail($id);
    return view('user.licenses.show', compact('license'));
}
```

## Testing Checklist

### As Customer User:
- [ ] Can access `/dashboard` ✓
- [ ] Can access `/dashboard/licenses` ✓
- [ ] Can access `/dashboard/licenses/{own_license_id}` ✓
- [ ] Cannot access `/admin/users` (403) ✓
- [ ] Cannot access `/admin/licenses` (403) ✓
- [ ] Cannot access `/admin/logs` (403) ✓
- [ ] Navigation shows only: Dashboard, Lisanslarım ✓
- [ ] Can edit own profile ✓
- [ ] Cannot see other users' licenses ✓

### As Admin User:
- [ ] Can access all `/admin/*` routes ✓
- [ ] Can manage all users ✓
- [ ] Can manage all licenses ✓
- [ ] Can view all logs ✓
- [ ] Navigation shows: Dashboard, Kullanıcılar, Lisanslar, Loglar ✓
- [ ] License type dropdown loads from database ✓

## Database Schema Addition

```sql
CREATE TABLE license_types (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

INSERT INTO license_types (code, name, description, active) VALUES
('demo', 'Demo', 'Demo lisans', TRUE),
('monthly', 'Aylık', '1 ay geçerli', TRUE),
('yearly', 'Yıllık', '1 yıl geçerli', TRUE),
('lifetime', 'Ömür Boyu', 'Süresiz lisans', TRUE);
```

## Key Files Modified

| File | Purpose | Changes |
|------|---------|---------|
| `bootstrap/app.php` | Middleware registration | Added 'admin' middleware alias |
| `routes/web.php` | Route definitions | Added admin middleware, customer routes |
| `app/Http/Controllers/Admin/LicenseController.php` | Admin license management | Fetch license types from DB |
| `app/Http/Controllers/User/ProfileController.php` | User profile | Added update method |
| `resources/views/layouts/app.blade.php` | Navigation | Conditional menu by role |
| `resources/views/user/profile.blade.php` | Profile view | Added edit form |
| `resources/views/admin/licenses/create.blade.php` | License form | Dynamic license types |
| `resources/views/admin/licenses/edit.blade.php` | License form | Dynamic license types |

## Key Files Created

| File | Purpose |
|------|---------|
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Admin authorization |
| `app/Models/LicenseType.php` | License type model |
| `app/Http/Controllers/User/LicenseController.php` | Customer license viewing |
| `database/migrations/2026_02_09_174500_create_license_types_table.php` | License types table |
| `resources/views/user/licenses/index.blade.php` | Customer license list |
| `resources/views/user/licenses/show.blade.php` | Customer license detail |

## Deployment Instructions

1. Pull latest changes
2. Run migration: `php artisan migrate`
3. Clear caches: `php artisan cache:clear && php artisan config:clear`
4. Test with both admin and customer accounts
5. Verify license types appear in forms

## Support

For issues or questions, refer to `AUTHORIZATION_CHANGES.md` for detailed documentation.
