<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Samochody_Main</title>
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
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 30px 0;
        }

        #data {
            width: 80%;
            border-collapse: collapse;
        }

        #data th, #data td {
            padding: 12px;
            text-align: center;
            border: none;
        }

        #data tr:nth-child(even) {
            background-color: #4d1a1a;
        }

        #data tr:nth-child(odd) {
            background-color: #661a1a;
        }

        #data th {
            background-color: #990000;
            color: white;
        }

        .details-cell {
            background-color: #121212;
            width: 10%;
        }

        .details-btn {
            background-color: #cc3333;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 0.95em;
            transition: background-color 0.2s ease;
            font-family: 'Comic Sans MS';
        }

        .details-btn:hover {
            background-color: #ff4d4d;
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

    $query = 'SELECT * FROM samochody ORDER BY cena';
    if(!$result = mysqli_query($con, $query)) {
        mysqli_close($con);
        echo "Failed to show result";
    }
    ?>
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
            <table id="data">
                <tr>
                    <th>ID</th>
                    <th>Marka</th>
                    <th>Model</th>
                    <th>Cena</th>
                </tr>
                
                <!-- Wariant B -->
                <?php
                for($i = 0; $i < 5; $i++) {
                    $row = mysqli_fetch_row($result);
                    echo "<tr>";
                    echo "<td>$row[0]</td>";
                    echo "<td>$row[1]</td>";
                    echo "<td>$row[2]</td>";
                    echo "<td>$row[3]</td>";
                    echo "<td class='details-cell'>
                             <form method='get' action='details.php'>
                                 <input type='hidden' name='id' value='$row[0]'>
                                 <input type='submit' class='details-btn' value='Szczegóły'>
                             </form>
                         </td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </main>    
    </div>
    <?php
    if(!mysqli_close($con)) {
        echo "Failed to close connection to database.";
    }
    ?>
</body>
</html>
