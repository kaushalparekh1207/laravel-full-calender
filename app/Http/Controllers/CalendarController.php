<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class CalendarController extends Controller
{
    public function index()
    {
        $events = array();
        $bookings = Event::all();
        foreach ($bookings as $booking){
            $events[] = [
                'id' => $booking->id,
                'title' => $booking->title,
                'start' => $booking->start_date,
                'end' => $booking->end_date,
                'color' => $booking->color,
            ];
        }
        return view('calendar.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string'
        ]);

        $start_date = date('Y-m-d H:i:s',strtotime($request->start_date));
        $end_date = date('Y-m-d 20:00:00',strtotime($request->end_date));

        $booking = Event::create([
            'title' => $request->title,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'color' => $request->color,
        ]);

        return response()->json([
            'id' => $booking->id,
            'start' => $booking->start_date,
            'end' => $booking->end_date,
            'title' => $booking->title,
            'color' => $booking->color,

        ]);
    }

    public function update($id, Request $request)
    {
        $bookings = Event::find($id);
        if(! $bookings) {
            return response()->json([
                'error' => 'Unable to locate the event'
            ], 404);
        }
        $bookings->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        return response()->json('Event updated');
    }

    public function delete($id)
    {
        $booking = Event::find($id);
        if(! $booking) {
            return response()->json([
                'error' => 'Unable to locate the event'
            ], 404);
        }
        $booking->delete();
        // return response()->json('Event Deleted');
        return $id;
    }

}
