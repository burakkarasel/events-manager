<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use \App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return Event::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Event
    {
        $data = $request->validate([
            "name" => "string | required | max:255",
            "description" => "string | nullable",
            "start_time" => "required | date",
            "end_time" => "required | date | after:start_time",
        ]);

        return Event::create([
            ...$data,
            "user_id" => "9ac8e20d-da06-457d-b6d5-69a28b9b6773"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): Event
    {
        return $event;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
