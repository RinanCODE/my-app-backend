<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $events = Event::where('user_id', $user->id)
            ->withCount(['participants', 'certificates'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($events);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'template_id' => 'nullable|exists:certificate_templates,id',
        ]);

        $user = $request->user();
        $validated['user_id'] = $user->id;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoPath = $photo->store('events/photos', 'public');
            $validated['photo_path'] = $photoPath;
        }

        unset($validated['photo']); // Remove photo from validated array as it's not a database column
        $event = Event::create($validated);

        return response()->json($event, 201);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load(['participants', 'certificates.participant']);

        return response()->json($event);
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'sometimes|required|date',
            'location' => 'nullable|string|max:255',
            'template_id' => 'nullable|exists:certificate_templates,id',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Request $request, Event $event)
    {
        $user = $request->user();
        
        // Only allow the event creator to delete the event
        if ($event->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized. You can only delete events you created.'
            ], 403);
        }

        // Delete event photo if exists
        if ($event->photo_path && Storage::exists('public/' . $event->photo_path)) {
            Storage::delete('public/' . $event->photo_path);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}

