<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_endpoint_without_filters()
    {
        $response = $this->get('/api/events');
        $response->assertStatus(200);
    }

    public function test_get_endpoint_with_filters()
    {
        $response = $this->get('/api/events?exact_date=2022-01-01&end_location=KRP&start_location=EBJ&type=FLT');
        $response->assertStatus(200);
    }

    public function test_get_endpoint_with_prohibited_filters()
    {
        $response = $this->get('/api/events?exact_date=2022-01-01&from_date=2022-01-01');
        $this->assertFalse($response->json()['success']);
    }

    public function test_upload_roster_and_adding_events()
    {
        $response = $this->post('/api/roster/upload', [
            'file' => new \Illuminate\Http\UploadedFile(__DIR__ . '/../htmlRosterTestFiles/valid_roster_file.html', 'valid_roster_file.html', 'text/html', null, true)
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('events', [
            "type" => "FLT",
            "flight_number" => "DX77",
            "start_location" => "KRP",
            "start_time" => "2022-01-0 08:45",
            "end_location" => "CPH",
            "end_time" => "2022-01-0 09:35"
        ]);
    }

    public function test_upload_roster_bad_filetype()
    {
        $response = $this->post('/api/roster/upload', [
            'file' => new \Illuminate\Http\UploadedFile(__DIR__ . '/../htmlRosterTestFiles/not_html.png', 'not_html.png', 'text/html', null, true)
        ]);

        $response->assertStatus(500);
    }
}
