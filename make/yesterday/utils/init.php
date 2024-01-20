<?php

require_once(__DIR__.'/../../utils/timeSheet.php');
$file = TimeSheet::getFile();
echo($file);

// echo (TimeSheet::getMatchAndBelow('## TIMESHEET'));
// echo(TimeSheet::removeOldTimeSheet(TimeSheet::getFile(), '## TIMESHEET'));