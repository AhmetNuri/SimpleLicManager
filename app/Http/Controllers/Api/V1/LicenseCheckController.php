<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseCheckController extends Controller
{
    /**
     * Check license validity.
     */
    public function check(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'serial_number' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            LicLog::error('License check validation failed: ' . json_encode($validator->errors()->toArray()));
            return response()->json([
                'valid' => false,
                'message' => 'Validation failed: ' . $validator->errors()->first(),
            ], 422);
        }

        $email = $request->input('email');
        $serialNumber = $request->input('serial_number');
        $deviceId = $request->input('device_id');

        // Find license
        $query = License::with('user')
            ->whereHas('user', function ($q) use ($email) {
                $q->where('email', $email);
            })
            ->where('serial_number', $serialNumber);

        $license = $query->first();

        // License not found
        if (!$license) {
            LicLog::error("License not found for email: {$email}, serial: {$serialNumber}");
            return response()->json([
                'valid' => false,
                'message' => 'Lisans bulunamadı.',
            ], 404);
        }

        // Check if user is enabled
        if (!$license->user_enable) {
            LicLog::error("User disabled for license ID: {$license->id}");
            return response()->json([
                'valid' => false,
                'message' => 'Kullanıcı devre dışı bırakılmış.',
            ], 403);
        }

        // Check device ID if provided
        if ($deviceId) {
            // If license has a device_id set, it must match
            if ($license->device_id && $license->device_id !== $deviceId) {
                LicLog::error("Device ID mismatch for license ID: {$license->id}. Expected: {$license->device_id}, Got: {$deviceId}");
                return response()->json([
                    'valid' => false,
                    'message' => 'Cihaz eşleşmedi. Bu lisans başka bir cihaza bağlı.',
                ], 403);
            }

            // If no device_id is set, bind it now
            if (!$license->device_id) {
                $license->device_id = $deviceId;
                $license->save();
                LicLog::info("Device bound to license ID: {$license->id}, Device: {$deviceId}", $license->id, $license->user_id);
            }
        }

        // Check expiration
        if ($license->expires_at && $license->expires_at->isPast()) {
            LicLog::error("License expired for ID: {$license->id}");
            return response()->json([
                'valid' => false,
                'message' => 'Lisans süresi dolmuş.',
            ], 403);
        }

        // Update last checked information
        $license->last_checked_date = now();
        if ($deviceId) {
            $license->last_checked_device_id = $deviceId;
        }
        $license->save();

        // Build response
        $response = [
            'valid' => true,
            'package' => $license->product_package,
            'type' => $license->license_type,
            'emergency' => $license->emergency,
        ];

        if ($license->expires_at) {
            $daysLeft = $license->getDaysLeft();
            $response['expires_at'] = $license->expires_at->format('Y-m-d');
            $response['days_left'] = $daysLeft;

            if ($license->isExpiringSoon()) {
                $response['warning'] = "Lisansınızın bitmesine {$daysLeft} gün kaldı!";
            }
        } else {
            $response['expires_at'] = null;
            $response['days_left'] = null;
        }

        // Log successful check
        LicLog::info("License check successful for ID: {$license->id}", $license->id, $license->user_id);

        return response()->json($response, 200);
    }
}
