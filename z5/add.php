<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Samochody_Add</title>
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Comic Sans MS';
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #1e1e1e;
            width: 100%;
        }

        nav table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        nav td {
            padding: 0;
            text-align: center;
        }

        nav a {
            display: block;
            padding: 15px;
            color: #f0f0f0;
            text-decoration: none;
        }

        nav a:hover {
            background-color: #333333;
        }

        main {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 1rem;
            padding-top: 10px;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #1e1e1e;
            color: #fff;
            font-size: 1rem;
        }

        input[type="submit"] {
            background-color: #cc3333;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s ease;
            font-family: 'Comic Sans MS';
        }

        input[type="submit"]:hover {
            background-color: #ff4d4d;
        }
    </style>

</head>
<body>
    <div id="container">
        <nav>
            <table id="menu">
                <tr>
                    <td><a href="main.php">Strona główna</a></td>
                    <td><a href="cars.php">Wszystkie samochody</a></td>
                    <td><a href="add.php">Dodaj samochód</a></td>
                </tr>
            </table>
        </nav>
        <main>
            <h2>Dodaj nowy samochód</h2>
            <form method="post" action="">
                <label>Marka:</label>
                <input type="text" name="marka" required>
                <label>Model:</label>
                <input type="text" name="model" required>
                <label>Cena:</label>
                <input type="number" name="cena" step="0.01" required>
                <label>Rok:</label>
                <input type="number" name="rok" min="1950" max="2025" required>
                <label>Opis:</label>
                <textarea name="opis" rows="4" cols="40" required></textarea><br>
                <input type="submit" value="Dodaj samochód">
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $con = mysqli_connect("localhost","root","root","mojaBaza");

                if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
                exit();
                }

                $marka = $_POST["marka"];
                $model = $_POST["model"];
                $cena = (double)$_POST["cena"];
                $rok = (int)$_POST["rok"];
                $opis = $_POST["opis"];

                $query = "INSERT INTO samochody (marka, model, cena, rok, opis)
                            VALUES ('$marka', '$model', $cena, $rok, '$opis')";

                if($result = mysqli_query($con, $query)) {
                    echo "<p> Car added!</p>";
                } else {    
                    echo "<p>Failed to insert data.</p>";
                }

                if(!mysqli_close($con)) {
                    echo "Failed to close connection to database.";
                }
            }
            ?>
        </main>    
    </div>
</body>
</html>
