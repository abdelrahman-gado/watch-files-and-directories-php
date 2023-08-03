<?php

$path = $argv[1];
$currentStatus = [];
readPath($path, $currentStatus);

while (true) {
    clearCache($path);
    checkPath($path);
    sleep(1);
}

function readPath($path, &$filesMap)
{
    $filesMap[$path] = filemtime($path);
    if (is_dir($path)) {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $fileName = $path . '/' . $file;
            $filesMap[$fileName] = filemtime($fileName);
            if (is_dir($fileName)) {
                readPath($fileName, $filesMap);
            }
        }
    }
}

function clearCache($path)
{
    clearstatcache(false, $path);
}

function checkPath($path)
{
    global $currentStatus;
    $newStatus = [];
    readPath($path, $newStatus);

    foreach ($currentStatus as $file => $time) {
        if (!isset($newStatus[$file])) {
            echo "File {$file} was deleted ..." . PHP_EOL; 
        } elseif ($time !== $newStatus[$file]) {
            echo "File {$file} was modified ..." . PHP_EOL;
        }
    }

    foreach ($newStatus as $file => $time) {
        if (!isset($currentStatus[$file])) {
            echo "File {$file} was added ..." . PHP_EOL;
        }
    }

    $currentStatus = $newStatus;
}