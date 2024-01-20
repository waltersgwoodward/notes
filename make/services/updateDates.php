<?php
require_once('init.php');
require_once(__DIR__.'/../utils/time.php');
require_once(__DIR__."/../utils/helpers.php");

echo "\n/********** Running updateDates.php"." **********/";

// Correct Timezone in DateTime object:
$localDateTimeNow = Time::local();

$dateToday = Time::format($localDateTimeNow, 'l, F jS, Y');
$timeToday = Time::format($localDateTimeNow, 'H');

TimeSheet::updateLine("# TODAY", "# TODAY ".$dateToday, $file);
TimeSheet::updateLine('## TIMESHEET', '## TIMESHEET: '.$dateToday, $file);
echo("\nDone");