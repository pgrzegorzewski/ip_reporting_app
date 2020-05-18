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
    <script type="text/javascript" src="./item.js"></script>
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
                        <li><a href ='../../invoice_import/invoice_import.php'><i class='far fa-file-alt'></i> Wprowadzenie faktury</a></li>
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
                           <li><a id='visited' href ='#'><i class='fas fa-cubes'></i> Towary</a></li>
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
                          <li><a href ='../client/client.php'><i class='fas fa-user-tie'></i> Klienci</a></li>
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

        <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editItemLabel">Edycja </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action = "item_actions.php" method="post" id="update_item_form">
                          <div class="form-row">
                            <div class="col-md-6">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "item_name" name = "item_name" type="text" value=" " style="color:white">
                                    <label for = "item_name">Towar</label>
                                </div>
                            </div>
                            <div class="col-md-6 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active"  name = "is_active" checked >
                                <label class="custom-control-label" for="is_active">Aktywny</label>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="md-form form-group">
                                  <select class="form-control" id = "group_name" name = "group" >
                                      <option selected>Szereg</option>
                                  </select>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form form-group">
                                  <select class="form-control" id = "type_name" name = "type" >
                                      <option selected>Rodzaj</option>
                                  </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "price_go" name = "price_go" type="number" step="0.01" style="color:white">
                                  <label for="price_go">Cena go</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "price_po" name = "price_po" type="number" step="0.01" style="color:white">
                                  <label for="price_po" >Cena po</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "price_gd" name = "price_gd" type="number" step="0.01" style="color:white">
                                  <label for="price_gd">Cena gd</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "price_pd" name = "price_pd" type="number" step="0.01" style="color:white">
                                  <label for="price_pd">Cena pd</label>
                              </div>
                            </div>
                          </div>
                          <div class="md-form form-group">
                              <input class="form-control" id = "action" name = "action" type="text" value="updateItem" hidden>
                          </div>
                          <div class="md-form form-group">
                              <input class="form-control" id = "itemId" name = "itemId" type="text" value ="" hidden>
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

        <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemLabel">Edycja </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action = "item_actions.php" method="post" id="add_item_form">
                          <div class="form-row">
                            <div class="col-md-6">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "item_name_new" name = "item_name_new" type="text" value="" style="color:white">
                                    <label for = "item_name_new">Towar</label>
                                </div>
                            </div>
                            <div class="col-md-6 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active_new"  name = "is_active_new" checked >
                                <label class="custom-control-label" for="is_active_new">Aktywny</label>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="md-form form-group">
                                  <select class="form-control" id = "group_name_new" name = "group_new" >
                                      <option selected>Szereg</option>
                                  </select>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form form-group">
                                  <select class="form-control" id = "type_name_new" name = "type_new" >
                                      <option selected>Rodzaj</option>
                                  </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "price_go_new" name = "price_go_new" type="number" value="0" step="0.01" style="color:white">
                                  <label for="price_go_new">Cena go</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "price_po_new" name = "price_po_new" type="number" value="0" step="0.01" style="color:white">
                                  <label for="price_po_new" >Cena po</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "price_gd_new" name = "price_gd_new" type="number" value="0" step="0.01" style="color:white">
                                  <label for="price_gd_new">Cena gd</label>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "price_pd_new" name = "price_pd_new" type="number" value="0" step="0.01" style="color:white">
                                  <label for="price_pd_new">Cena pd</label>
                              </div>
                            </div>
                          </div>
                          <div class="md-form form-group">
                              <input class="form-control" id = "action_add_item" name = "action" type="text" value="addItem" hidden>
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
      <?php
      if(isset($_SESSION['e_item_update'])){
          echo '<div class = "error">'.$_SESSION['e_item_update'].'</div>';
          unset($_SESSION['e_item_update']);
      }
      ?>
      <div style="text-align:right">
        <?php
          $query = "
                 SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 6)
          ";
          $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
          $hasAccess = pg_fetch_assoc($hasAccessQuery);
          if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
              echo "
                        <button class= 'btn btn-success' data-toggle='modal' data-target='#addItemModal'>Dodaj nowy towar</button>
                   ";
          }
       ?>

      </div>
      <div id= "item_management" class="table-responsive">
          <table class="table table-striped table-bordered" id="data-table" style="width:100%">
              <thead>
              <tr>
                  <th>ID towaru</th>
                  <th>Nazwa</th>
                  <th>Aktywny</th>
                  <th>Szereg</th>
                  <th>Rodzaj</th>
                  <th>Cena go</th>
                  <th>Cena po</th>
                  <th>Cena gd</th>
                  <th>Cena pd</th>
                  <th>Edytuj</th>
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
