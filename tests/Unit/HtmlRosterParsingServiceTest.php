<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\HtmlRosterParsingService;

class HtmlRosterParsingServiceTest extends TestCase
{
    public function testParseRosterValidFile()
    {
        $rosterParser = new HtmlRosterParsingService();
        $expectedData = [
            0 => [
                "type" => "FLT",
                "flight_number" => "DX77",
                "start_location" => "KRP",
                "start_time" => "2022-01-0 08:45",
                "end_location" => "CPH",
                "end_time" => "2022-01-0 09:35"
            ]
        ];
        $actualData = $rosterParser->parseRoster(__DIR__ . '/htmlRosterTestFiles/valid_roster_file.html');
        $this->assertEquals($expectedData, $actualData);
    }

    public function testParseRosterEmptyFile()
    {
        $rosterParser = new HtmlRosterParsingService();
        $expectedData = [];
        $actualData = $rosterParser->parseRoster(__DIR__ . '/htmlRosterTestFiles/empty_roster_file.html');
        $this->assertEquals($expectedData, $actualData);
    }
}
