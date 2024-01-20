<?php
require_once(__DIR__.'/../utils/helpers.php');

class Time
{
    // daysSinceResignation() {
    public static function daysSince($dateTimeInput)
    {
        $localDateTimeNow = Time::local();
        $lastDayAtWork = new DateTime($dateTimeInput);
        // The time here needs to match the time of whenever this script is run
        // otherwise you might end up with the wrong date
        $lastDayAtWork->setTime($localDateTimeNow->format('H'), $localDateTimeNow->format('i'));

        return $lastDayAtWork->diff($localDateTimeNow)->days;
    }

    // local :: void -> DateTime
    // DateTime = new DateTime object
    public static function local($time = 'now', $timezone = 'America/Los_Angeles')
    {
        return new DateTime($time, new DateTimeZone($timezone));
    }

    // format :: (DateTime, string) -> DateTime
    // DateTime = DateTime object
    public static function format($dateTime, $format)
    {
        return $dateTime->format($format);
    }

    // calculateTotalHours :: (string, string, int) -> int
    // *note: first two param strings need to be valid strtotime args
    // TODO: Revise so that Total Hours can be calculated without a clockedOutTime
    public static function calculateTotalHours($arrivalTime, $clockedOutTime, $offHours)
    {
        if ($missingMeridiem = preg_replace('/[a-zA-Z]+/', '', $arrivalTime) == $arrivalTime) {
            echo ('ERROR: Arrival time is missing meridiem');
            return;
            // echo("\nAdding missing meridiem to arrival time (AM): ");
            // // echo("\n".$clockedOutTime);
            // // echo("\n".$clockedOutTimeMeridiem);
            // echo($arrivalTime." -> ");
            // $arrivalTime = $arrivalTime.'AM';
            // echo($arrivalTime);
        }

        $arrivalToClockedOut = self::timeDiffInHours($arrivalTime, $clockedOutTime);

        $totalHours = round($arrivalToClockedOut, 2) - $offHours;

        echo ("\nArrival Time: " . $arrivalTime);
        echo ("\nClocked Out Time: " . $clockedOutTime);
        echo ("\nArrival to Clocked Out Hours: " . round($arrivalToClockedOut, 2));
        echo ("\nLunch and Break Hours: " . convertDecimalTo($offHours) . ' (to subtract)');
        echo ("\nTotal Hours: " . convertDecimalTo($totalHours) . "\n");

        return convertDecimalTo($totalHours);
    }

    // TODO: Combine this with extractTimeRange
    // extractTimeFrom :: string -> string
    // *only first match found is returned
    public static function extractTimeFrom($string)
    {
        // TODO: Is there a better way to write this?
        preg_match('/\d{1,2}:\d{2}[AP]*M*/i', $string, $arr);
        return $arr[0];
    }

    // extractTimeRange :: (range, tag?) -> tuple
    // tuple = [string, string]
    // range = string
    // tag = string
    public static function extractTimeRange($range, $tag = null)
    {
        if (strpos($range, '-') !== FALSE && strlen(preg_replace('/\D/', '', $range)) >= 6) {
            preg_match_all('/\d{1,2}:\d{2}[AP]*M*/i', $range, $arr);
            return $arr[0];
        }
        echo ("\nWARNING: Could not extract time range for tag: \"" . ($tag ?? 'N/A/') . "\"");
        return null;
    }

    // timeDiffInHours :: (start, end) -> hours 
    // start, end = string (valid strtotime input)
    // hours = int
    public static function timeDiffInHours($start, $end)
    {
        return (strtotime($end) - strtotime($start)) / (60 * 60);
    }

    // extractHours :: (range, tag) -> string
    // range = /\[\d{1,2}:\d{2}[A&M|P&M]{2}-\d{1,2}:\d{2}[A&M|P&M]{2}\]/
    // tag = string
    public static function extractHours($string, $tag = null)
    {
        // echo("\nextractHours inputs: \n".$string);
        if (is_null($string)) {
            return null;
        }

        $timesArr = self::extractTimeRange($string, $tag);
        // print_r("\nextractHours timeArr: \n");
        // print_r($timesArr);

        // ex. ['11:30AM', '12:30PM']
        if (!empty($timesArr)) {
            if ($missingMeridiem = preg_replace('/[a-zA-Z]+/', '', $timesArr[0]) == $timesArr[0]) {
                // echo("\nMeridiem added: ".$timesArr[0]." -> ");
                $timesArr[0] = $timesArr[0] . preg_replace('/[:\d]+/', '', $timesArr[1]);
                // echo($timesArr[0]);
            }
            return self::timeDiffInHours($timesArr[0], $timesArr[1]);
        }

        return null;
    }
}
