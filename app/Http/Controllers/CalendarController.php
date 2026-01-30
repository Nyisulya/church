<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events()
    {
        $events = Event::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->date->format('Y-m-d H:i:s'),
                'end' => $event->end_date ? $event->end_date->format('Y-m-d H:i:s') : null,
                'description' => $event->description,
                'location' => $event->location,
                'url' => route('events.show', $event->id),
                'backgroundColor' => $this->getColorForEvent($event->type ?? 'default'),
                'borderColor' => $this->getColorForEvent($event->type ?? 'default'),
            ];
        });

        return response()->json($events);
    }

    private function getColorForEvent($type)
    {
        $colors = [
            'service' => '#007bff', // Blue
            'meeting' => '#28a745', // Green
            'special' => '#dc3545', // Red
            'youth' => '#ffc107',   // Yellow
            'default' => '#6c757d', // Grey
        ];

        return $colors[$type] ?? $colors['default'];
    }
}
