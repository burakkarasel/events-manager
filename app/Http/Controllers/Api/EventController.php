<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use \App\Models\Event;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection | AnonymousResourceCollection
    {
        return EventResource::collection(Event::latest()->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Event | EventResource
    {
        $data = $request->validate([
            "name" => "string | required | max:255",
            "description" => "string | nullable",
            "start_time" => "required | date",
            "end_time" => "required | date | after:start_time",
        ]);

        return new EventResource(Event::create([
            ...$data,
            "user_id" => "9ac8e20d-da06-457d-b6d5-69a28b9b6773"
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): Event | EventResource
    {
        return new EventResource($event->load("user", "attendees"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): EventResource
    {
        $event->update([
            ...$request->validate([
                "name" => "string | max:255 | sometimes",
                "description" => "string | nullable",
                "start_time" => "date | sometimes",
                "end_time" => "date | after:start_time | sometimes",
            ])
        ]);
        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response(status: 204);
    }
}
