<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    /**
     * Display a listing of participants for an event.
     */
    public function index(Request $request, Event $event)
    {
        $participants = $event->participants()->get();

        return response()->json($participants);
    }

    /**
     * Store a newly created participant.
     */
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:11|regex:/^[0-9]+$/',
            'additional_info' => 'nullable|string',
        ], [
            'email.required' => 'Email is required.',
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 11 digits.',
            'phone.regex' => 'Phone number must contain only numbers.',
        ]);

        $participant = $event->participants()->create($validated);

        return response()->json($participant, 201);
    }

    /**
     * Upload and import participants from CSV.
     */
    public function uploadCsv(Request $request, Event $event)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $realPath = $file->getRealPath();

        // Detect delimiter (comma, semicolon, or tab) based on header line
        $firstBytes = file_get_contents($realPath, false, null, 0, 2048) ?: '';
        // Remove UTF-8 BOM if present
        if (str_starts_with($firstBytes, "\xEF\xBB\xBF")) {
            $firstBytes = substr($firstBytes, 3);
        }
        $firstLine = strtok($firstBytes, "\r\n");
        $cComma = substr_count($firstLine, ',');
        $cSemi  = substr_count($firstLine, ';');
        $cTab   = substr_count($firstLine, "\t");
        $delimiter = ',';
        if ($cSemi > $cComma && $cSemi >= $cTab) { $delimiter = ';'; }
        if ($cTab > $cComma && $cTab > $cSemi) { $delimiter = "\t"; }

        $csv = Reader::createFromPath($realPath, 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($records as $index => $record) {
                $rowNumber = $index + 2; // +2 because header is row 1, and index starts at 0

                // Normalize column names (case-insensitive, handle spaces)
                $normalized = [];
                foreach ($record as $key => $value) {
                    $normalized[strtolower(trim($key))] = trim($value);
                }

                // Map common CSV column names
                $name = $normalized['name'] ?? $normalized['participant name'] ?? $normalized['full name'] ?? null;
                $email = $normalized['email'] ?? $normalized['email address'] ?? null;
                $phone = $normalized['phone'] ?? $normalized['phone number'] ?? $normalized['mobile'] ?? null;
                $additionalInfo = $normalized['additional info'] ?? $normalized['notes'] ?? $normalized['remarks'] ?? null;

                if (empty($name)) {
                    $errors[] = "Row {$rowNumber}: Name is required";
                    continue;
                }

                if (empty($email)) {
                    $errors[] = "Row {$rowNumber}: Email is required";
                    continue;
                }

                if (empty($phone)) {
                    $errors[] = "Row {$rowNumber}: Phone number is required";
                    continue;
                }

                // Validate phone number format (only numbers, at least 11 digits)
                $phone = preg_replace('/[^0-9]/', '', $phone);
                if (strlen($phone) < 11) {
                    $errors[] = "Row {$rowNumber}: Phone number must be at least 11 digits";
                    continue;
                }

                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNumber}: Invalid email format";
                    continue;
                }

                $event->participants()->create([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'additional_info' => $additionalInfo ?: null,
                ]);

                $imported++;
            }

            DB::commit();

            return response()->json([
                'message' => 'CSV imported successfully',
                'imported' => $imported,
                'errors' => $errors,
                'delimiter' => $delimiter,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error importing CSV',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified participant.
     */
    public function show(Participant $participant)
    {
        $participant->load(['event', 'certificate']);

        return response()->json($participant);
    }

    /**
     * Update the specified participant.
     */
    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:11|regex:/^[0-9]+$/',
            'additional_info' => 'nullable|string',
        ], [
            'email.required' => 'Email is required.',
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 11 digits.',
            'phone.regex' => 'Phone number must contain only numbers.',
        ]);

        $participant->update($validated);

        return response()->json($participant);
    }

    /**
     * Remove the specified participant.
     */
    public function destroy(Participant $participant)
    {
        // Delete associated certificate if exists (cascade should handle this, but we'll ensure it)
        if ($participant->certificate) {
            $certificate = $participant->certificate;
            // Delete files
            if ($certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
                Storage::delete($certificate->pdf_path);
            }
            if ($certificate->qr_code_path && Storage::exists($certificate->qr_code_path)) {
                Storage::delete($certificate->qr_code_path);
            }
            $certificate->delete();
        }
        
        $participant->delete();

        return response()->json(['message' => 'Participant and associated certificate deleted successfully']);
    }

    /**
     * Bulk delete participants.
     */
    public function bulkDestroy(Request $request, Event $event)
    {
        $request->validate([
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'exists:participants,id',
        ]);

        $participantIds = $request->input('participant_ids');
        $participants = $event->participants()->whereIn('id', $participantIds)->get();

        foreach ($participants as $participant) {
            if ($participant->certificate) {
                $certificate = $participant->certificate;
                if ($certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
                    Storage::delete($certificate->pdf_path);
                }
                if ($certificate->qr_code_path && Storage::exists($certificate->qr_code_path)) {
                    Storage::delete($certificate->qr_code_path);
                }
                $certificate->delete();
            }
            $participant->delete();
        }

        return response()->json([
            'message' => 'Participants and associated certificates deleted successfully',
            'deleted_count' => count($participantIds),
        ]);
    }
}

