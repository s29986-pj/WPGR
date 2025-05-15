<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Samochody_Details</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Comic Sans MS';
            background-color: #121212;
            color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color:  #4d1a1a;
        }

        tr:nth-child(odd) {
            background-color: #2a1212;
        }

        th {
            background-color: #990000;
        }

        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #cc3333;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.2s ease;
            font-family: 'Comic Sans MS';
        }

        .back-link:hover {
            background-color: #ff4d4d;
        }

        .back-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php 
    $con = mysqli_connect("localhost","root","root","mojaBaza");

    if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
    }

    $id = (int) $_GET["id"];
    $query = "SELECT * FROM samochody WHERE id = $id";
    if(!$result = mysqli_query($con, $query)) {
        mysqli_close($con);
        echo "Failed to show result";
    }
    ?>
    <div id="container">
        <main>
            <table id="data">
                <tr>
                    <th>ID</th>
                    <th>Marka</th>
                    <th>Model</th>
                    <th>Cena</th>
                    <th>Rok</th>
                    <th>Opis</th>
                </tr>
                
                <?php
                    $row = mysqli_fetch_row($result);
                    echo "<tr>";
                    echo "<td>$row[0]</td>";
                    echo "<td>$row[1]</td>";
                    echo "<td>$row[2]</td>";
                    echo "<td>$row[3]</td>";
                    echo "<td>$row[4]</td>";
                    echo "<td>$row[5]</td>";
                    echo "</tr>";
                ?>
            </table>
            <div class="back-container">
                <a class="back-link" href="main.php">Powrót do strony głównej</a>
            </div>
        </main>    
    </div>
    <?php
    if(!mysqli_close($con)) {
        echo "Failed to close connection to database.";
    }
    ?>
</body>
</html>
