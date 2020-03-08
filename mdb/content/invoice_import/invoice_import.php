<?php
session_start();
if(isset($_SESSION['user']) != true && isset($_SESSION['is_logged']) != true)
{
    header('Location:index.php');
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
    <script type="text/javascript" src="invoice_import.js"></script>
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
                        <span>Zalogowany jako: <?php echo $_SESSION['user'] ?>&ensp;</span><a href = "../logout.php"><button class="btn btn-danger" value="">Wyloguj</button></a>
                        </button>
                    </td>
                </tr>
            </table>
        </header>
        <div class="nav">
            <ol>
                <li>
                    <a  href = '#' id="visited">Wprowadzenie faktury</a>
                </li>
                <li>
                    <a href ='../reports.php'>Raporty i podsumowania</a>
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
            <form id="invoice_header_form">
                <br />
                <h5>Nagłówek faktury</h5>
                <div class="form-row">
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <input class="form-control" id = "invoice_number" name = "invoice_number" type="text">
                            <label for = "invoice_number">Numer faktury</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <input class="form-control" id = "invoice_date" name = "invoice_date" type="date">
                            <label for = "invoice_date">Data faktury</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <input class="form-control" id = "salesman" name = "salesman" type="text">
                            <label for = "salesman">Sprzedawca</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="md-form form-group" id = "rate">
                            <select class="custom-select" single>
                                <option selected>Waluta</option>
                                <option value="1">PLN</option>
                                <option value="2">EUR</option>
                                <option value="3">USD</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="md-form form-group">
                            <input class="form-control" id = "rate" name = "rate" type="number">
                            <label for = "rate">Kurs</label>
                        </div>

                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">

                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="radioType1" name="inlineDefaultRadiosExample" mdbInput>
                            <label class="custom-control-label" for="radioType1">Eksport</label>
                        </div>

                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="radioType2" name="inlineDefaultRadiosExample" mdbInput>
                            <label class="custom-control-label" for="radioType2">Przelew</label>
                        </div>

                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="radioType3" name="inlineDefaultRadiosExample" mdbInput>
                            <label class="custom-control-label" for="radioType3">Dostawa</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="md-form form-group">
                            <input class="form-control" id = "client" name = "client" type="text">
                            <label for = "client">Kontrahent</label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="md-form form-group">
                            <select class="form-control" id = "country" name = "country" id = "country">
                                <option selected>Kraj</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="md-form form-group">
                            <select class="form-control" id = "voivodeship" name = "voivodeship" id = "voivodeship">
                                <option selected>Województwo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="md-form form-group">
                            <select class="form-control" id = "region" name = "region" id = "region">
                                <option selected>Region</option>
                            </select>

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="md-form form-group">
                            <textarea type="text" id="comment" class="md-textarea form-control" mdbInput></textarea>
                            <label for="form7">Uwagi</label>
                        </div>
                    </div>
                </div>
            </form>
            <br/ >
            <form id ="upload_csv" method="post" enctype="multipart/form-data">
                <div class="form-row" style ="text-align: center">
                    <div class="col-md-12">
                        <div class="input-group" style="vertical-align: middle">
                            <div class="input-group-prepend" >
                                <input type ="submit" name="upload" id="upload" value="Importuj" class = "btn btn-success" style="padding:7px; margin:0px; width:120px"/>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="csv_file" name="csv_file"
                                       aria-describedby="fileBrowser" acccept=".csv"  style="margin:6px">
                                <label class="custom-file-label" for="csv_file">Wybierz plik</label>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
            <br />
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="data-table">
                    <thead>
                    <tr>
                        <th>lp</th>
                        <th>cena zero</th>
                        <th>towar</th>
                        <th>nazwa</th>
                        <th>ilosc</th>
                        <th>jm</th>
                        <th>cena</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </section>
        <div class="footer">
            © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
        </div>
    </div>
</body>
</html>

<script>

$(document).ready(function(){
   $('#upload_csv').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: "../import_csv.php",
            method: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (jsonData) {
                $('#csv_file').val('');
                $('#data-table').DataTable({
                    data : jsonData,
                    columns: [
                        {data: 'lp'},
                        {data: 'cena zero'},
                        {data: 'towar'},
                        {data: 'nazwa'},
                        {data: 'ilosc'},
                        {data: 'jm'},
                        {data: 'cena'}
                    ]
                });
            }
        })
   })
});

</script>
