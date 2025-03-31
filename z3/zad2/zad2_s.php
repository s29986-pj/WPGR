<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $t1 = trim($_POST["t1"]);
    $t2 = trim($_POST["t2"]);

    if (!empty($t1) && !empty($t2)) {
        $data = "$t1 - $t2" . PHP_EOL;

        if (file_put_contents("dane.txt", $data, FILE_APPEND) !== false) {
            echo "<h3>Dane zapisane pomyślnie!</h3>";
        } else {
            echo "<h3>Błąd zapisu do pliku!</h3>";
        }
    } else {
        echo "<h3>Wypełnij oba pola!</h3>";
    }
}
?>
<br><a href="zad2.php">Powrót</a>
