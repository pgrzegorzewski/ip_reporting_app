<?php
  session_start();
  require '../core/connect.php';
?>

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
    <div class="md-form form-group">
      <button class="btn btn-info" id = "salesman_summary_data_refresh"><span id="salesman_summary_data_refresh_span">Odśwież/załaduj</span></button>
    </div>
  </div>
</div>
<div style = "text-align:center">
    <span id="error_msg" style="color:red"></span>
    <br><br>
</div>
<?php
  $query = "
         SELECT * FROM  usr.sf_sprawdz_prawo_dostepu($1, 9)
  ";
  $hasAccessQuery = @pg_query_params($connection, $query, array($_SESSION['user']));
  $hasAccess = pg_fetch_assoc($hasAccessQuery);
  if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
      echo "
              <div style='text-align:center'>
                <button class='btn btn-info' id = 'late_pay_table'><span id='late_pay_table_span'>Pokaż kwoty przeterm.</span></button>
              </div>
              <div id='late_pay_datatable' hidden=true></div>
           ";
  }
?>
<div class="table-responsive">
    <table class="table table-striped table-bordered" id="data-table">
        <thead>
        <tr>
            <th>Sprzedawca</th>
            <th>Suma wartości</th>
            <th>Suma marż</th>
            <th>Procent</th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th>Podsumowanie:</th>
                <th style="text-align:right"></th>
                <th style="text-align:right"></th>
                <th style="text-align:right"></th>
            </tr>
        </tfoot>
    </table>
</div>
