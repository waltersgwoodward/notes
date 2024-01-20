<?php
require_once(__DIR__ . '/timeSheet.php');
require_once(__DIR__ . '/../utils/helpers.php');
require_once(__DIR__ . '/time.php');

class Hours
{
    // archive :: void -> void
    // Description: Saves total hours and other info from the current timesheet's header in today.md a file in `calculated-hours/`
    public static function archive()
    {
        $calculatedHours = self::getFile();
        $headerContent = getStringBetween(file_get_contents(TimeSheet::getFile()), '## TIMESHEET:', '### Total Hours:');

        file_put_contents($calculatedHours, "\n\n" . $headerContent, FILE_APPEND | LOCK_EX);
    }

    // getFile :: void -> string
    public static function getFile()
    {
        $tz = 'America/Los_Angeles';
        $localDateTimeNow = new DateTime("now", new DateTimeZone($tz));
        $date = $localDateTimeNow->format('Y-m');
        return __DIR__ . '/../../hours/hours__' . $date . '.md';
    }

    // public static function calculateWeekTotal() {
    public static function getTotalHoursForPast($numberOfDays = null)
    {
        $file = TimeSheet::getFileName();
        $timeSheetTag = "## TIMESHEET:";
        $totalHoursTag = "### Total Hours:";



        $dayTonumberOfDaysMap = [
            'Mon' => 5,
            'Tue' => 1,
            'Wed' => 2,
            'Thu' => 3,
            'Fri' => 4,
            'Sat' => 5,
            'Sun' => 5
        ];

        $dayDict = [
            'Mon' => 'Monday',
            'Tue' => 'Tuesday',
            'Wed' => 'Wednesday',
            'Thu' => 'Thursday',
            'Fri' => 'Friday',
            'Sat' => 'Saturday',
            'Sun' => 'Sunday'
        ];

        echo ("Day Today: ");
        $dayToday = Time::format(Time::local(), 'D');
        echo ($dayDict[$dayToday] . "\n");

        echo ("Number of Week Days past: ");
        $numberOfDays = $numberOfDays ?? $dayTonumberOfDaysMap[$dayToday];
        echo ($numberOfDays . "\n" . "\n");

        $timeSheetHeadersArray = TimeSheet::getLines($file, $timeSheetTag);
        $totalHoursStringsArray = TimeSheet::getLines($file, $totalHoursTag);

        $headerToHoursObject = [];
        foreach ($timeSheetHeadersArray as $index => $timeSheetHeader) {
            $headerToHoursObject[$timeSheetHeader] = $totalHoursStringsArray[$index];
        }


        $totalHoursArraySlice = array_slice($headerToHoursObject, count($headerToHoursObject) - $numberOfDays);
        // print("<pre>" . print_r($totalHoursArraySlice, true) . "</pre>");


        $totalHours = [];
        // $daysAndHours = [];
        if ($totalHoursArraySlice) {
            foreach ($totalHoursArraySlice as $fullString => $totalHoursString) {
                $hours = TimeSheet::extractHoursFrom($totalHoursString);
                array_push($totalHours, $hours);

                $day = TimeSheet::extractDayFrom($fullString);
                // $daysAndHours[$day] = $hours;
                echo ("$day: $hours\n");
            }
        }
        // print("<pre>" . print_r($totalHours, true) . "</pre>");
        // print("<pre>" . print_r($daysAndHours, true) . "</pre>");


        $hoursTotal = 0;
        $minsTotal = 0;
        foreach ($totalHours as $index => $hours) {
            if ($hours) {
                $hoursAndMinutes = explode(" ", $hours);
                if (count($hoursAndMinutes) === 4) {
                    $hrs = $hoursAndMinutes[0];;
                    $hoursTotal += (int)preg_replace("/[^0-9]/", "", $hrs);





                    $mins = $hoursAndMinutes[2];
                    $minsTotal += (int)preg_replace("/[^0-9]/", "", $mins);;
                }
            }
        }

        $hoursFromMins = floor($minsTotal / 60);
        $hoursTtl = $hoursTotal + $hoursFromMins;
        $minsTtl = $minsTotal - ($hoursFromMins * 60);

        $output = $hoursTtl . " hours and " . $minsTtl . " minutes!\n\n";
        echo ("\nTotal Hours for last $numberOfDays days: ");
        echo ($output);
        if ($numberOfDays) {
            echo ("Target Hours for " . $numberOfDays . " days: " . ($numberOfDays * 8) . "\n");
        }
    }
}
