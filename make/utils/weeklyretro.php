<?php
require_once(__DIR__.'/time.php');

class WeeklyRetro {
    // make :: (string, string) -> void
    public static function make($path, $message = null) {
        $fileName = "weekly-retro__".Time::local('now', 'Y-m-d').($message ? "--".$message : '').".md";
        echo($fileName);
        $pathToFile = $path."/".$fileName;
        if (file_exists($pathToFile)) {
            $continue = readline("\nWARNING: $fileName already exists. Continuing will erase all contents. Do you want to continue? (Y|n)");
            if ($continue == 'Y') {
                $file = fopen($pathToFile, "w");
                echo("\n$fileName created successfully!");
                return $pathToFile;
            } else {
                echo("\nOk! Byyeeeee");
                die();
            }
        } else {
            $file = fopen($pathToFile, "w");
            echo("\n$fileName created successfully!");
            return $pathToFile;
        }
    }
}