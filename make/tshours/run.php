<?php
require_once(__DIR__.'/../utils/timeSheet.php');
require_once(__DIR__.'/../utils/helpers.php');

$totalHours = implode("\n", TimeSheet::getLine(__DIR__.'/../../today.md', "### Total Hours:"));

echo($totalHours);

$file = __DIR__.'/../../today.md';

$parsed = getStringBetween(file_get_contents($file), '## TIMESHEET:', '### Total Hours:');

echo($parsed);