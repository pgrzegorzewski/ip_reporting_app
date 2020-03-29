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
    <title>Instalplast księgowość</title>

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
    <script type="text/javascript" src="./user.js"></script>
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
				<li>
					<a href ='../../invoice_import/invoice_import.php'><i class="far fa-file-alt"></i> Wprowadzenie faktury</a>
				</li>
				<li>
					<a href ='../../reports/reports.php'><i class="fas fa-chart-line"></i> Raporty</a>
				</li>
				<li>
					<a href ='../../invoice/invoice.php'><i class="fas fa-file-invoice-dollar"></i> Faktury</a>
				</li>
        <li>
          <a href ='#'><i class='fas fa-cog'></i> Ustawienia</a>
          <ul>
            <li><a href ='../user/user.php' ><i class='fas fa-users-cog'></i> Użytkownicy</a></li>
            <li><a href ='../item/item.php'><i class='fas fa-cubes'></i> Towary</a></li>
            <li><a href ='#' id='visited'><i class='fas fa-user-tie'></i> Klienci</a></li>
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

        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserLabel">Edycja </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action = "user_actions.php" method="post" id="update_user_form">
                          <div class="form-row">
                            <div class="col-md-6">
                                <div class="md-form form-group">
                                    <input class="form-control" id = "username" name = "username" type="text" value=" " style="color:white">
                                    <label for = "username">Username</label>
                                </div>
                            </div>
                            <div class="col-md-6 my-auto" style="text-align: center;">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active"  name = "is_active" value= "" checked >
                                <label class="custom-control-label" for="is_active">Aktywny</label>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "first_name" name = "first_name" type="text" value=" " style="color:white">
                                  <label for = "first_name">Imię</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "last_name" name = "last_name" type="text" value=" " style="color:white">
                                  <label for = "last_name">Nazwisko</label>
                              </div>
                            </div>
                          </div>
                          Rola: <br />
                          <div class="md-form form-group">
                              <select class="form-control" id = "role" name = "role" style="color:white">
                                  <option value="admin">Admin</option>
                                  <option value="handlowiec">handlowiec</option>
                              </select>
                          </div>
                          <div class="form-row" style="border:#AFC2D1 solid 2px; border-radius: 25px;">
                            <div class="col-md-6">
                              <div class="md-form form-group">
                                  <input class="form-control" id = "password_temporary" name = "password_temporary" type="text" value="" style="color:white">
                                  <label for = "password_temporary">Hasło tymczasowe</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form form-group">
                                <input type="button" class="btn btn-info" id="tmp_password_btn" value="Przypisz hasło"></button>
                              </div>
                            </div>
                            <div class="col-md-12" style="text-align:center">
                              <span id = "assign_temporary_pwd_error"></span>
                              <span id = "assign_temporary_pwd_success"></span>
                            </div>
                          </div>
                          <div class="md-form form-group">
                              <input class="form-control" id = "action" name = "action" type="text" value="updateUser" hidden>
                          </div>
                          <div class="md-form form-group">
                              <input class="form-control" id = "userId" name = "userId" type="text" value ="" hidden>
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
      if(isset($_SESSION['e_user_update'])){
          echo '<div class = "error">'.$_SESSION['e_user_update'].'</div>';
          unset($_SESSION['e_user_update']);
      }
      ?>
      <div id= "user_managment" class="table-responsive">
          <table class="table table-striped table-bordered" id="data-table">
              <thead>
              <tr>
                  <th>ID Uzytkownika</th>
                  <th>Username</th>
                  <th>Imię</th>
                  <th>Nazwisko</th>
                  <th>Aktywny</th>
                  <th>Rola</th>
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
