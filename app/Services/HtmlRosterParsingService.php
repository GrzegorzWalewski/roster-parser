<?php

namespace App\Services;

use App\Services\RosterParsingServiceInterface;
use Masterminds\HTML5;

class HtmlRosterParsingService implements RosterParsingServiceInterface
{
    private const COLUMNS_TO_EXTRACT = [
        'lineLeft activitytablerow-date' => 'date',
        'activitytablerow-activity' => 'type',
        'activitytablerow-fromstn' => 'start_location',
        'activitytablerow-tostn' => 'end_location',
        'activitytablerow-stdutc' => 'start_time',
        'activitytablerow-stautc' => 'end_time'
    ];

    private const PERIOD_SELECT_ID = 'ctl00_Main_periodSelect';

    public function parseRoster($file): array
    {
        $html = new HTML5();
        $html = $html->load($file);
        $table = $html->getElementsByTagName('table')->item(0);

        if ($table == null) {
            return [];
        }

        $yearAndMonth = $this->getYearAndMonth($html);
        $extractedData = [];
        $dataRows = $table->getElementsByTagName('tr');

        for ($i = 1; $i < $dataRows->length; $i++) {
            $rowData = [];
            $dataRow = $dataRows->item($i);

            foreach ($dataRow->getElementsByTagName('td') as $td) {
                $key = $td->getAttribute('class');

                if (!array_key_exists($key, self::COLUMNS_TO_EXTRACT)) {
                    continue;
                }

                switch (self::COLUMNS_TO_EXTRACT[$key]) {
                    case 'type':
                        $rowData['type'] = $this->getType($td->textContent);

                        if ($rowData['type'] == static::FLIGHT_TYPE) {
                            $rowData['flight_number'] = $td->textContent;
                        } else {
                            $rowData['flight_number'] = null;
                        }

                        break;
                    case 'date':
                        if ($td->textContent != null) {
                            $day = $this->getDay($td->textContent);
                        }

                        break;
                    case 'start_time':
                    case 'end_time':
                        $rowData[self::COLUMNS_TO_EXTRACT[$key]] = $yearAndMonth . '-' . $day . ' ' . $this->formatTime($td->textContent);
                        break;
                    default:
                        $rowData[self::COLUMNS_TO_EXTRACT[$key]] = $td->textContent;
                }
            }

            $extractedData[] = $rowData;
        }

        return $extractedData;
    }

    private function getType($type)
    {
        if (preg_match(static::FLIGHT_PATTERN, $type)) {
            return static::FLIGHT_TYPE;
        } elseif (in_array($type, static::ALLOWED_ACTIVITY_TYPES)) {
            return $type;
        } else {
            return static::UNKNOWN_TYPE;
        }
    }

    private function getYearAndMonth($html): string
    {
        $periodSelect = $html->getElementById(self::PERIOD_SELECT_ID);

        foreach ($periodSelect->getElementsByTagName('option') as $option) {
            if ($option->hasAttribute('selected')) {
                $selectedValue = $option->getAttribute('value');
                break;
            }
        }

        return substr($selectedValue, 0, 7);
    }

    private function getDay($dayString): int
    {
        return (int)substr($dayString, -2);
    }

    private function formatTime($timeString): string
    {
        return substr($timeString, 0, 2) . ':' . substr($timeString, 2, 2);
    }
}