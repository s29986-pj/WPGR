<?php
    $fruits = array("jablko", "banan", "pomarancza", "ananas", "kiwi");
    foreach ($fruits as $f) {
        $j = 0;
        while($f[$j]) {
            $j++;
        }
        for($i = $j - 1; $i >= 0; $i--) {
            echo $f[$i];
        }
        
        echo " - $f " . ($f[0] == 'p' ? "" : "nie ") . "zaczyna się literą 'p'\n\n";
    }
?>
