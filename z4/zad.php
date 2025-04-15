<?php
session_start();

// Wylogowanie
if (isset($_GET['logout'])) {
    unset($_SESSION['loggedin']);
    session_destroy();
    setcookie("username", "", time()-3600);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}


// Logowanie - zapis ciasteczek na 7 dni
$hard_user = "admin";
$hard_pass = "admin";

if (isset($_POST['login'])) {
    $login = $_POST['login_username'];
    $pass  = $_POST['login_password'];
    if ($login === $hard_user && $pass === $hard_pass) {
        $_SESSION['loggedin'] = true;
        setcookie("username", $login, time()+7*24*3600);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $login_error = "Nieprawidłowe dane logowania.";
    }
}

// Czyszczenie formularza
if (isset($_POST['clear_form'])) {
    $baseCookies = [
        "guests", "address", "email", "credit_card", 
        "check_in", "check_out", "arrival_time", 
        "child_bed", "amenities"
    ];
    foreach ($baseCookies as $cookieName) {
        setcookie($cookieName, "", time() - 3600, "/");
    }
    
    if (isset($_COOKIE['guests'])) {
        $guestCount = (int) $_COOKIE['guests'];
        for ($i = 1; $i <= $guestCount; $i++) {
            setcookie("guest_{$i}_first_name", "", time() - 3600, "/");
            setcookie("guest_{$i}_last_name", "", time() - 3600, "/");
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>


<!-- Główna część HTML -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Zadanie - Cookies i Sesje</title>
</head>
<body>
    <?php
    // Formularz logowania oraz komunikat o braku dostępu
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        echo "<h2>Brak dostępu do rezerwacji</h2>";
        echo "<p>Dostęp do formularza rezerwacji ma tylko zalogowany użytkownik. Musisz się zalogować, ponieważ system rezerwacji wymaga utworzenia sesji, aby ustalić tożsamość użytkownika.</p>";
        if (isset($login_error)) {
            echo "<p style='color:red;'>$login_error</p>";
        }
    ?>
    <form method="post">
        <label>Login:</label>
        <input type="text" name="login_username" required><br><br>
        <label>Hasło:</label>
        <input type="password" name="login_password" required><br><br>
        <input type="submit" name="login" value="Zaloguj">
    </form>
    <?php
    } else {
        $user = isset($_COOKIE["username"]) ? $_COOKIE["username"] : "Użytkowniku";
        echo "<h2>Witaj, $user!</h2>";
        echo "<a href='?logout=1'>Wyloguj</a><br><br>";

        ?>
        <form method="post" style="margin-bottom:20px;">
            <input type="submit" name="clear_form" value="Wyczyść formularz (usuń ciasteczka)">
        </form>
        <?php

        // Formularz rezerwacji
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guests"]) && !isset($_POST["final_submit"])) {
            $guests = (int)$_POST["guests"];
            ?>
            <form action="" method="post">
                <input type="hidden" name="guests" value="<?php echo $guests; ?>">
                <input type="hidden" name="final_submit" value="1">
                <h3>Dane rezerwującego:</h3>
                <?php
                for ($i = 1; $i <= $guests; $i++) {
                    $g_first = isset($_COOKIE["guest_{$i}_first_name"]) ? $_COOKIE["guest_{$i}_first_name"] : "";
                    $g_last = isset($_COOKIE["guest_{$i}_last_name"]) ? $_COOKIE["guest_{$i}_last_name"] : "";
                    echo "<fieldset>
                            <legend>Osoba $i</legend>
                            <label>Imię:</label>
                            <input type='text' name='guest_{$i}_first_name' value='".$g_first."' required><br><br>
                            <label>Nazwisko:</label>
                            <input type='text' name='guest_{$i}_last_name' value='".$g_last."' required><br><br>";
                    if ($i == 1) {
                        $addr = isset($_COOKIE["address"]) ? $_COOKIE["address"] : "";
                        $mail = isset($_COOKIE["email"]) ? $_COOKIE["email"] : "";
                        $credit = isset($_COOKIE["credit_card"]) ? $_COOKIE["credit_card"] : "";
                        echo "<label>Adres:</label>
                            <input type='text' name='address' value='".$addr."' required><br><br>
                            <label>E-mail:</label>
                            <input type='email' name='email' value='".$mail."' required><br><br>
                            <label>Numer karty kredytowej:</label>
                            <input type='text' name='credit_card' value='".$credit."' pattern='\d{16}' title='Wprowadź 16 cyfr' required><br><br>";
                    }
                    echo "</fieldset>";
                }
                ?>
                <h3>Szczegóły rezerwacji:</h3>
                <label>Data przyjazdu:</label>
                <input type="date" name="check_in" value="<?php echo isset($_COOKIE['check_in']) ? $_COOKIE['check_in'] : ''; ?>" required><br><br>
                <label>Data wyjazdu:</label>
                <input type="date" name="check_out" value="<?php echo isset($_COOKIE['check_out']) ? $_COOKIE['check_out'] : ''; ?>" required><br><br>
                <label>Godzina przyjazdu:</label>
                <input type="time" name="arrival_time" value="<?php echo isset($_COOKIE['arrival_time']) ? $_COOKIE['arrival_time'] : ''; ?>"><br><br>
                <label>Czy potrzebujesz dostawki dla dziecka?</label>
                <input type="checkbox" name="child_bed" value="Tak" <?php if(isset($_COOKIE['child_bed']) && $_COOKIE['child_bed']=="Tak") echo "checked"; ?>> Tak<br><br>
                <label>Udogodnienia:</label><br>
                <input type="checkbox" name="amenities[]" value="klimatyzacja" <?php if(isset($_COOKIE['amenities']) && strpos($_COOKIE['amenities'],"klimatyzacja")!==false) echo "checked"; ?>> Klimatyzacja<br>
                <input type="checkbox" name="amenities[]" value="popielniczka" <?php if(isset($_COOKIE['amenities']) && strpos($_COOKIE['amenities'],"popielniczka")!==false) echo "checked"; ?>> Popielniczka<br><br>
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
                foreach($errors as $error) { echo "<li>$error</li>"; }
                echo "</ul>";
            } else {
                
                // Zapis danych do ciasteczek na 1h
                $guests = $_POST["guests"];
                setcookie("guests", $guests, time()+3600);
                for ($i = 1; $i <= $guests; $i++) {
                    setcookie("guest_{$i}_first_name", $_POST["guest_{$i}_first_name"], time()+3600);
                    setcookie("guest_{$i}_last_name", $_POST["guest_{$i}_last_name"], time()+3600);
                }
                setcookie("address", $_POST["address"], time()+3600);
                setcookie("email", $_POST["email"], time()+3600);
                setcookie("credit_card", $_POST["credit_card"], time()+3600);
                setcookie("check_in", $_POST["check_in"], time()+3600);
                setcookie("check_out", $_POST["check_out"], time()+3600);
                setcookie("arrival_time", $_POST["arrival_time"], time()+3600);
                setcookie("child_bed", isset($_POST["child_bed"]) ? "Tak" : "Nie", time()+3600);
                if (!empty($_POST["amenities"])) {
                    setcookie("amenities", implode(", ", $_POST["amenities"]), time()+3600);
                } else {
                    setcookie("amenities", "", time()+3600);
                }
                
                // Podsumowanie
                echo "<h2>Podsumowanie rezerwacji:</h2>";
                echo "<p><strong>Rezerwacja dla:</strong> " . $_POST["guests"] . ($_POST["guests"]==1 ? " osoby" : " osób") . "</p>";
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
    }
    ?>
</body>
</html>
