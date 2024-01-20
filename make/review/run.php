<?php
require_once(__DIR__.'/../utils/timeSheet.php');
require_once(__DIR__.'/../utils/reviews.php');

$path = __DIR__.'/../../reviews';

$ticket = "";
if ($argv[1]) {
    $ticket=$argv[1];
    echo("ticket: ".$ticket);
}

$command = "open ".Reviews::make($path, $ticket);
