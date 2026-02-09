<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $licenses = $user->licenses()
            ->latest()
            ->get()
            ->map(function ($license) {
                return [
                    'id' => $license->id,
                    'serial_number' => $license->serial_number,
                    'product_package' => $license->product_package,
                    'license_type' => $license->license_type,
                    'starts_at' => $license->starts_at,
                    'expires_at' => $license->expires_at,
                    'days_left' => $license->getDaysLeft(),
                    'is_valid' => $license->isValid(),
                    'is_expiring_soon' => $license->isExpiringSoon(),
                    'user_enable' => $license->user_enable,
                ];
            });

        return view('user.dashboard', compact('licenses'));
    }
}
