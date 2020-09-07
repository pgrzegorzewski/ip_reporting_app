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
    <title>Instalplast rozliczenia</title>

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
    <script type="text/javascript" src="./invoice_import_improved.js"></script>
    <script type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
      <div class="alert alert-danger alert-dismissible fade show" id="invoice_add_error" role="alert" hidden="true">
        <strong>Nie można wprowadzić faktury <br>Popraw:<br></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="alert alert-success alert-dismissible fade show" id="invoice_add_success" role="alert" hidden="true">
        <strong>Pomyślnie dodano wszystkie faktury, formularz zostanie odświeżone za 3 sekundy</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="alert alert-success alert-dismissible fade show" id="invoices_already_imported" role="alert" hidden="true">
        <strong>Wszystkie faktury z pliku są już dostępne w systemie.</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
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
              <?php
                $query = "
                       SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 2)
                ";
                $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
                $hasAccess = pg_fetch_assoc($hasAccessQuery);
                if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                    echo "
                            <li>
                              <a href='#'><i class='far fa-file-alt'></i> Wprowadzenie faktury</a>
                              <ul>
                                <li><a href ='../invoice_import/invoice_import.php'><i class='far fa-file-alt'></i> Wprowadzenie faktury</a></li>
                                <li><a href ='#' id ='visited' ><i class='far fa-file-alt'></i> Szybki import</a></li>
                                </ul>
                            </li>
                       ";
                }
              ?>
                <li>
                    <a href ='../reports/reports.php'><i class="fas fa-chart-line"></i> Raporty</a>
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
          <form id ="upload_csv" method="post" enctype="multipart/form-data">
              <div class="form-row" style ="text-align: center">
                  <div class="col-md-12">
                      <div class="input-group" style="vertical-align: middle">
                          <div class="input-group-prepend" >
                              <input type ="submit" name="upload" id="upload" value="Importuj" class = "btn btn-success" style="padding:7px; margin:0px; width:120px"/>
                          </div>
                          <div class="custom-file">
                              <input type="file" class="custom-file-input" id="csv_file" name="csv_file"
                                     aria-describedby="fileBrowser"  accept=".csv" style="margin:6px">
                              <label class="custom-file-label" for="csv_file"><span id="import_label">Wybierz plik</span></label>
                          </div>
                      </div>

                  </div>
              </div>
          </form>
          <br />
          <div align="center">
            <div class="spinner-border loading" role="status" style="display:none;">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
          <div id = "import_invoice_numbers" style="display:none">
            <br>
          </div>
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
                          <select class="form-control" id = "salesman" name = "salesman">
                              <option selected>Sprzedawca</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-2">
                      <div class="md-form form-group">
                          <select  id = "currency" class="custom-select" single>
                              <option selected>Waluta</option>
                              <option value="1">PLN</option>
                              <option value="2">EUR</option>
                              <option value="3">USD</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-1">
                      <div class="md-form form-group">
                          <input class="form-control" id = "rate" name = "rate" type="number" step="0.0001" min = 0>
                          <label for = "rate">Kurs</label>
                      </div>

                  </div>
              </div>
              <div class="form-row">
                  <div class="col-md-2 my-auto" style="text-align: center;">
                    <div class="custom-control custom-checkbox custom-control-inline">
                      <input type="checkbox" class="custom-control-input" id="export_checkbox" name="export_checkbox" mdbInput>
                      <label class="custom-control-label" for="export_checkbox">Eksport</label>
                    </div>
                  </div>
                  <div class="col-md-2 my-auto" style="text-align: center;">
                    <div class="custom-control custom-checkbox custom-control-inline" >
                      <input type="checkbox" class="custom-control-input" id="transfer_checkbox" name="transfer_checkbox" mdbInput>
                      <label class="custom-control-label" for="transfer_checkbox">Przelew</label>
                    </div>
                  </div>
                  <div class="col-md-2 my-auto" style="text-align: center;">
                    <div class="custom-control custom-checkbox custom-control-inline" >
                      <input type="checkbox" class="custom-control-input" id="delivery_checkbox" name="delivery_checkbox" mdbInput>
                      <label class="custom-control-label" for="delivery_checkbox">Dostawa</label>
                    </div>
                  </div>
                  <div class="col-md-4">
                      <div class="md-form form-group">
                          <select class="form-control" id = "client" name = "client">
                              <option selected>Kontrahent</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-2">
                      <div class="md-form form-group">
                        <input class="form-control" id = "bonus" name = "bonus" type="number" value="0" disabled>
                        <label for = "bonus" class= 'active'>Bonus %</label>
                      </div>
                  </div>
              </div>
              <div class="form-row">
                  <div class="col-md-4">
                      <div class="md-form form-group">
                          <select class="form-control" id = "country" name = "country">
                              <option selected>Kraj</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="md-form form-group">
                          <select class="form-control" id = "voivodeship" name = "voivodeship">
                              <option selected>Województwo</option>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="md-form form-group">
                          <select class="form-control" id = "region" name = "region" >
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
          <div style="text-align:center">
            <button id="show_price_calculator" class="btn btn-info"><i class="fas fa-calculator"></i> Pokaż kalkulator cen</button>
            <button id="recalculatePricesButton" class="btn btn-info" disabled = "true"><i class="fas fa-redo"></i>  Przeładuj ceny</button>
          </div>
          <div id="price_calculator_div" style="display:none">
            <form id="price_calculator">
              <br />
              <div class="form-row">
                <div class="col-md-3">
                    <div class="md-form form-group">
                        <select class="form-control" class="item" id="item_calculator_select">
                            <option selected>Towar</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                  <div class="md-form form-group">
                    <input class="form-control" id = "calculator_amount" name = "calculator_amount" type="number" step="1" value = "1" min = "1">
                    <label for = "calculator_amount">Ilość</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="md-form form-group">
                    <input class="form-control" id = "price_go_calculator" name = "price_go_calculator" type="number" step="0.01" value = "0">
                    <label for = "price_go_calculator">Cena go</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="md-form form-group">
                    <input class="form-control" id = "price_po_calculator" name = "price_po_calculator" type="number" step="0.01" value = "0">
                    <label for = "price_po_calculator">Cena po</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="md-form form-group">
                    <input class="form-control" id = "price_gd_calculator" name = "price_gd_calculator" type="number" step="0.01" value = "0">
                    <label for = "price_gd_calculator">Cena gd</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="md-form form-group">
                    <input class="form-control" id = "price_pd_calculator" name = "price_pd_calculator" type="number" step="0.01" value = "0">
                    <label for = "price_pd_calculator">Cena pd</label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <br />
          <div class="table-editable" id="editable-table-div">
            <span class="table-add float-right mb-3 mr-2" id="invoiceItemRowAdd" ><i class="fas fa-plus fa-2x green-text"></i></span>
            <table class="table table-bordered table-responsive-md table-striped text-center" id="data-table">
                <thead>
                <tr>
                    <th>Lp</th>
                    <th>Towar</th>
                    <th>Towar (import)</th>
                    <th>Ilość</th>
                    <th>J.M.</th>
                    <th>Cena</th>
                    <th>Cena zero</th>
                    <th>Wartość</th>
                    <th>Marża</th>
                    <th>Procent</th>
                    <th>Usuń</th>
                </tr>
                </thead>
                <tbody></tbody>
                <tfoot >
                    <tr>
                        <th colspan="7">Podsumowanie:</th>
                        <th style="text-align:center"></th>
                        <th style="text-align:center"></th>
                        <th style="text-align:center"></th>
                        <th style="text-align:center"></th>
                    </tr>
                </tfoot>
            </table>
          </div>

          <div id = "import_invoice_div"></div>
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
