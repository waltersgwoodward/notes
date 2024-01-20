<?php
require_once(__DIR__.'/time.php');

class Reviews {
    // make :: (string, string) -> void
    public static function make($path, $ticket = null) {
        $folderName = ($ticket ? $ticket : 'NAME-ME');
        $fileName = "notes.md";
        $pathToFile = $path."/".$folderName."/".$fileName;
        if (file_exists($pathToFile)) {
            fopen($pathToFile, "r+");
            exec("open ".$pathToFile);
        } else {
            mkdir($path."/".$folderName."/");
            echo("\n".$folderName." created successfully!\n\n");

            $file = fopen($pathToFile, "w");
            echo("\n$fileName created successfully!");

            self::appendBlank($pathToFile, $ticket);

            exec("open ".$pathToFile);
            return $pathToFile;
        }
    }

    // appendBlank :: (string, string) -> void
    public static function appendBlank($file, $ticket) {
        file_put_contents($file, $ticket, FILE_APPEND | LOCK_EX);
    }
}
