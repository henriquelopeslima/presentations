<?php

class AsyncOperation {
    public function __construct(
        public string $name,
        public Fiber $fiber,
        public int $delaySeconds,
        public float $startTime
    ) {}
}

function simulateAPI(string $name, int $delaySeconds): string {
    echo "$name | Perando : {$delaySeconds}s.\n";
    
    $resumeData = Fiber::suspend([$name, $delaySeconds]); 
    $latency = $resumeData['latency'] ?? 'N/A';
    
    return "[$name] Cabou. Latência: {$latency}.\n";
}

function scheduler(Fiber ...$initialFibers): array {
    $pending = [];
    $results = [];
    
    $loopStartTime = microtime(true);
    
    foreach ($initialFibers as $fiber) {
        $operationInfo = $fiber->start();
        
        $pending[] = new AsyncOperation(
            $operationInfo[0],
            $fiber,
            $operationInfo[1],
            microtime(true)
        );
    }
    
    echo "\nFibers foram iniciadas e suspensas.\n";

    while (!empty($pending)) {
        $finishedCount = 0;
        
        foreach ($pending as $key => $op) {
            $elapsed = microtime(true) - $op->startTime;

            if ($elapsed >= $op->delaySeconds) {
                $latencyStr = round($elapsed, 2) . 's';

                $result = $op->fiber->resume(['latency' => $latencyStr]);

                $results[] = $result;
                unset($pending[$key]);
                $finishedCount++;
            }
        }
        
        if ($finishedCount > 0) {
            $pending = array_values($pending);
        }
    }

    $loopTotalTime = microtime(true) - $loopStartTime;
    echo "\nTempo total do Agendador: " . round($loopTotalTime, 2) . "s\n";
    
    return $results;
}

echo "--- PHP 8.4 com Fibers (Concorrência Real) ---\n";
$startTime = microtime(true);

$fibers = [
    new Fiber(fn() => simulateAPI('A', 2)),
    new Fiber(fn() => simulateAPI('B', 2)),
    new Fiber(fn() => simulateAPI('C', 2)),
    new Fiber(fn() => simulateAPI('D', 2)),
    new Fiber(fn() => simulateAPI('E', 2)),
];

$final_results = scheduler(...$fibers);

$total_time = microtime(true) - $startTime;

echo "\n--- END TIME ---\n";
echo "Tempo total: " . round($total_time, 2) . " segundos.\n";
echo "Requisição mais lenta (2s).\n";
