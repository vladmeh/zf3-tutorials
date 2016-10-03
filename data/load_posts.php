<?php
$db = new PDO('sqlite:' . realpath(__DIR__) . 'zftutorial.db');
$fh = fopen(__DIR__ . '/posts.schema.sql', 'r');
while ($line = fread($fh, 4096)) {
    $line = trim($line);
    $db->exec($line);
}
fclose($fh);