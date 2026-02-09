# Authorization and Access Control Changes

## Overview
This implementation adds comprehensive role-based access control to the SimpleLicManager system, ensuring customer users can only access their own data while preserving full admin capabilities.

## Key Changes

### 1. Middleware Implementation
**File:** `app/Http/Middleware/EnsureUserIsAdmin.php`
- New middleware that checks if the authenticated user has admin role
- Returns 403 error for non-admin users attempting to access admin routes
- Registered as 'admin' alias in `bootstrap/app.php`

### 2. Protected Routes
**File:** `routes/web.php`
- All admin routes now require both 'auth' and 'admin' middleware
- Customer users attempting to access admin routes get 403 Forbidden
- Routes protected:
  - `/admin/users` - User management
  - `/admin/licenses` - License management (admin view)
  - `/admin/logs` - Log viewing

### 3. Customer License Management
**New Files:**
- `app/Http/Controllers/User/LicenseController.php` - Customer license controller
- `resources/views/user/licenses/index.blade.php` - Customer license list
- `resources/views/user/licenses/show.blade.php` - Customer license detail

**Features:**
- Customers can view their own licenses at `/dashboard/licenses`
- Uses relationship filtering: `$user->licenses()` ensures customers only see their own data
- Read-only access - no edit or delete capabilities
- Can view license details and recent activity logs

### 4. Profile Management
**File:** `app/Http/Controllers/User/ProfileController.php`
- Added `update()` method for profile editing
- Users can update their own email, name, and company
- Cannot change their own role
- Password change functionality preserved

**File:** `resources/views/user/profile.blade.php`
- Updated with editable form fields
- Shows user role (read-only)
- Separate sections for profile and password change

### 5. Navigation Menu
**File:** `resources/views/layouts/app.blade.php`
- Conditional menu based on user role using `@if(Auth::user()->isAdmin())`
- Admin users see: Dashboard, Kullanıcılar, Lisanslar, Loglar
- Customer users see: Dashboard, Lisanslarım
- Email in header is now a link to profile page
- All users can access their profile

### 6. License Type Management
**New Files:**
- `database/migrations/2026_02_09_174500_create_license_types_table.php`
- `app/Models/LicenseType.php`

**Features:**
- License types stored in database (demo, monthly, yearly, lifetime)
- Active flag allows disabling types without deleting
- Description field for additional info
- Forms dynamically load from database using `LicenseType::getActiveTypes()`
- Validation checks against database values

**Updated Files:**
- `app/Http/Controllers/Admin/LicenseController.php` - Uses LicenseType model
- `resources/views/admin/licenses/create.blade.php` - Dynamic dropdown
- `resources/views/admin/licenses/edit.blade.php` - Dynamic dropdown

## Authorization Matrix

| Feature | Admin | Customer |
|---------|-------|----------|
| View own dashboard | ✅ | ✅ |
| View own profile | ✅ | ✅ |
| Edit own profile | ✅ | ✅ |
| Change own password | ✅ | ✅ |
| View own licenses | ✅ | ✅ |
| View all users | ✅ | ❌ (403) |
| Create/Edit/Delete users | ✅ | ❌ (403) |
| View all licenses | ✅ | ❌ (403) |
| Create/Edit/Delete licenses | ✅ | ❌ (403) |
| View logs | ✅ | ❌ (403) |

## Security Considerations

### Horizontal Privilege Escalation Prevention
- Customer license queries use `Auth::user()->licenses()` relationship
- This ensures Eloquent automatically filters by `user_id`
- Direct ID manipulation in URL (e.g., `/dashboard/licenses/999`) will return 404 if license doesn't belong to user
- `findOrFail()` method ensures proper error handling

### Vertical Privilege Escalation Prevention
- Middleware check at route level prevents URL manipulation
- Admin routes return 403 before controller is even instantiated
- No ability for customers to elevate privileges through form manipulation

### Data Exposure Prevention
- Navigation menu hides admin links from customers (defense in depth)
- API endpoints not affected (already had separate authentication)
- Profile editing prevents role changes

## Testing Scenarios

### Scenario 1: Customer Login
1. Customer logs in
2. Sees Dashboard with their licenses
3. Navigation shows: Dashboard, Lisanslarım
4. Can click on profile to edit information
5. Cannot see or access user management, logs, or admin license panel

### Scenario 2: Customer Attempts Admin Access
1. Customer types `/admin/users` in browser
2. Receives 403 Forbidden error
3. Same for `/admin/licenses` and `/admin/logs`

### Scenario 3: Admin Login
1. Admin logs in
2. Sees full dashboard
3. Navigation shows: Dashboard, Kullanıcılar, Lisanslar, Loglar
4. Can manage all users and licenses
5. Can view all logs

### Scenario 4: License Type Management
1. Admin creates new license
2. License type dropdown populated from database
3. Can add new types via database without code changes
4. Can disable types by setting `active = false`

## Migration Notes

### Running Migrations
```bash
php artisan migrate
```

This will create the `license_types` table and populate it with default types:
- demo - Demo lisans
- monthly - 1 ay geçerli
- yearly - 1 yıl geçerli
- lifetime - Süresiz lisans

### Existing Data
- No changes to existing licenses table structure
- Existing licenses will continue to work
- License types remain as enum in the database but are validated against license_types table

## Files Modified
1. `bootstrap/app.php` - Middleware registration
2. `routes/web.php` - Route protection and customer routes
3. `app/Http/Controllers/Admin/LicenseController.php` - License type from DB
4. `app/Http/Controllers/User/ProfileController.php` - Profile update
5. `resources/views/layouts/app.blade.php` - Conditional navigation
6. `resources/views/user/profile.blade.php` - Editable profile
7. `resources/views/admin/licenses/create.blade.php` - Dynamic license types
8. `resources/views/admin/licenses/edit.blade.php` - Dynamic license types

## Files Created
1. `app/Http/Middleware/EnsureUserIsAdmin.php`
2. `app/Models/LicenseType.php`
3. `app/Http/Controllers/User/LicenseController.php`
4. `database/migrations/2026_02_09_174500_create_license_types_table.php`
5. `resources/views/user/licenses/index.blade.php`
6. `resources/views/user/licenses/show.blade.php`

## Backward Compatibility
- Existing admin functionality unchanged
- API endpoints not affected
- Existing user accounts work as before
- Admin users maintain all privileges
