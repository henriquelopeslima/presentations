<?php

function taskBlock(string $name) {
    echo "[$name] Start. Main script waiting...\n";
    sleep(2); 
    echo "[$name] End.".PHP_EOL;
}

echo "Exec PHP sync".PHP_EOL;

$startTime = microtime(true);

taskBlock("Task A");
taskBlock("Task B");

$totalTime = microtime(true) - $startTime;

echo 'Fim da execução.'.PHP_EOL;
echo ' Tempo total (Marromeno): '.round($totalTime, 2).' segundos'.PHP_EOL;