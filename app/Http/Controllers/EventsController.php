<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RosterParsingServiceInterface;
use App\Models\Events;

class EventsController extends Controller
{
    public function uploadRoster(Request $request, RosterParsingServiceInterface $rosterParsingService)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,excel,txt,html,ical|max:2048',
        ]);

        $parsedData = $rosterParsingService->parseRoster($request->file('file'));

        Events::insert($parsedData);
    }

    public function getEvents()
    {

    }
}
