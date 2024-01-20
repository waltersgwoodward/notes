<?php
require_once('init.php');
require_once(__DIR__.'/../../utils/time.php');
require_once(__DIR__.'/../../utils/helpers.php');

$file = TimeSheet::getFile();

echo "\n\n/********** Running updateTotalHours.php"." **********/";

// TODO: Figure out why these don't work
// Matches all valid time ranges
// $timeRegexPattern = '\d{1,2}:\d{2}[A&M|P&M]{0,2}';
// $timeRegexPatternSingle = "\[".$timeRegexPattern."\]";
// $timeRangeRegexPattern = "\[$timeRegexPattern-$timeRegexPattern\]"; // ex. [12:30-1:30PM]
// $arrivedRegex = $timeRegexPatternSingle." Arrived";
// echo($timeRangeRegexPattern);

$arrivalTime = Time::extractTimeFrom(TimeSheet::getLine($file, '[a-zA-Z\d:].* Arrived', 'Arrived'));
$clockedOutTime = Time::extractTimeFrom(TimeSheet::getLine($file, '[a-zA-Z\d:].* Clocked Out', 'Clocked Out'));
$offHours = TimeSheet::calculateOffHours($file, ['Lunch', 'Break', 'Morning Routine']);
// echo("\nOff hours: $offHours");

// TODO: Refactor the below to something like:
// // call(updateLine(), [file, pattern, replacement], onSuccess(), onFailure())
if ($arrivalTime && $clockedOutTime) {
    TimeSheet::updateLine('### Total Hours', '### Total Hours: '.Time::calculateTotalHours($arrivalTime, $clockedOutTime, $offHours), $file);
} else {
    TimeSheet::updateLine('### Total Hours', '### Total Hours: ---', $file);
}

if ($arrivalTime) {
    TimeSheet::updateLine('### Arrived', '### Arrived: '.$arrivalTime, $file);
} else {
    TimeSheet::updateLine('### Arrived', '### Arrived: ---', $file);
}

if ($clockedOutTime) {
    TimeSheet::updateLine('### Clocked Out', '### Clocked Out: '.$clockedOutTime, $file);
} else {
    TimeSheet::updateLine('### Clocked Out', '### Clocked Out: ---', $file);
}

if ($offHours) {
    TimeSheet::updateLine('### Break Hours', '### Break Hours: '.convertDecimalTo($offHours), $file);
} else {
    TimeSheet::updateLine('### Break Hours', '### Break Hours: ---', $file);
}

echo("\nDone\n\n");
