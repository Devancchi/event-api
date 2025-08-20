<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // GET /events → list event dengan pagination, filter, sorting
    public function index(Request $request)
    {
        $query = Event::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by title
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Sorting (default: ascending)
        if ($request->has('sort')) {
            $sort = $request->sort == 'desc' ? 'desc' : 'asc';
            $query->orderBy('start_datetime', $sort);
        }

        $events = $query->paginate(20);

        return response()->json($events);
    }

    // GET /events/{id}
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    // POST /events
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'status' => 'required|in:draft,published',
        ]);

        $user = Auth::user();

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'venue' => $request->venue,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'status' => $request->status,
            'organizer_id' => $user->id,
        ]);

        return response()->json(['message' => 'Event created', 'event' => $event], 201);
    }

    // PUT /events/{id}
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $user = Auth::user();

        // Organizer hanya boleh update event miliknya
        if ($user->role !== 'admin' && $event->organizer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'sometimes|string|max:255',
            'start_datetime' => 'sometimes|date',
            'end_datetime' => 'sometimes|date|after:start_datetime',
            'status' => 'sometimes|in:draft,published',
        ]);

        $event->update($request->all());

        return response()->json(['message' => 'Event updated', 'event' => $event]);
    }

    // DELETE /events/{id}
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $user = Auth::user();

        // Organizer hanya boleh delete event miliknya
        if ($user->role !== 'admin' && $event->organizer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }
}
