<?php
require_once(__DIR__.'/../utils/timeSheet.php');
require_once(__DIR__.'/../utils/hours.php');

echo(Hours::archive());
echo(TimeSheet::archive());