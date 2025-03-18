<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zadanie 4</title>
</head>
<body>
    <h1>Sprawdź, czy liczba jest pierwsza</h1>
    <form method="post">
        <label>Podaj liczbę:</label>
        <input type="number" name="number" required>
        <input type="submit" value="Sprawdź">
    </form>

    <?php
        function isPrime($n, &$iterations) {
            $iterations = 0;

            if ($n < 2) return false;
            if ($n == 2 || $n == 3) return true;
            if ($n % 2 == 0 || $n % 3 == 0) return false;

            // 6k±1
            for ($i = 5; $i * $i <= $n; $i += 6) {
                $iterations++;
                if ($n % $i == 0 || $n % ($i + 2) == 0) return false;
            }

            return true;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $number = (int)$_POST["number"];
            
            if ($number === false || $number <= 0) {
                echo "<p>Podaj poprawną liczbę całkowitą dodatnią.</p>";
            } else {
                $iterations = 0;
                $isPrime = isPrime($number, $iterations);

                echo "<p>Liczba <strong>$number</strong> " . ($isPrime ? "jest" : "nie jest") . " liczbą pierwszą.</p>";
                echo "<p>Ilość iteracji pętli: <strong>" . $iterations . "</strong></p>";
            }
        }
    ?>
</body>
</html>
