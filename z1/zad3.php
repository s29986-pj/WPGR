<?php
    $n = 10;
    $fib = [0, 1];
    for($i = 2; $i < $n; $i++) {
        $fib[$i] =  $fib[$i - 2] + $fib[$i - 1]; 
    }
    echo "N-ta liczba ciągu Fibonacciego dla N = $n: " . $fib[$n - 1] . "\n\n";

    echo "Liczby nieparzyste ciągu Fibonacciego:\n";
    $lineNumber = 1;
    for($i = 0; $i < $n; $i++) {
        if($fib[$i] % 2 != 0) {
            echo "$lineNumber. " . $fib[$i] . "\n";
            $lineNumber++;
        }
    }
?>
