<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Zadanie 3</title>
</head>
<body>
    <h1>Formularz rezerwacji hotelu</h1>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guests"]) && !isset($_POST["final_submit"])) {
            $guests = (int)$_POST["guests"];
            ?>

            <form action="" method="post">
                <input type="hidden" name="guests" value="<?php echo $guests; ?>">
                <input type="hidden" name="final_submit" value="1">

                <h3>Dane rezerwującego:</h3>
                <?php
                for ($i = 1; $i <= $guests; $i++) {
                    echo "<fieldset>
                            <legend>Osoba $i</legend>
                            <label>Imię:</label>
                            <input type='text' name='guest_{$i}_first_name' required><br><br>
                            <label>Nazwisko:</label>
                            <input type='text' name='guest_{$i}_last_name' required><br><br>";
                    if ($i == 1) {
                        echo "<label>Adres:</label>
                            <input type='text' name='address' required><br><br>
                            <label>E-mail:</label>
                            <input type='email' name='email' required><br><br>
                            <label>Numer karty kredytowej:</label>
                            <input type='text' name='credit_card' pattern='\d{16}' title='Wprowadź 16 cyfr' required><br><br>";
                    }
                    echo "</fieldset>";
                }
                ?>
                <h3>Szczegóły rezerwacji:</h3>
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

                <input type="submit" value="Zatwierdź rezerwację">
            </form>

            <?php
        } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["final_submit"])) {
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
                echo "<h3>Goście:</h3><ul>";
                for ($i = 1; $i <= $_POST["guests"]; $i++) {
                    echo "<li><strong>" . $_POST["guest_{$i}_first_name"] . " " . $_POST["guest_{$i}_last_name"] . "</strong></li>";
                    if ($i == 1) {
                        echo "<p><strong>Adres:</strong> " . $_POST["address"] . "</p>";
                        echo "<p><strong>E-mail:</strong> " . $_POST["email"] . "</p>";
                        echo "<p><strong>Numer karty:</strong> **** **** **** " . substr($_POST["credit_card"], -4) . "</p>";
                    }
                }
                echo "</ul>";

                echo "<p><strong>Data pobytu:</strong> od " . $_POST["check_in"] . " do " . $_POST["check_out"] . "</p>";
                echo "<p><strong>Godzina przyjazdu:</strong> " . ($_POST["arrival_time"] ?: "Nie podano") . "</p>";
                echo "<p><strong>Dostawka dla dziecka:</strong> " . (isset($_POST["child_bed"]) ? "Tak" : "Nie") . "</p>";
                echo "<p><strong>Udogodnienia:</strong> " . (!empty($_POST["amenities"]) ? implode(", ", $_POST["amenities"]) : "Brak") . "</p>";
            }
        } else {
            ?>
            <form action="" method="post">
                <label>Ilość osób:</label>
                <select name="guests" required>
                    <option value="1">1 osoba</option>
                    <option value="2">2 osoby</option>
                    <option value="3">3 osoby</option>
                    <option value="4">4 osoby</option>
                </select><br><br>
                <input type="submit" value="Dalej">
            </form>
            <?php
        }
    ?>
</body>
</html>
