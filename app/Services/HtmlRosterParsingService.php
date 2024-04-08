<?php

namespace App\Services;

use App\Services\RosterParsingServiceInterface;
use Masterminds\HTML5;

class HtmlRosterParsingService implements RosterParsingServiceInterface
{
    public const COLUMNS_TO_EXTRACT = [
        'activitytablerow-activity' => 'type',
        'activitytablerow-fromstn' => 'start_location',
        'activitytablerow-tostn' => 'end_location',
        'activitytablerow-stdutc' => 'start_time',
        'activitytablerow-stautc' => 'end_time'
    ];

    public function parseRoster($file): array
    {
        $html = new HTML5();
        $html = $html->load($file);
        $table = $html->getElementsByTagName('table')->item(0);

        $parsedData = [];
        $dataRows = $table->getElementsByTagName('tr');

        for ($i = 1; $i < $dataRows->length; $i++) {
            $rowData = [];
            $dataRow = $dataRows->item($i);

            foreach ($dataRow->getElementsByTagName('td') as $td) {
                $key = $td->getAttribute('class');

                if (!array_key_exists($key, self::COLUMNS_TO_EXTRACT)) {
                    continue;
                }

                if (self::COLUMNS_TO_EXTRACT[$key] == 'type')
                {
                    $rowData['type'] = $this->getType($td->textContent);

                    if ($rowData['type'] == 'FLT') {
                        $rowData['flight_number'] = $td->textContent;
                    } else {
                        $rowData['flight_number'] = null;
                    }
                } else {
                    $rowData[self::COLUMNS_TO_EXTRACT[$key]] = $td->textContent;
                }
            }

            $extractedData[] = $rowData;
        }

        return $extractedData;
    }

    private function getType($type)
    {
        if (preg_match(self::FLIGHT_PATTERN, $type)) {
            return 'FLT';
        } elseif (in_array($type, self::ALLOWED_ACTIVITY_TYPES)) {
            return $type;
        } else {
            return 'UNK';
        }
    }
}