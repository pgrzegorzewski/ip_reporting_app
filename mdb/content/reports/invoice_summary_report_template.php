<div class="form-row" style="vertical-align:baseline">
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
  <div class="col-md-1 my-auto" style="text-align: left;">
    <span>Typ faktur:</span>
    <div class="custom-control custom-radio">
      <input type="radio" class="custom-control-input export_radio" value = "1" id="export_radio_1" name="export_radios" checked>
      <label class="custom-control-label" for="export_radio_1">Wszytskie</label>
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
  <div class="col-md-3">
      <div class="md-form form-group">
          <select class="form-control" id = "salesman" name = "salesman">
              <option selected>Sprzedawca</option>
          </select>
      </div>
  </div>
  <div class="col-md-1">
    <button class="btn btn-info" id = "invoice_summary_data_refresh"><span id="invoice_summary_data_refresh_span">Odśwież/załaduj</span></button>
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
            <th>Wartość</th>
            <th>Marża</th>
            <th>Procent</th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="4">Podsumowanie:</th>
                <th style="text-align:right"></th>
                <th style="text-align:right"></th>
                <th style="text-align:right"></th>
            </tr>
        </tfoot>
    </table>
</div>
