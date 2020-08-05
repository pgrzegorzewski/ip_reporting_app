<?php
session_start();
require '../core/connect.php';

?>

<!DOCTYPE html>
<html lang = "pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Instalplast rozliczenia</title>

    <link rel="icon" href="../../resources/ip_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/mdb.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="StyleSheet" href="../../scss/core/app/home.css"/>
    <link rel="StyleSheet" href="../../scss/core/app/reports.css"/>

    <script type="text/javascript" src="../../js/jquery.min.js" ></script>
    <script type="text/javascript" src="../../js/addons/datatables.min.js" ></script>
    <script type="text/javascript" src="../../js/popper.min.js"></script>
    <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../js/mdb.min.js"></script>
    <script type="text/javascript" src="./reports.js"></script>
    <script type="text/javascript" src="./charts/item_charts.js"></script>
    <script type="text/javascript" src="./charts/client_charts.js"></script>
    <script type="text/javascript" src="./charts/salesman_charts.js"></script>
    <script type="text/javascript" src="./charts/region_charts.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <script src="../../resources/lodash.js"></script>
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
          <?php
            $query = "
                   SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 2)
            ";
            $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
            $hasAccess = pg_fetch_assoc($hasAccessQuery);
            if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                echo "
                        <li>
                          <a href='#'><i class='far fa-file-alt'></i>  Wprowadzenie faktury</a>
                          <ul>
                            <li><a href ='../invoice_import/invoice_import.php'><i class='far fa-file-alt'></i> Wprowadzenie faktury</a></li>
                            <li><a href ='../invoice_import_improved/invoice_import_improved.php'><i class='far fa-file-alt'></i> Szybki import</a></li>
                            </ul>
                        </li>
                     ";
            }
          ?>
            <li>
                <a href ='#' id="visited"><i class="fas fa-chart-line"></i> Raporty</a>
            </li>
            <li>
                <a href ='../invoice/invoice.php'><i class="fas fa-file-invoice-dollar"></i> Faktury</a>
            </li>
            <li>
              <a href ='#'><i class='fas fa-cog'></i> Ustawienia</a>
              <ul>
                <?php
                  $query = "
                         SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 1)
                  ";
                  $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
                  $hasAccess = pg_fetch_assoc($hasAccessQuery);
                  if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                      echo "
                                <li><a href ='../admin/user/user.php'><i class='fas fa-users-cog'></i> Użytkownicy</a></li>
                           ";
                  }
      	       ?>
               <?php
                 $query = "
                        SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 5)
                 ";
                 $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
                 $hasAccess = pg_fetch_assoc($hasAccessQuery);
                 if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                     echo "
                               <li><a href ='../admin/item/item.php'><i class='fas fa-cubes'></i> Towary</a></li>
                          ";
                 }
              ?>
              <?php
                $query = "
                       SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 7)
                ";
                $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
                $hasAccess = pg_fetch_assoc($hasAccessQuery);
                if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                    echo "
                              <li><a href ='../admin/client/client.php'><i class='fas fa-user-tie'></i> Klienci</a></li>
                         ";
                }
                ?>
              </ul>
            </li>
    			</ol>
    </div>
    <section class = "section">
        W tej sekcji możesz sporządzić różne raporty i podsumowania. Wybierz interesujący Cię raport z listy.
        <br><br>
        <table>
          <tr>
            <td>
                <button class="btn btn-info" id="summary_by_region_show">Podsumowanie per region</button>
            </td>
            <td>
              <button class="btn btn-info" id="summary_by_salesman_show">Podsumowanie per sprzedawca</button>
            </td>
            <td>
              <button class="btn btn-info" id="summary_by_client_show">Podsumowanie per kontrahent</button>
            </td>
            <td>
                <button class="btn btn-info" id="summary_by_item_show">Podsumowanie per towar</button>
            </td>
            <td>
                <button class="btn btn-info" id="invoice_summary_report_show">Raport faktur</button>
            </td>
            <td>
                <button class="btn btn-info" id="error_summary_report_show">Błędne faktury</button>
            </td>
          </tr>

        </table>
        <hr>

        <div id ="report_div"></div>
        <br><br>
        <hr>
        <div id ="chart_div"></div>
      </section>
    <div>

    </div>
    <div class="footer">
        © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
    </div>
</div>

</body>
</html>
