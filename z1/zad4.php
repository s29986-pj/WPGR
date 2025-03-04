<?php
    $lorem = "Lorem Ipsum is simply dummy text of the printing and typesettings indutryLorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

    $arr = explode(" ", $lorem);
    
    for ($i = 0; $i < count($arr); $i++) {
        $cleanWord = "";
        for ($j = 0; $j < strlen($arr[$i]); $j++) {
            if (!in_array($arr[$i][$j], [".", ","])) { 
                $cleanWord .= $arr[$i][$j];
            }
        }
        $arr[$i] = $cleanWord;
    }
    
    $assocArray = [];
    $keys = [];
    $values = [];
    
    foreach ($arr as $index => $word) {
        if ($index % 2 == 0) {
            $keys[] = $word;
        } else {
            $values[] = $word;
        }
    }
    
    for ($i = 0; $i < count($values); $i++) {
        $assocArray[$keys[$i]] = $values[$i];
    }
    
    foreach ($assocArray as $key => $value) {
        echo "$key: $value\n";
    }
    
    
    // Program działa poprawnie. Wyświetlany wynik się niepoprawny, ponieważ 
    // klucze nie są unikalne. Poniżej zamieszczam pętlę, która wyświetla
    // poprawny wynik (pętla odlicza do $values, ponieważ ilość słów jest 
    // nieparzysta - 91):
    
    // for ($i = 0; $i < count($values); $i++) {
    //     echo $keys[$i] . ": " . $values[$i] . "\n";
    // }
    

    // Tutaj wypisywałem wartości kluczy i wartości razem z ich indeksami, aby
    // upewnić się, że do siebie pasują:

    // foreach ($keys as $key => $value) {
    //     echo "$key: $value\n";
    // }
    // foreach ($values as $key => $value) {
    //     echo "$key: $value\n";
    // }
    
    
    // Wykorzystywałem również funkcje wbudowane do debugowania:
    // $assocArray = array_combine($keys, $values);
    // print_r($assocArray);
?>
