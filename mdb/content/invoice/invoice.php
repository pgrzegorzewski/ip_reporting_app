<?php
session_start();
if(isset($_SESSION['user']) != true && isset($_SESSION['is_logged']) != true)
{
    header('Location:../../index.php');
    exit();
}
require '../core/connect.php';
include '../class/class_user.php';

$loggedUser = new User();

?>

<!DOCTYPE html>
<html lang = "pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Instalplast księgowość</title>

    <link rel="icon" href="../../resources/ip_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/mdb.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="StyleSheet" href="../../scss/core/app/home.css"/>

    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <script type="text/javascript" src="../../js/popper.min.js"></script>
    <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../js/addons/datatables.min.js" ></script>
    <script type="text/javascript" src="../../js/mdb.min.js"></script>
    <script type="text/javascript" src="./invoice.js"></script>
    <script type="text/javascript"></script>
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
                        </button>
                    </td>
                </tr>
            </table>
        </header>
        <div class="nav">
            <ol>
                <li>
                    <a  href = '../invoice_import/invoice_import.php'><i class="far fa-file-alt"></i> Wprowadzenie faktury</a>
                </li>
                <li>
                    <a href ='../reports/reports.php'><i class="fas fa-chart-line"></i> Raporty</a>
                </li>
                <li>
                    <a href ='#' id="visited"><i class="fas fa-file-invoice-dollar"></i> Faktury</a>
                </li>
                <?php
                $query = "
                       SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 1)
                ";
                $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
                $hasAccess = pg_fetch_assoc($hasAccessQuery);
                if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                    echo "<li>
                            <a href ='#'><i class='fas fa-cog'></i> Ustawienia</a>
                            <ul>
                              <li><a href ='../admin/user/user.php'><i class='fas fa-users-cog'></i> Użytkownicy</a></li>
                              <li><a href ='../admin/item/item.php'><i class='fas fa-cubes'></i> Towary</a></li>
                              <li><a href ='../admin/client/client.php'><i class='fas fa-user-tie'></i> Klienci</a></li>
                            </ul>
                          </li>";
                }
                ?>
            </ol>
        </div>
        <section class = "section">
            <form id="invoice_header_form">
                <div class="form-row">
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <input class="form-control" id = "report_date_from" name = "report_date_from" type="date">
                            <label class="active" for = "report_date_from">Data początkowa</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <input class="form-control" id = "report_date_to" name = "report_date_to" type="date">
                            <label class="active" for = "report_date_to">Data końcowa</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                          <select class="form-control" id = "invoice_number" name = "invoice_number">
                              <option selected>Numer Faktury</option>
                          </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <select class="form-control" id = "salesman" name = "salesman">
                                <option selected>Sprzedawca</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <select class="form-control" id = "country" name = "country">
                                <option selected>Kraj</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <select class="form-control" id = "client" name = "client">
                                <option selected>Kontrahent</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <select class="form-control" id = "voivodeship" name = "voivodeship">
                                <option selected>Województwo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <select class="form-control" id = "region" name = "region" >
                                <option selected>Region</option>
                            </select>

                        </div>
                    </div>
                </div>
            </form>
            <div style="text-align:center">
              <button id ='showInvoiceInfo' class='btn btn-info'><span id="loadButtonSpan">Załaduj</span></button>
            </div>
            <div class="table-editable" id="editable-table-div">
                <table class="table table-bordered table-responsive-md table-striped text-center" id="data-table">
                    <thead>
                    <tr>
                      <th>Nr faktury</th>
                      <th>Data</th>
                      <th>Sprzedawca</th>
                      <th>Waluta</th>
                      <th>Kurs</th>
                      <th>Eksp</th>
                      <th>Dst</th>
                      <th>P</th>
                      <th>Kontrahent</th>
                      <th>Kraj</th>
                      <th>Woj</th>
                      <th>Reg</th>
                      <th>Poz</th>
                      <th>Towar</th>
                      <th>Ilość</th>
                      <th>Jedn</th>
                      <th>Cena</th>
                      <th>Cena zero</th>
                      <th>Wartość</th>
                      <th>Marża</th>
                      <th>Proc</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <?php
            if(isset($_SESSION['e_invoice'])){
                echo '<div class = "error">'.$_SESSION['e_invoice'].'</div>';
                unset($_SESSION['e_invoice']);
            }
            ?>
        </section>
        <div class="footer">
            © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
        </div>
    </div>
</body>
</html>
