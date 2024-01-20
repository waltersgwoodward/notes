<?php
require_once(__DIR__ . '/../utils/timeSheet.php');

$printVarCount = 1;

function printVar($variable, $tag = null, $wrappers = null)
{
    global $printVarCount;

    if (is_null($tag)) {
        $tag = 'printVar #' . $printVarCount;
    }
    $printVarCount++;

    $supportedTypes = ['integer', 'string', 'array', 'object', 'double'];
    $dataType = gettype($variable);
    $padding = "\n\n";
    if (!in_array($dataType, $supportedTypes)) {
        print_r("\nWARNING: Variable with tag: $tag cannot be logged with printVar because the " . $dataType . " data type is not supported");
    }

    if (in_array($dataType, ['string', 'integer', 'double'])) {
        if ($dataType === 'string') {
            echo "\n$tag:" .
                "\n\ttype: $dataType" .
                "\n\tcharCount: " . strlen($variable) .
                "\n\tvalue: $variable";
            echo ($padding);
        } else if (in_array($dataType, ['integer', 'double'])) {
            echo "\n$tag:" .
                "\n\ttype: $dataType" .
                "\n\tvalue: $variable";
            echo ($padding);
        }
    } else if (in_array($dataType, ['object', 'array'])) {
        echo "\n$tag: ";
        print_r($variable);
        echo ($padding);
        if (is_array($wrappers) && !empty($wrappers)) {
            echo ("\nAdditonal Properties:");
            foreach ($wrappers as $wrapper) {
                echo ("\n");
                print_r(call_user_func($wrapper, $variable));
            }
        }
    }
}


// getStringBetween :: (string, string, string) -> string
// description: A convenient wrapper for strpos that allows you to provide a string and two substrings, and get the substring between them. 
// note: The way this is written, startStr is included, but endStr is excluded
function getStringBetween($haystack, $startStr, $endStr)
{
    $startIndex = strpos($haystack, $startStr);

    $fullEndStr = getLine(findLineNumber($endStr));
    echo $fullEndStr;

    $endIndex = strpos($haystack, $endStr) + strlen($fullEndStr);

    $length =  $endIndex - $startIndex;

    return substr($haystack, $startIndex, $length);
}

function findLineNumber($string)
{
    $line_number = false;
    if ($handle = fopen(TimeSheet::getFile(), "r")) {
        $count = 0;
        while (($line = fgets($handle, 4096)) !== FALSE and !$line_number) {
            $count++;
            $line_number = (strpos($line, $string) !== FALSE) ? $count : $line_number;
        }
        fclose($handle);
    }
    return $line_number;
}

function getLine($lineNumber)
{
    $myFile = TimeSheet::getFile(); // e.g. today.md
    $lines = file($myFile); // convert file to an array
    return $lines[$lineNumber - 1]; // return desired line
}

function convertDecimalTo($double)
{
    // printVar($double, 'Double');
    $hours = floor($double);

    // printVar($hours, 'Hours');
    $decimal = $double - $hours;

    // printVar($decimal, 'Decimal');
    $minutes = $decimal * 60;

    // printVar(($hours == 0 ? '' : ($hours . "hr".($hours > 1 ? "s" : "")) . " and ") . $minutes . " minutes", 'Final Output');
    return ($hours == 0 ? '' : ($hours . "hr" . ($hours > 1 ? "s" : "")) . " and ") . round($minutes) . " minutes";
}

function convertToDecimal($hours, $minutes)
{
    return $hours + round($minutes / 60, 2);
}
