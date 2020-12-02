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
    <button class="btn btn-info" id = "country_summary_data_refresh"><span id="country_summary_data_refresh_span">Odśwież/załaduj</span></button>
  </div>
</div>
<div style = "text-align:center">
    <span id="error_msg" style="color:red"></span>
    <br><br>
</div>
<div class="table-responsive" style="width:95% text-align:center">
    <table class="table table-striped table-bordered" id="data-table">
        <thead>
        <tr>
            <th>Kraj nazwa</th>
            <th>Kraj kod</th>
            <th>Suma wartości</th>
            <th>Suma marż</th>
            <th>Procent</th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th>Podsumowanie:</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
