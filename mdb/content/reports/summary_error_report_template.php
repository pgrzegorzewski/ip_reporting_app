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
    <button class="btn btn-info" id = "error_summary_data_refresh"><span id="error_summary_data_refresh_span">Odśwież/załaduj</span></button>
  </div>
</div>
<div style = "text-align:center">
    <span id="error_msg" style="color:red"></span>
    <br><br>
</div>
<div class="table-responsive">
    <table class="table table-striped table-bordered" id="data-table">
        <thead>
        <tr>
            <th>Faktura</th>
            <th>Data wystawienia</th>
            <th>Kontrahent</th>
            <th>Sprzedawca</th>
            <th>Region</th>
            <th>Kraj</th>
            <th>Województwo</th>
            <th>Waluta</th>
            <th>Eksport</th>
            <th>Kurs</th>
            <th>Cena Zero</th>
            <th>Cena</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
