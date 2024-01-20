<?php
require_once('init.php');
require_once(__DIR__ . '/../utils/time.php');
require_once(__DIR__ . '/../utils/helpers.php');

$file = TimeSheet::getFile();

echo "\n\n/********** Running updateTotalHours.php" . " **********/";

$arrivalTime = TimeSheet::getLine($file, '[a-zA-Z\d:].* Arrived', 'Arrived') !== null ? Time::extractTimeFrom(TimeSheet::getLine($file, '[a-zA-Z\d:].* Arrived', 'Arrived')) : null;
$clockedOutTime = TimeSheet::getLine($file, '[a-zA-Z\d:].* Clocked Out', 'Clocked Out') ? Time::extractTimeFrom(TimeSheet::getLine($file, '[a-zA-Z\d:].* Clocked Out', 'Clocked Out')) : null;
$offHours = TimeSheet::calculateOffHours($file, ['Lunch', 'Break', 'Morning Routine']);

if ($arrivalTime && $clockedOutTime) {
    TimeSheet::updateLine('### Total Hours', '### Total Hours: ' . Time::calculateTotalHours($arrivalTime, $clockedOutTime, $offHours), $file);
} else {
    TimeSheet::updateLine('### Total Hours', '### Total Hours: ---', $file);
}

if ($arrivalTime) {
    TimeSheet::updateLine('### Arrived', '### Arrived: ' . $arrivalTime, $file);
} else {
    TimeSheet::updateLine('### Arrived', '### Arrived: ---', $file);
}

if ($clockedOutTime) {
    TimeSheet::updateLine('### Clocked Out', '### Clocked Out: ' . $clockedOutTime, $file);
} else {
    TimeSheet::updateLine('### Clocked Out', '### Clocked Out: ---', $file);
}

if ($offHours) {
    TimeSheet::updateLine('### Break Hours', '### Break Hours: ' . convertDecimalTo($offHours), $file);
} else {
    TimeSheet::updateLine('### Break Hours', '### Break Hours: ---', $file);
}

echo ("\nDone\n\n");
