<?php

require_once(__DIR__.'/../utils/hours.php');

$days = "";
if (isset($argv[1])) {
    $days=$argv[1];
    echo("custom days: ".$days);
    Hours::getTotalHoursForPast($days);
} else {
    Hours::getTotalHoursForPast();
}
