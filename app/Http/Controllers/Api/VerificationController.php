<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Verify a certificate by certificate ID.
     */
    public function verify(string $certificateId)
    {
        $certificate = Certificate::where('certificate_id', $certificateId)
            ->with(['event', 'participant'])
            ->first();

        if (!$certificate) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate not found',
            ], 404);
        }

        if (!$certificate->is_valid) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate has been revoked',
                'certificate' => [
                    'certificate_id' => $certificate->certificate_id,
                    'participant_name' => $certificate->participant->name,
                    'event_name' => $certificate->event->name,
                    'issued_date' => $certificate->issued_date->format('Y-m-d'),
                ],
            ], 200);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Certificate is valid',
            'certificate' => [
                'certificate_id' => $certificate->certificate_id,
                'participant_name' => $certificate->participant->name,
                'event_name' => $certificate->event->name,
                'event_date' => $certificate->event->event_date->format('Y-m-d'),
                'issued_date' => $certificate->issued_date->format('Y-m-d'),
                'location' => $certificate->event->location,
            ],
        ]);
    }
}

