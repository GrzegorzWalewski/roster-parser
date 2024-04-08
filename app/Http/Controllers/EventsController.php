<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RosterParsingServiceInterface;
use App\Models\Events;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
    public function uploadRoster(Request $request, RosterParsingServiceInterface $rosterParsingService)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,excel,txt,html,ical|max:2048',
        ]);

        try {
            $parsedData = $rosterParsingService->parseRoster($request->file('file'));
        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        Events::insert($parsedData);

        return json_encode(['success' => true]);
    }

    public function getEvents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'string|size:3',
            'start_location' => 'string|size:3',
            'end_location' => 'string|size:3',
            'from_date' => 'date_format:Y-m-d|prohibits:exact_date',
            'to_date' => 'date_format:Y-m-d|prohibits:exact_date',
            'exact_date' => 'date_format:Y-m-d|prohibits:from_date,to_date',
        ]);

        if ($validator->fails()) {
            return json_encode(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $validated = $validator->validated();
        $query = Events::query();
        
        if(isset($validated['from_date']) && isset($validated['to_date'])) {
            $query->whereBetween('start_time', [$validated['from_date'], $validated['to_date']]);
        } elseif(isset($validated['exact_date'])) {
            $query->whereDate('start_time', '=', $validated['exact_date']);
        }
        
        foreach($validated as $key => $value) {
            if($key !== 'from_date' && $key !== 'to_date' && $key !== 'exact_date') {
                $query->where($key, $value);
            }
        }

        return $query->get();
    }
}
