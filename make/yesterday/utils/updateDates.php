<?php
require_once('init.php');
require_once(__DIR__.'/../../utils/time.php');
require_once(__DIR__."/../../utils/helpers.php");

echo "\n/********** Running updateDates.php"." **********/";

// Correct Timezone in DateTime object:
$dateYesterday = Time::format(Time::local('yesterday'), 'l, F jS, Y');

TimeSheet::updateLine("# TODAY", "# TODAY ".$dateYesterday, $file);
TimeSheet::updateLine('## TIMESHEET', '## TIMESHEET: '.$dateYesterday, $file);
echo("\nDone");