<?php

require_once(__DIR__ . '/hours.php');
require_once(__DIR__ . '/time.php');
require_once(__DIR__ . '/../utils/helpers.php');

class TimeSheet
{
    public static function archive()
    {
        $timeSheetFileName = self::getFileName();
        $contentToArchive = self::getMatchAndBelow('## TIMESHEET');

        // TODO: Confirm that the Total Hours is listed and if not run `make daily` logic first

        echo ("\nAppended old timesheet");
        $archiveUpdated = file_put_contents($timeSheetFileName, "\n\n" . $contentToArchive, FILE_APPEND | LOCK_EX);
        if ($archiveUpdated !== false) {
            self::removeOld(self::getFile(), '## TIMESHEET');
        } else {
            echo ("ERROR: ");
        }
    }

    public static function reset()
    {
        echo ("\nOld timesheet removed without saving");
        self::removeOld(self::getFile(), '## TIMESHEET');
    }

    // removeOld :: (string, string) -> string
    public static function removeOld($file, $pattern, $noLog = false)
    {
        $command = "sed '/$pattern/q' $file | sed '\$d'";
        exec($command, $output);
        file_put_contents(self::getFile(), implode("\n", $output));

        if (!$noLog) {
            echo ("\nOld timesheet removed from today.md");
        }
        self::appendBlank(self::getFile());
        return implode("\n", $output);
    }

    // appendBlank :: string -> void
    public static function appendBlank($file)
    {
        // $addBlank = readline("\nAdd blank timesheet? (yes|no)");
        // if ($addBlank == 'yes') {
        $template = self::getTemplate();
        file_put_contents($file, $template, FILE_APPEND | LOCK_EX);
        // }
    }

    // getTemplate :: void -> string
    public static function getTemplate()
    {
        $dateToday = Time::format(Time::local(), 'l, F jS, Y');
        $timeToday = Time::format(Time::local(), 'g:iA');

        return "\n## TIMESHEET: " . $dateToday .
            "\n### Arrived: " . $timeToday .
            "\n### Break Hours: ---" .
            "\n### Clocked Out: ---" .
            "\n### Total Hours: ---" .
            "\n* [" . $timeToday . "] Arrived" .
            "\n* [9:30AM-10:00AM] Updates" .
            "\n  * **Status Update** " .
            "\n    * " .
            "\n  * **TODO Today** " .
            "\n    * " .
            "\n  * **Additional Updates** " .
            "\n    * " .
            "\n* ";
    }

    // getMatchAndBelow :: string -> string
    public static function getMatchAndBelow($pattern)
    {
        $command = "sed -n '/" . $pattern . "/,\$p' today.md";
        // printVar($command, 'command');
        // echo("\ncommand: ".$command);
        exec($command, $output);
        return implode("\n", $output);
    }

    // getFile :: void -> string
    public static function getFile()
    {
        return __DIR__ . '/../../today.md';
    }

    // getFileName :: void -> string
    public static function getFileName($customDate = null)
    {
        if ($customDate) {
            $fileName = __DIR__ . '/../../time-sheets/time-sheets__' . $customDate . '.md';
            return $fileName;
        }

        // Previous: This grabs the current date
        // $localDateTimeNow = new DateTime("now", new DateTimeZone('America/Los_Angeles'));
        // $date = $localDateTimeNow->format('Y-m');

        // Current: Use the date currently used in the timesheet in today.md
        $file = TimeSheet::getFile(); // e.g. today.md
        $timeSheetLine = TimeSheet::getLine($file, '## TIMESHEET:');

        if (is_null($timeSheetLine)) {
            $message = "\nUnable to get date from timesheet for file name - make sure you run 'make daily' before you run 'make archive'\n";
            throw new ErrorException($message);
        }

        $humanReadableDate = substr($timeSheetLine, strpos($timeSheetLine, ":") + 1);

        $localDateTimeNow = new DateTime($humanReadableDate, new DateTimeZone('America/Los_Angeles'));
        $date = $localDateTimeNow->format('Y-m');

        echo("\n\$date: ".$date."\n\n");
        $fileName = __DIR__ . '/../../time-sheets/time-sheets__' . $date . '.md';
        // echo ("\nUsing " . $fileName . "\n");
        return __DIR__ . '/../../time-sheets/time-sheets__' . $date . '.md';
    }

    // getLine :: (string, string, string?) -> 
    public static function getLine($file, $pattern, $logLabel = null)
    {
        // echo("\n".'pattern: '.$pattern);
        // Potential Alternatives:
        // grep -i "## TIMESHEET" -C 10 today.md
        // sed -n '/## TIMESHEET/,$p' today.md
        $command = 'grep "' . $pattern . '" "' . $file . '"';
        // printVar($command, 'command');
        // echo("\ncommand: ".$command);
        exec($command, $output);

        if (!empty($output)) {
            return implode("\n", $output);
        } else {
            // echo("\nNo matches found for '".($logLabel ?? $pattern)."'");
            return null;
        }
    }

    // getLine :: (string, string, string?) -> []
    public static function getLines($file, $pattern, $logLabel = null)
    {
        $command = 'grep "' . $pattern . '" "' . $file . '"';
        exec($command, $output);

        if (!empty($output)) {
            // if (in_array($pattern, ['] Lunch', '] Break', '] Morning Routine'])) {
            //     echo ("\n Matching offHours tag found for pattern: " . $pattern);
            //     echo ("\n");
            //     print_r($output);
            // }
            return $output;
        } else {
            // echo("\nNo matches found for '".($logLabel ?? $pattern)."'");
            return [];
        }
    }

    // updateLine :: (regex, string, path) -> void
    // regex, path = string
    public static function updateLine($pattern, $replacement, $file)
    {
        echo("\nAttempting to update line: \"$pattern\"");
        $oldLine = self::getLine($file, $pattern);

        if ($oldLine == $replacement) {
            // echo("\nLine \"$oldLine\" unchanged");
            // echo("\n-- No Changes");
            return;
        }

        // read entire file
        $contents = file_get_contents($file);

        // replace oldLine

        if ($oldLine) {
            $contents = str_replace($oldLine, $replacement, $contents);
                    // //write the entire string
        file_put_contents($file, $contents);

        $newLine = self::getLine($file, $pattern);

        echo "\nReplaced old line: " . $oldLine;
        echo "\nNew Line: " . $newLine;
        } else {
            $contents = $oldLine;
            echo "\nLine not updated b/c it could not be found: " . ($oldLine ? $oldLine : "EMPTY");
        }


    }

    // calculateOffHours :: (string, [ ]) -> float(2)
    public static function calculateOffHours($file, $tags)
    {
        $hoursDict = [];

        foreach ($tags as $tag) {
            $offHoursArr = self::getLines($file, '] ' . $tag, $tag);

            $hoursDict[$tag] = 0;
            if ($offHoursArr) {
                foreach ($offHoursArr as $offHoursString) {
                    $hoursDict[$tag] += Time::extractHours($offHoursString, $tag);
                }
            }
        }

        // print("<pre>".print_r($hoursDict,true)."</pre>");

        $totalOffHours = array_reduce(
            array_values($hoursDict),
            function ($carry, $item) {
                return $carry + $item;
            },
            0
        );
        return round($totalOffHours, 2);
    }

    // getTotalHours :: void -> string?
    public static function getTotalHours()
    {
        $file = self::getFile();
        $pattern = "### Total Hours:";
        $totalHours = self::extractHoursFrom(self::getLine($file, $pattern));
        return $totalHours;
    }

    // extractHoursFrom :: string -> string?
    public static function extractHoursFrom($string)
    {
        preg_match('/\d{1,2}hrs\sand\s\d{1,2}\sminutes/i', $string, $arr);
        return $arr[0] ?? null;
    }

    // extractDayFrom :: string -> string?
    public static function extractDayFrom($string)
    {
        preg_match('/[a-zA-Z]{1,6}day/i', $string, $arr);
        return $arr[0] ?? null;
    }
}