<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Zadanie 2</title>
    </head>
    <body>
        <h1>Formularz rezerwacji hotelu</h1>
        <form action="" method="post">
            <label>Ilość osób:</label>
            <select name="guests" required>
                <option value="1">1 osoba</option>
                <option value="2">2 osoby</option>
                <option value="3">3 osoby</option>
                <option value="4">4 osoby</option>
            </select><br><br>

            <label>Imię:</label>
            <input type="text" name="first_name" required><br><br>

            <label>Nazwisko:</label>
            <input type="text" name="last_name" required><br><br>

            <label>Adres:</label>
            <input type="text" name="address" required><br><br>

            <label>E-mail:</label>
            <input type="email" name="email" required><br><br>

            <label>Numer karty kredytowej:</label>
            <input type="text" name="credit_card" pattern="\d{16}" title="Wprowadź 16 cyfr" required><br><br>

            <label>Data przyjazdu:</label>
            <input type="date" name="check_in" required><br><br>

            <label>Data wyjazdu:</label>
            <input type="date" name="check_out" required><br><br>

            <label>Godzina przyjazdu:</label>
            <input type="time" name="arrival_time"><br><br>

            <label>Czy potrzebujesz dostawki dla dziecka?</label>
            <input type="checkbox" name="child_bed" value="Tak"> Tak<br><br>

            <label>Udogodnienia:</label><br>
            <input type="checkbox" name="amenities[]" value="klimatyzacja"> Klimatyzacja<br>
            <input type="checkbox" name="amenities[]" value="popielniczka"> Popielniczka<br><br>

            <input type="submit" value="Zarezerwuj">
        </form>

        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $errors = [];

                if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Podano niepoprawny e-mail.";
                }

                $check_in = $_POST["check_in"];
                $check_out = $_POST["check_out"];
                if ($check_in < date("Y-m-d")) {
                    $errors[] = "Data przyjazdu nie może być z przeszłości.";
                }
                if ($check_out <= $check_in) {
                    $errors[] = "Data wyjazdu musi być po dacie przyjazdu.";
                }

                if (!empty($errors)) {
                    echo "<h3>Błędy w formularzu:</h3><ul>";
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<h2>Podsumowanie rezerwacji:</h2>";
                    echo "<p><strong>Rezerwacja dla:</strong> " . $_POST["guests"] . ($_POST["guests"] == 1 ? " osoby" : " osób") . "</p>";
                    echo "<p><strong>Imię:</strong> " . $_POST["first_name"] . "</p>";
                    echo "<p><strong>Nazwisko:</strong> " . $_POST["last_name"] . "</p>";
                    echo "<p><strong>Adres:</strong> " . $_POST["address"] . "</p>";
                    echo "<p><strong>E-mail:</strong> " . $_POST["email"] . "</p>";
                    echo "<p><strong>Numer karty:</strong> **** **** **** " . substr($_POST["credit_card"], -4) . "</p>";
                    echo "<p><strong>Data pobytu:</strong> od $check_in do $check_out</p>";
                    echo "<p><strong>Godzina przyjazdu:</strong> " . ($_POST["arrival_time"] ?: "Nie podano") . "</p>";
                    echo "<p><strong>Dostawka dla dziecka:</strong> " . (isset($_POST["child_bed"]) ? "Tak" : "Nie") . "</p>";
                    echo "<p><strong>Udogodnienia:</strong> " . (!empty($_POST["amenities"]) ? implode(", ", $_POST["amenities"]) : "Brak") . "</p>";
                }
            }
        ?>
    </body>
</html>
