<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendeeController extends Controller
{
    use CanLoadRelationships;
    private array $relations = ["user"];

    public function __construct()
    {
        $this->middleware("auth:sanctum")->except(["index", "show"]);
        $this->middleware("throttle:api")->only(["store", "destroy"]);
        $this->authorizeResource(Attendee::class, "attendee");
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event): AnonymousResourceCollection
    {
        // ->attendees as a property directly fetches the attendees of the event
        // $attendees = $event->attendees
        // ->attendees() builds a query builder use get or paginate the get the result
        $attendees = $this->loadRelationships($event->attendees()->latest());
        return AttendeeResource::collection(
            $attendees->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event): AttendeeResource
    {
        // here we use the relation with event to create a new attendee and add a temporary user id inside of it
        $attendee = $event->attendees()->create([
            "user_id" => $request->user()->id,
        ]);
        return new AttendeeResource($this->loadRelationships($attendee));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee): AttendeeResource
    {
        return new AttendeeResource($this->loadRelationships($attendee));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        $attendee->delete();
        return response(status: 204);
    }
}
