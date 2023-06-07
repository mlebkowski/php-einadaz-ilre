<?php
require_once __DIR__ . '/src/getDifferingPairs.php';

ini_set('memory_limit', '10M');

function getLines(string $sourceName): \Generator
{
    $handle = fopen("https://storage.googleapis.com/zadanie-php-dane/{$sourceName}.jsonl", 'r');
    while (!feof($handle)) {
        yield json_decode(fgets($handle), true);
    }
}

$cursor = getDifferingPairs(getLines('1'), getLines('2'));
foreach ($cursor as [$product1, $product2]) {
    if ($product1 && $product2) {
        echo "{$product1['id']} jest różne \n";
    } else if ($product1) {
        echo "{$product1['id']} jest tylko w pierwszym\n";
    } else {
        echo "{$product2['id']} jest tylko w drugim\n";
    }
}
