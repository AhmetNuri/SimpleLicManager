<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenseController extends Controller
{
    /**
     * Display a listing of the user's licenses.
     */
    public function index()
    {
        $user = Auth::user();
        $licenses = $user->licenses()
            ->latest()
            ->paginate(15);

        return view('user.licenses.index', compact('licenses'));
    }

    /**
     * Display the specified license.
     */
    public function show($id)
    {
        $user = Auth::user();
        $license = $user->licenses()->findOrFail($id);

        $license->load(['logs' => function ($query) {
            $query->latest()->limit(20);
        }]);

        return view('user.licenses.show', compact('license'));
    }
}
