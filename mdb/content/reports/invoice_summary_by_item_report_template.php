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
  <div class="col-md-3">
      <div class="md-form form-group">
          <select class="form-control" id = "item" name = "item">
              <option selected>Towar</option>
          </select>
      </div>
  </div>
  <div class="col-md-1">
    <button class="btn btn-info" id = "invoice_summary_by_item_data_refresh"><span id="invoice_summary_by_item_data_refresh_span">Odśwież/załaduj</span></button>
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
            <th>Towar</th>
            <th>Numer faktury</th>
            <th>Kontrahent</th>
            <th>Sprzedawca</th>
            <th>Ilość</th>
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
                <th style="text-align:right"></th>
            </tr>
        </tfoot>
    </table>
</div>
