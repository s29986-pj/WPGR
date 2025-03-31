<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Zadanie 3 - CMS</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        td {
            white-space: nowrap;
        }
    </style>
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
                        <input type='text' name='guest_{$i}_first_name'><br><br>
                        <label>Nazwisko:</label>
                        <input type='text' name='guest_{$i}_last_name'><br><br>";
                if ($i == 1) {
                    echo "<label>Adres:</label>
                        <input type='text' name='address'><br><br>
                        <label>E-mail:</label>
                        <input type='email' name='email'><br><br>
                        <label>Numer karty kredytowej:</label>
                        <input type='text' name='credit_card' pattern='\d{16}' title='Wprowadź 16 cyfr'><br><br>";
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
                $data = [];

                for ($i = 1; $i <= 4; $i++) {
                    $data[] = isset($_POST["guest_{$i}_first_name"]) ? $_POST["guest_{$i}_first_name"] : "";
                    $data[] = isset($_POST["guest_{$i}_last_name"]) ? $_POST["guest_{$i}_last_name"] : "";
                }
                $data[] = $_POST["address"];
                $data[] = $_POST["email"];
                $data[] = $_POST["credit_card"];
                $data[] = $_POST["check_in"];
                $data[] = $_POST["check_out"];
                $data[] = $_POST["arrival_time"] ?: "Nie podano";
                $data[] = isset($_POST["child_bed"]) ? "Tak" : "Nie";
                $data[] = !empty($_POST["amenities"]) ? implode(", ", $_POST["amenities"]) : "Brak";

                $file = fopen("rezerwacje.csv", "a");

                if (filesize("rezerwacje.csv") == 0) {
                    $headers = [];
                    for ($i = 1; $i <= 4; $i++) {
                        $headers[] = "Imię_$i";
                        $headers[] = "Nazwisko_$i";
                    }
                    $headers[] = "Adres";
                    $headers[] = "E-mail";
                    $headers[] = "Numer karty";
                    $headers[] = "Data przyjazdu";
                    $headers[] = "Data wyjazdu";
                    $headers[] = "Godzina przyjazdu";
                    $headers[] = "Dostawka dla dziecka";
                    $headers[] = "Udogodnienia";
                    fputcsv($file, $headers, ";");
                }

                fputcsv($file, $data, ";");
                fclose($file);

                echo "<h2>Rezerwacja została zapisana!</h2>";
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

    <form action="" method="post">
        <br /><input type="submit" name="load_data" value="Wczytaj dane">
    </form>

    <?php
        if (isset($_POST["load_data"])) {
            if (file_exists("rezerwacje.csv")) {
                $file = fopen("rezerwacje.csv", "r");
        
                echo "<h3>Wczytane dane:</h3><table>";
                $first_line = true;
                while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
                    echo "<tr>";
                    foreach ($row as $column) {
                        echo $first_line ? "<th>" . $column . "</th>" : "<td>" . $column . "</td>";
                    }
                    echo "</tr>";
                    $first_line = false;
                }
                echo "</table>";
                fclose($file);
            } else {
                echo "<h3>Brak zapisanych danych.</h3>";
            }
        }        
    ?>
</body>
</html>
