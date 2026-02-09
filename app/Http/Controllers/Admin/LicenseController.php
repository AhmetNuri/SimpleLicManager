<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use App\Models\LicenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $licenses = License::with('user')
            ->when($search, function ($query, $search) {
                $query->where('serial_number', 'like', "%{$search}%")
                    ->orWhere('product_package', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('email', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15);

        return view('admin.licenses.index', compact('licenses', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('email')->get();
        $licenseTypes = LicenseType::getActiveTypes();
        return view('admin.licenses.create', compact('users', 'licenseTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $licenseTypeCodes = LicenseType::where('active', true)->pluck('code')->toArray();
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'serial_number' => 'nullable|string|max:64|unique:licenses,serial_number',
            'product_package' => 'required|string|max:255',
            'license_type' => 'required|in:' . implode(',', $licenseTypeCodes),
            'starts_at' => 'required|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'device_id' => 'nullable|string|max:255',
            'user_enable' => 'boolean',
            'emergency' => 'boolean',
            'max_connection_count' => 'required|integer|min:1',
        ]);

        // Generate serial number if not provided
        if (empty($validated['serial_number'])) {
            $validated['serial_number'] = $this->generateSerialNumber();
        }

        License::create($validated);

        return redirect()->route('admin.licenses.index')
            ->with('success', 'Lisans başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(License $license)
    {
        $license->load(['user', 'logs' => function ($query) {
            $query->latest()->limit(50);
        }]);

        return view('admin.licenses.show', compact('license'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(License $license)
    {
        $users = User::orderBy('email')->get();
        $licenseTypes = LicenseType::getActiveTypes();
        return view('admin.licenses.edit', compact('license', 'users', 'licenseTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, License $license)
    {
        $licenseTypeCodes = LicenseType::where('active', true)->pluck('code')->toArray();
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'serial_number' => 'required|string|max:64|unique:licenses,serial_number,' . $license->id,
            'product_package' => 'required|string|max:255',
            'license_type' => 'required|in:' . implode(',', $licenseTypeCodes),
            'starts_at' => 'required|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'device_id' => 'nullable|string|max:255',
            'user_enable' => 'boolean',
            'emergency' => 'boolean',
            'max_connection_count' => 'required|integer|min:1',
        ]);

        $license->update($validated);

        return redirect()->route('admin.licenses.index')
            ->with('success', 'Lisans başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(License $license)
    {
        $license->delete();

        return redirect()->route('admin.licenses.index')
            ->with('success', 'Lisans başarıyla silindi.');
    }

    /**
     * Generate a unique serial number.
     */
    private function generateSerialNumber(): string
    {
        do {
            $serial = strtoupper(Str::random(8) . '-' . Str::random(8) . '-' . Str::random(8) . '-' . Str::random(8));
        } while (License::where('serial_number', $serial)->exists());

        return $serial;
    }
}
