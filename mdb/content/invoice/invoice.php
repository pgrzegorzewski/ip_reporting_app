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
                              <li><a href ='../invoice_import/invoice_import.php'><i class='far fa-file-alt'></i> Pojedyncza faktura</a></li>
                              <li><a href ='../invoice_import_improved/invoice_import_improved.php'><i class='far fa-file-alt'></i> Wiele faktur</a></li>
                              </ul>
                          </li>
                     ";
            }
          ?>
            <li>
                <a href ='../reports/reports.php'><i class="fas fa-chart-line"></i> Raporty</a>
            </li>
            <li>
                <a href ='#' id="visited"><i class="fas fa-file-invoice-dollar"></i> Faktury</a>
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

        <div class="modal fade" id="editInvoiceItemModal" tabindex="-1" role="dialog" aria-labelledby="editInvoiceItemModalLablel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInvoiceLabel">Edycja </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                          <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "invoiceNumberEdit" name = "invoiceNumberEdit" type="text" value = " ">
                                  <label for = "invoiceNumberEdit">Numer Faktury</label>
                                  <span id="invoiceNumberError" style="font-weight:bold; color:red"></span>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "invoiceDateEdit" name = "invoiceDateEdit" type="date" >
                                  <label for = "invoiceDateEdit" class="active">Data</label>
                                  <span id="invoiceDateError" style="font-weight:bold; color:red"></span>
                              </div>
                          </div>
                          <div class="col-md-3">
                            <div class="md-form form-group">
                              <select class="form-control" id = "salesmanEdit" name = "group" >
                                  <option selected>Sprzedawca</option>
                              </select>
                              <span id="salesmanError" style="font-weight:bold; color:red"></span>
                            </div>
                          </div>
                          <div class="col-md-2">
                              <div class="md-form form-group">
                                  <select  id = "currencyEdit" class="form-control" single>
                                      <option selected>Waluta</option>
                                      <option value="1">PLN</option>
                                      <option value="2">EUR</option>
                                      <option value="3">USD</option>
                                  </select>
                                  <span id="currencyError" style="font-weight:bold; color:red"></span>
                              </div>
                          </div>
                          <div class="col-md-1">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "rateEdit" name = "rate" type="number" min=0 step=0.0001>
                                  <label for = "rateEdit" class="active">Kurs</label>
                              </div>
                              <span id="rateError" style="font-weight:bold; color:red"></span>
                          </div>
                          <div class="col-md-2 my-auto" style="text-align: center;">
                            <div class="custom-control custom-checkbox custom-control-inline">
                              <input type="checkbox" class="custom-control-input" id="exportCheckboxEdit" name="exportCheckboxEdit" mdbInput>
                              <label class="custom-control-label" for="exportCheckboxEdit">Eksport</label>
                            </div>
                          </div>
                          <div class="col-md-2 my-auto" style="text-align: center;">
                            <div class="custom-control custom-checkbox custom-control-inline" >
                              <input type="checkbox" class="custom-control-input" id="transferCheckboxEdit" name="transferCheckboxEdit" mdbInput>
                              <label class="custom-control-label" for="transferCheckboxEdit">Przelew</label>
                            </div>
                          </div>
                          <div class="col-md-2 my-auto" style="text-align: center;">
                            <div class="custom-control custom-checkbox custom-control-inline" >
                              <input type="checkbox" class="custom-control-input" id="deliveryCheckboxEdit" name="deliveryCheckboxEdit" mdbInput>
                              <label class="custom-control-label" for="deliveryCheckboxEdit">Dostawa</label>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="md-form form-group">
                              <select class="form-control" id = "clientEdit" name = "clientEdit" >
                                  <option selected>Kontrahent</option>
                              </select>
                              <span id="clientError" style="font-weight:bold; color:red"></span>
                            </div>
                          </div>
                          <div class="col-md-1">
                              <div class="md-form form-group">
                                <input class="form-control" id = "bonus" name = "bonus" type="number" value="0"  min=0 step=0.01>
                                <label for = "bonus" class= 'active'>% wpr</label>
                              </div>
                          </div>
                          <div class="col-md-1">
                              <div class="md-form form-group">
                                <input class="form-control" id = "bonusEdit" name = "bonusEdit" type="number" value="0" disabled>
                                <label for = "bonusEdit" class= 'active'>% akt</label>
                              </div>
                          </div>
                          <div class="col-md-4">
                            <div class="md-form form-group">
                              <select class="form-control" id = "countryEdit" name = "countryEdit" >
                                  <option selected>Kraj</option>
                              </select>
                              <span id="countryError" style="font-weight:bold; color:red"></span>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="md-form form-group">
                              <select class="form-control" id = "voivodeshipEdit" name = "voivodeshipEdit" >
                                  <option selected>Województwo</option>
                              </select>
                              <span id="voivodeshipError" style="font-weight:bold; color:red"></span>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="md-form form-group">
                              <select class="form-control" id = "regionEdit" name = "regionEdit" >
                                  <option selected>Region</option>
                              </select>
                              <span id="regionError" style="font-weight:bold; color:red"></span>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="md-form form-group">
                              <input class="form-control" id = "noteEdit" name = "noteEdit" type="text" value = " ">
                              <label for = "noteEdit">Uwagi</label>
                            </div>
                          </div>
                          <div class="col-md-2 my-auto">
                            <div class="custom-control custom-checkbox checkbox-danger custom-control-inline" >
                              <input type="checkbox" class="custom-control-input" id="headerActiveEdit" name="headerActiveEdit" mdbInput>
                              <label class="custom-control-label label-danger" for="headerActiveEdit" style="color:red">Usuń fakturę</label>
                              <br><span id="activeHeaderEditSpan" style ="color:red"> </span>
                            </div>
                          </div>
                          <div class="col-md-3 my-auto">
                            <div class="custom-control custom-checkbox checkbox-success custom-control-inline" >
                              <input type="checkbox" class="custom-control-input" id="invoicePricesEdit" name="invoicePricesEdit" mdbInput>
                              <label class="custom-control-label label-success" for="invoicePricesEdit" style="color:green">Przelicz wartości wszystkich pozycji</label>
                            </div>
                          </div>
                          <div class="col-md-3 my-auto">
                            <div class="md-form form-group">
                              <div class="md-form form-group">
                                <input type="button" class="btn btn-info" id="invoiceHeaderEditButton" value="Zaktualizuj nagłówek"></button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <span id="invoiceHeaderUpdateResult" style="font-weight:bold; color:white"></span>
                        <hr>
                        <button class="btn btn-success" id="invoiceItemEditButton" disabled="true" style="width:220px"><i class="far fa-edit"></i> Edytuj pozycję</button>
                        <button class="btn btn-info" id="invoiceItemAddButton" style="width:220px"><i class="far fa-plus-square"></i> Dodaj pozycję</button>
                        <div id="editInvoiceItemForm">
                          <form action = "invoice_actions.php" onSubmit="return checkEditItemForm()" method="post" id="editInvoiceForm">
                            <div class="row">
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                  <select class="form-control" id = "itemEdit" name = "item" >
                                      <option selected>Towar</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "amountEdit" name = "amount" type="number" value="0">
                                    <label for="amountEdit">Ilość</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "unitEdit" name = "unit" type="text" value = "szt" >
                                    <label for="unitEdit">Jednostka</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "priceEdit" name = "price" type="number" step="0.01" value="0">
                                    <label for="priceEdit">Cena</label>
                                </div>
                              </div>
                            </div>
                            <button id="getPriceZeroButtonItemEdit" class="btn btn-info" type="button" style="width:220px"><i class="fas fa-redo"></i>  Pobierz cenę zero</button>
                            <button id="recalculatePricesButtonItemEdit" class="btn btn-info" type="button" style="width:220px"><i class="fas fa-redo"></i>  Przelicz wartości</button>
                            <div class="row">
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "priceZeroEdit" name = "priceZero" type="number" step="0.01" value="0">
                                    <label for="priceZeroEdit">Cena zero</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "valueEdit" name = "value" type="number" step="0.01" value="0">
                                    <label for="valueEdit">Wartość</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "marginEdit" name = "margin" type="number" step="0.01" value="0">
                                    <label for="marginEdit">Marża</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "percentEdit" name = "percent" type="number" step="0.000001" value="0">
                                    <label for="percentEdit">Procent</label>
                                </div>
                              </div>
                              <div class="col-md-9">
                              </div>
                              <div class="col-md-3">
                                <div class="custom-control custom-checkbox checkbox-danger custom-control-inline" >
                                  <input type="checkbox" class="custom-control-input" id="itemActiveEdit" name="itemActive" mdbInput>
                                  <label class="custom-control-label label-danger" for="itemActiveEdit" style="color:red">Usuń pozycję faktury</label>
                                  <br><span id="activeHeaderEditSpan" style ="color:red"> </span>
                                </div>
                              </div>
                            </div>
                            <div class="md-form form-group">
                                <input class="form-control" id = "action_edit_item" name = "action" type="text" value ="updateInvoiceItem" hidden>
                            </div>
                            <div class="md-form form-group">
                                <input class="form-control" id = "invoiceItemId" name = "invoiceItemId" type="text" value ="" hidden>
                            </div>

                            <div class="modal-footer">
                              <input class="btn btn-info" type = "submit" id="itemUpdateButton" value ="Zaktualizuj pozycję" />
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                            </div>
                          </form>
                        </div>
                        <div id="addInvoiceItemForm" style="display:none">
                          <form action = "invoice_actions.php"  onSubmit="return checkAddItemForm()" method="post" id="addInvoiceForm">
                            <div class="row">
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                  <select class="form-control" id = "itemAdd" name = "item" >
                                      <option selected>Towar</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "amountAdd" name = "amount" type="number" value="0">
                                    <label for="amountAdd">Ilość</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "unitAdd" name = "unit" type="text" value = "szt" >
                                    <label for="unitAdd">Jednostka</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "priceAdd" name = "price" type="number" step="0.01" value="0">
                                    <label for="priceAdd">Cena</label>
                                </div>
                              </div>
                            </div>
                            <button id="getPriceZeroButtonItemAdd" class="btn btn-info" type="button" style="width:220px"><i class="fas fa-redo"></i>  Pobierz cenę zero</button>
                            <button id="recalculatePricesButtonItemAdd" class="btn btn-info" type="button" style="width:220px"><i class="fas fa-redo"></i>  Przelicz wartości</button>
                            <div class="row">
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                  <input class="form-control" id = "priceZeroAdd" name = "priceZero" type="number" step="0.01" value="0">
                                  <label for="priceZeroAdd">Cena zero</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "valueAdd" name = "value" type="number" step="0.01" value="0">
                                    <label for="valueAdd">Wartość</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "marginAdd" name = "margin" type="number" step="0.01" value="0">
                                    <label for="marginAdd">Marża</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "percentAdd" name = "percent" type="number" step="0.000001" value="0">
                                    <label for="percentAdd">Procent</label>
                                </div>
                              </div>
                              <div class="col-md-9">
                              </div>
                            </div>
                            <div class="md-form form-group">
                                <input class="form-control" id = "action_add_item" name = "action" type="text" value ="addInvoiceItem" hidden>
                            </div>
                            <div class="md-form form-group">
                                <input class="form-control" id = "relatedInvoiceItemId" name = "invoiceItemId" type="text" value ="" hidden>
                            </div>
                            <div class="modal-footer">
                              <input class="btn btn-info" type = "submit" id="itemAddButton" value ="Dodaj pozycję" />
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                            </div>
                          </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class = "section">
          <?php
          if(isset($_SESSION['e_invoice_update'])){
              echo '<div class = "error">'.$_SESSION['e_invoice_update'].'</div>';
              unset($_SESSION['e_invoice_update']);
          }
          ?>
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
                          <span>Numer faktury</span>
                          <select class="form-control" id = "invoice_number" name = "invoice_number">
                              <option selected>Numer Faktury</option>
                          </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="md-form form-group">
                            <span>Sprzedawca</span>
                            <select class="form-control" id = "salesman" name = "salesman">
                                <option selected>Sprzedawca</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                  <div class="col-md-3 my-auto" style="text-align: left;">
                    <span>Typ faktur:</span>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input export_radio" value = "1" id="export_radio_1" name="export_radios" checked>
                      <label class="custom-control-label" for="export_radio_1">Wszystkie</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input export_radio" value = "2" id="export_radio_2" name="export_radios" >
                      <label class="custom-control-label" for="export_radio_2">Krajowe</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input export_radio" value = "3" id="export_radio_3" name="export_radios">
                      <label class="custom-control-label" for="export_radio_3">Eksportowe</label>
                    </div>
                  </div>
                  <div class="col-md-3 my-auto" style="text-align: left;">
                    <span>Typ zapłaty:</span>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input pay_radio" value = "1" id="pay_radio_1" name="pay_radios" checked>
                      <label class="custom-control-label" for="pay_radio_1">Wszystkie</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input pay_radio" value = "2" id="pay_radio_2" name="pay_radios" >
                      <label class="custom-control-label" for="pay_radio_2">Gotówka</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input pay_radio" value = "3" id="pay_radio_3" name="pay_radios">
                      <label class="custom-control-label" for="pay_radio_3">Przelew</label>
                    </div>
                  </div>
                  <div class="col-md-3 my-auto" style="text-align: left;">
                    <span>Typ dostawy:</span>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input delivery_radio" value = "1" id="delivery_radio_1" name="delivery_radios" checked>
                      <label class="custom-control-label" for="delivery_radio_1">Wszystkie</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input delivery_radio" value = "2" id="delivery_radio_2" name="delivery_radios" >
                      <label class="custom-control-label" for="delivery_radio_2">Bez dostawy</label>
                    </div>
                    <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input delivery_radio" value = "3" id="delivery_radio_3" name="delivery_radios">
                      <label class="custom-control-label" for="delivery_radio_3">Z dostawą</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                      <div class="md-form form-group">
                          <span>Waluta</span>
                          <select  id = "currency" class="custom-select" single>
                              <option selected>Waluta</option>
                              <option value="1">PLN</option>
                              <option value="2">EUR</option>
                              <option value="3">USD</option>
                          </select>
                      </div>
                  </div>
                </div>
                <div class="form-row">
                  <div class="col-md-3">
                      <div class="md-form form-group">
                        <span>Kontrahent</span>
                        <select class="form-control" id = "client" name = "client">
                            <option selected>Kontrahent</option>
                        </select>
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="md-form form-group">
                        <span>Kraj</span>
                        <select class="form-control" id = "country" name = "country">
                            <option selected>Kraj</option>
                        </select>
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="md-form form-group">
                        <span>Województwo</span>
                        <select class="form-control" id = "voivodeship" name = "voivodeship">
                            <option selected>Województwo</option>
                        </select>
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="md-form form-group">
                        <span>Region</span>
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
                      <th>Bonus</th>
                      <?php
                        $query = "
                          SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 2)
                        ";
                        $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
                        $hasAccess = pg_fetch_assoc($hasAccessQuery);
                        if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                            echo "
                                      <th>Edycja</th>
                                 ";
                        } else {
                          echo "
                                    <th hidden=true>Edycja</th>
                               ";
                        }
            	        ?>
                      <th>Poz</th>
                      <th>Towar</th>
                      <th>Ilość</th>
                      <th>Jedn</th>
                      <th>Cena</th>
                      <th>Cena zero</th>
                      <th>Wartość</th>
                      <th>Marża</th>
                      <th>Proc</th>
                      <th>Uwagi</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="20" style="text-align:right">Podsumowanie:</th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                        </tr>
                    </tfoot>
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
