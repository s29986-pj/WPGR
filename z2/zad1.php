<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Zadanie 1</title>
    </head>
    <body>
        <h1>KALKULATOR</h1>
        <form action="" method="post">
            <input type="number" name="firstNumber" step="any" required />
            <select name="operator">
                <option value="+">+</option>
                <option value="-">-</option>
                <option value="*">*</option>
                <option value="/">/</option>
            </select>
            <input type="number" name="secondNumber" step="any" required />
            <input type="submit" value="Oblicz"/>
        </form>

        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $fnum = $_POST["firstNumber"];
                $snum = $_POST["secondNumber"];
                $op = $_POST["operator"];

                if (is_numeric($fnum) && is_numeric($snum)) {
                    echo "<h3>Wynik: ";
                    switch ($op) {
                        case '+':
                            echo $fnum + $snum;
                            break;
                        case '-':
                            echo $fnum - $snum;
                            break;
                        case '*':
                            echo $fnum * $snum;
                            break;
                        case '/':
                            if ($snum != 0) {
                                echo round($fnum / $snum, 2);
                            } else {
                                echo "Nie dziel przez 0!";
                            }
                            break;
                        default:
                            echo "Błędna operacja!";
                    }
                    echo "</h3>";
                } else {
                    echo "<h3>Wprowadź poprawne liczby!</h3>";
                }
            }
        ?>
    </body>
</html>
