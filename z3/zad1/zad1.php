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

        <?php require "zad1_f.php";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $fnum = $_POST["firstNumber"];
                $snum = $_POST["secondNumber"];
                $op = $_POST["operator"];

                if (is_numeric($fnum) && is_numeric($snum)) {
                    echo "<h3>Wynik: ";
                    switch ($op) {
                        case '+':
                            echo add($fnum, $snum);
                            break;
                        case '-':
                            echo subtract($fnum, $snum);
                            break;
                        case '*':
                            echo multiply($fnum, $snum);
                            break;
                        case '/':
                            echo divide($fnum, $snum);
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
