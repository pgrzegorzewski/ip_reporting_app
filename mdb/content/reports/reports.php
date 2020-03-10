<?php
session_start();
require '../core/connect.php';
?>

<!DOCTYPE html>
<html lang = "pl">
<head>
    <link rel="icon" href="../../resources/ip_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/mdb.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="StyleSheet" href="../../scss/core/app/home.css"/>

    <script type="text/javascript" src="../../js/jquery.min.js" ></script>
    <script type="text/javascript" src="../../js/addons/datatables.min.js" ></script>
    <script type="text/javascript" src="../../js/popper.min.js"></script>
    <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../js/mdb.min.js"></script>
    <script type="text.javascript" src="../../js/addons/datatables.min.js"></script>
    <script type="text/javascript" src="./reports.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">

</head>

<body>

<div class="container-fluid">

    <header class ="header">
        <table width = 100%>
            <tr>
                <td style = "text-align:left">
                    <h1 id="title"><a href ="../../index.php"><img src="../../resources/instalplast.png" style="width:300px;"></h1>
                </td>
                <td style = "text-align:right">
                  <span>Zalogowany jako: <?php echo $_SESSION['user'] ?>&ensp;</span><a href = "../core/logout.php"><button class="btn btn-danger" value="">Wyloguj</button></a>
                </td>
            </tr>
        </table>
    </header>
    <div class="nav">
        <ol>
            <li>
                <a  href ='../invoice_import/invoice_import.php'>Wprowadzenie faktury</a>
            </li>
            <li>
                <a href ='#' id="visited">Raporty i podsumowania</a>
            </li>
            <li>
                <a href ='#'>Korekty</a>
            </li>
            <?php
            $query = "
                       SELECT * FROM  usr.sf_sprawdz_prawo_dostepu('" . $_SESSION['user'] . "', 1)
                ";
            $hasAccessQuery = @pg_query($connection, $query);
            $hasAccess = pg_fetch_assoc($hasAccessQuery);
            if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                echo "<li>
                        <a href ='../admin/user/user.php'>Użytkownicy</a>
                    </li>";
            }
            ?>
        </ol>
    </div>
    <section class = "section">
        W tej sekcji możesz sporządzić różne rpaorty i podsumowania. Wybierz interesujący Cię raport z listy.
        <br><br>

        <div class="form-row">
          <div class="col-md-3">
            <div class="md-form form-group">
              <input class="form-control" id = "report_date_from" name = "report_date_from" type="date">
              <label for = "report_date_from">Data początkowa</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="md-form form-group">
              <input class="form-control" id = "report_date_to" name = "report_date_to" type="date">
              <label for = "report_date_to">Data końcowa</label>
            </div>
          </div>
          <div class="col-md-1">
            <button class="btn btn-info" id = "data_refresh">Odśwież/załaduj</button>
          </div>
        </div>
        <div style = "text-align:center">
            <span id="error_msg" style="color:red"></span>
            <br><br>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="data-table">
                <thead>
                <tr>
                    <th>region_nazwa</th>
                    <th>region kod</th>
                    <th>suma wartosci</th>
                    <th>suma marz</th>
                    <th>procent</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
    <div>

    </div>
    <div class="footer">
        © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
    </div>
</div>

</body>
</html>
