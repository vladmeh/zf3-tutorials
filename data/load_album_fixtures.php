<?php
$db = new PDO('sqlite:' . realpath(__DIR__) . '/zftutorial.db');
$fh = fopen(__DIR__ . '/album-fixtures.sql', 'r');
while ($line = fread($fh, 4096)) {
    $db->exec($line);
}
fclose($fh);