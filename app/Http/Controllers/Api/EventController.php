<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Http\Request;
use \App\Models\Event;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    use CanLoadRelationships;

    public function __construct()
    {
        $this->middleware("auth:sanctum")->except(["index", "show"]);
        $this->middleware("throttle:api")->only(["store", "destroy", "update"]);
        $this->authorizeResource(Event::class, "event");
    }

    private array $relations = ["user", "attendees", "attendees.user"];
    /**
     * Display a listing of the resource.
     */
    public function index():  AnonymousResourceCollection
    {
        $query = $this->loadRelationships(Event::query());
        return EventResource::collection($query->latest()->paginate(10));
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

        $event = Event::create([
            ...$data,
            "user_id" => $request->user()->id,
        ]);

        return new EventResource(
            $this->loadRelationships($event)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): Event | EventResource
    {
        return new EventResource($this->loadRelationships($event));
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
        return new EventResource($this->loadRelationships($event));
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
