<?php
    session_start();
    if(isset($_SESSION['user']) != true && isset($_SESSION['is_logged']) != true)
    {
        header('Location:../../../index.php');
        exit();
    }
    require './../../core/connect.php';

?>

<html lang = "pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Instalplast rozliczenia</title>

    <link rel="icon" href="../../../resources/ip_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="../../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../css/mdb.min.css">
    <link rel="stylesheet" href="../../../css/style.css">
    <link rel="StyleSheet" href="../../../scss/core/app/home.css"/>

    <script type="text/javascript" src="../../../js/jquery.min.js" ></script>
    <script type="text/javascript" src="../../../js/popper.min.js"></script>
    <script type="text/javascript" src="../../../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../../js/addons/datatables.min.js" ></script>
    <script type="text/javascript" src="../../../js/mdb.min.js"></script>
    <script type="text/javascript" src="./client.js"></script>
    <script type="text/javascript"></script>
	   <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">

</head>

<body>

<div class="container-fluid">

  <header class ="header">
      <table width = 100%>
          <tr>
              <td style = "text-align:left">
                  <h1 id="title"><a href ="../../../index.php"><img src="../../../resources/instalplast.png" style="width:300px;"></h1>
              </td>
              <td style = "text-align:right">
                  <span id="user_span">Zalogowany jako: <?php echo $_SESSION['user'] ?>&ensp;</span><a href = "../../core/logout.php"><button class="btn btn-danger" value="">Wyloguj</button></a>
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
                        <li><a href ='../../invoice_import/invoice_import.php'><i class='far fa-file-alt'></i> Pojedyncza faktura</a></li>
                        <li><a href ='../../invoice_import_improved/invoice_import_improved.php'><i class='far fa-file-alt'></i> Wiele faktur</a></li>
                        </ul>
                    </li>
                   ";
          }
        ?>
				<li>
					<a href ='../../reports/reports.php'><i class="fas fa-chart-line"></i> Raporty</a>
				</li>
				<li>
					<a href ='../../invoice/invoice.php'><i class="fas fa-file-invoice-dollar"></i> Faktury</a>
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
                            <li><a href ='../user/user.php'><i class='fas fa-users-cog'></i> Użytkownicy</a></li>
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
                           <li><a href ='../item/item.php'><i class='fas fa-cubes'></i> Towary</a></li>
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
                          <li><a id='visited' href ='#'><i class='fas fa-user-tie'></i> Klienci</a></li>
                     ";
            }
            ?>
          </ul>
        </li>
			</ol>
		</div>

        <?php
        if(isset($_SESSION['e_password'])){
            echo '<div class = "error">'.$_SESSION['e_password'].'</div>';
            unset($_SESSION['e_password']);
        }
        ?>

        <div class="modal fade" id="editClientModal" tabindex="-1" role="dialog" aria-labelledby="editClientLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editClientLabel">Edycja </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action = "client_actions.php" method="post" id="update_client_form">
                          <div class="form-row">
                            <div class="col-md-6">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "client_name" name = "client_name" type="text" value=" " style="color:white">
                                    <label for = "client_name">Klient</label>
                                </div>
                            </div>
                            <div class="col-md-3 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active"  name = "is_active" value= "" checked >
                                <label class="custom-control-label" for="is_active">Aktywny</label>
                              </div>
                            </div>
                            <div class="col-md-3 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="black_list"  name = "black_list" value= "" checked >
                                <label class="custom-control-label" for="black_list">Czarna lista</label>
                              </div>
                            </div>

                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "street" name = "street" type="text" value=" " style="color:white">
                                  <label for = "street">Ulica</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "address_2" name = "address_2" type="text" value=" " style="color:white">
                                  <label for = "address_2">Nr domu</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "post_code" name = "post_code" type="text" value=" " style="color:white">
                                  <label for = "post_code">Kod pocztowy</label>
                              </div>
                            </div>

                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "city" name = "city" type="text" value=" " style="color:white">
                                  <label for = "city">Miasto</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                <select class="form-control" id = "voivodeship" name = "voivodeship" >
                                    <option selected>Województwo</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                <select class="form-control" id = "country" name = "country" >
                                    <option selected>Kraj</option>
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
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "bonus" name = "bonus" type="number" step="0.01" min="0" max = "100" style="color:white">
                                  <label for = "bonus">Bonus</label>
                              </div>
                            </div>

                          </div>
                          <hr>
                          <span>Domyślne wartości</span>
                          <div class="form-row">
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                <select class="form-control" id = "salesman" name = "salesman" >
                                    <option selected>Sprzedawca</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-2">
                                <div class="md-form form-group">
                                    <select  name="currency" id = "currency" class="form-control" single>
                                        <option selected>Waluta</option>
                                        <option value="1">PLN</option>
                                        <option value="2">EUR</option>
                                        <option value="3">USD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="exportCheckbox" name="exportCheckbox" mdbInput>
                                <label class="custom-control-label" for="exportCheckbox">Eksport</label>
                              </div>
                            </div>
                            <div class="col-md-2 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox custom-control-inline" >
                                <input type="checkbox" class="custom-control-input" id="transferCheckbox" name="transferCheckbox" mdbInput>
                                <label class="custom-control-label" for="transferCheckbox">Przelew</label>
                              </div>
                            </div>
                            <div class="col-md-2 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox custom-control-inline" >
                                <input type="checkbox" class="custom-control-input" id="deliveryCheckbox" name="deliveryCheckbox" mdbInput>
                                <label class="custom-control-label" for="deliveryCheckbox">Dostawa</label>
                              </div>
                            </div>
                          </div>
                          <div class="md-form form-group">
                              <input class="form-control" id = "action" name = "action" type="text" value="updateClient" hidden>
                          </div>
                          <div class="md-form form-group">
                              <input class="form-control" id = "clientId" name = "clientId" type="text" value ="" hidden>
                          </div>
                          <div class="modal-footer">
                              <input class="btn btn-info" type = "submit" value ="Zapisz zmiany" />
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClientLabel">Nowy klient </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action = "client_actions.php" method="post" id="add_client_form">
                          <div class="form-row">
                            <div class="col-md-6">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "client_name_new" name = "client_name_new" type="text" value="" style="color:white">
                                    <label for = "client_name_new">Klient</label>
                                </div>
                            </div>
                            <div class="col-md-3 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active_new"  name = "is_active_new" value= "" checked >
                                <label class="custom-control-label" for="is_active_new">Aktywny</label>
                              </div>
                            </div>
                            <div class="col-md-3 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="black_list_new"  name = "black_list_new" value= "" >
                                <label class="custom-control-label" for="black_list_new">Czarna lista</label>
                              </div>
                            </div>

                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "street_new" name = "street_new" type="text" value="" style="color:white">
                                  <label for = "street_new">Ulica</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "address_2_new" name = "address_2_new" type="text" value="" style="color:white">
                                  <label for = "address_2_new">Nr domu</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "post_code_new" name = "post_code_new" type="text" value="" style="color:white">
                                  <label for = "post_code_new">Kod pocztowy</label>
                              </div>
                            </div>

                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "city_new" name = "city_new" type="text" value="" style="color:white">
                                  <label for = "city_new">Miasto</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                <select class="form-control" id = "voivodeship_new" name = "voivodeship_new" >
                                    <option selected>Województwo</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                <select class="form-control" id = "country_new" name = "country_new" >
                                    <option selected>Kraj</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                <select class="form-control" id = "region_new" name = "region_new" >
                                    <option selected>Region</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <hr>
                          <span>Domyślne wartości</span>
                          <div class="form-row">
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                <select class="form-control" id = "salesman_new" name = "salesman_new" >
                                    <option selected>Sprzedawca</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-2">
                                <div class="md-form form-group">
                                    <select  name="currency_new" id = "currency_new" class="form-control" single>
                                        <option selected>Waluta</option>
                                        <option value="1">PLN</option>
                                        <option value="2">EUR</option>
                                        <option value="3">USD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="export_checkbox_new" name="export_checkbox_new" mdbInput>
                                <label class="custom-control-label" for="export_checkbox_new">Eksport</label>
                              </div>
                            </div>
                            <div class="col-md-2 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox custom-control-inline" >
                                <input type="checkbox" class="custom-control-input" id="transfer_checkbox_new" name="transfer_checkbox_new" mdbInput>
                                <label class="custom-control-label" for="transfer_checkbox_new">Przelew</label>
                              </div>
                            </div>
                            <div class="col-md-2 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox custom-control-inline" >
                                <input type="checkbox" class="custom-control-input" id="delivery_checkbox_new" name="delivery_checkbox_new" mdbInput>
                                <label class="custom-control-label" for="delivery_checkbox_new">Dostawa</label>
                              </div>
                            </div>
                          </div>
                          <div class="md-form form-group">
                              <input class="form-control" id = "action_add_client" name = "action" type="text" value="addClient" hidden>
                          </div>
                          <div class="modal-footer">
                              <input class="btn btn-info" type = "submit" value ="Zapisz zmiany" />
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

		<section class = "section">
      <div style="text-align:right">
        <?php
          $query = "
                 SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 8)
          ";
          $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
          $hasAccess = pg_fetch_assoc($hasAccessQuery);
          if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
              echo "
                          <button class= 'btn btn-success' data-toggle='modal' data-target='#addClientModal'>Dodaj nowego klienta</button>
                   ";
          }
       ?>


      </div>
      <?php
      if(isset($_SESSION['e_client_update'])){
          echo '<div class = "error">'.$_SESSION['e_client_update'].'</div>';
          unset($_SESSION['e_client_update']);
      }
      ?>
      <div id= "client_management" class="table-responsive">
          <table class="table table-striped table-bordered" id="data-table" style="width:100%">
              <thead>
              <tr>
                  <th>ID Kontrahenta</th>
                  <th>Nazwa</th>
                  <th>Ulica</th>
                  <th>Nr domu</th>
                  <th>Kod pocztowy</th>
                  <th>Miasto</th>
                  <th>Województwo</th>
                  <th>Region</th>
                  <th>Kraj</th>
                  <th>Aktywny</th>
                  <th>Czarna lista</th>
                  <th>Bonus</th>
                  <th>Przelew</th>
                  <th>Dostawa</th>
                  <th>Eksport</th>
                  <th>Waluta</th>
                  <th>Sprzedawca</th>
                  <th>Edycja</th>
              </tr>
              </thead>
              <tbody></tbody>
          </table>
      </div>
    </section>
		<div class="footer">
		© 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
		</div>
	</div>

</body>
</html>
