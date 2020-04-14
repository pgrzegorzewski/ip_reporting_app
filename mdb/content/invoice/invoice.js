var today = new Date();
var expiry = new Date(today.getTime() + 30 * 24 * 3600 * 1000);

$(document).ready(function(){
    appendShowInvoiceInfo();
    loadFilterValues();
    loadDateCookies();
    $('#data-table').DataTable({
        "scrollX": true,
    });
});

function getInvoiceNumbersFilter() {
  $.ajax({
      url: "../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'invoice_number'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var faktura_id = response[i]['faktura_id'];
              var faktura_numer = response[i]['faktura_numer'];

              $("#invoice_number").append("<option value='"+faktura_id+"'>"+faktura_numer+"</option>");
          }
          if(getCookie('invoice_number') != 'null') {
            $('#invoice_number').val(getCookie('invoice_number'));
          }
      }
  });
}

function getRegionFilter() {
  $.ajax({
      url: "../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'region'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var region_id = response[i]['region_id'];
              var region_name = response[i]['region_nazwa'];

              $("#region").append("<option value='"+region_id+"'>"+region_name+"</option>");
          }
          if(getCookie('region') != 'null') {
            $('#region').val(getCookie('region'));
          }
      }
  });
}

function getCountryFilter() {
  $.ajax({
      url: "../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type: 'country'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i < len; i++){
              var country_id = response[i]['kraj_id'];
              var country_name = response[i]['kraj_nazwa'];

              $("#country").append("<option value='"+country_id+"'>"+country_name+"</option>");
          }
          if(getCookie('country') != 'null') {
            $('#country').val(getCookie('country'));
          }
      }
  });
}

function getVoivodeshipFilter() {
  $.ajax({
      url: "../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'voivodeship'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var voivodeship_id = response[i]['wojewodztwo_id'];
              var voivodeship_name = response[i]['wojewodztwo_nazwa'];

              $("#voivodeship").append("<option value='"+voivodeship_id+"'>"+voivodeship_name+"</option>");
          }
          if(getCookie('voivodeship') != 'null') {
            $('#voivodeship').val(getCookie('voivodeship'));
          }
      }
  });
}

function getClientFilter() {
  $.ajax({
      url: "../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'client'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var client_id = response[i]['kontrahent_id'];
              var client_name = response[i]['kontrahent_nazwa'];

              $("#client").append("<option value='"+client_id+"'>"+client_name+"</option>");
          }
          if(getCookie('client') != 'null') {
            $('#client').val(getCookie('client'));
          }
      }
  });
}

function getSalesmanFilter() {
  $.ajax({
      url: "../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'salesman'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var salesman_id = response[i]['uzytkownik_id'];
              var salesman_name = response[i]['uzytkownik_nazwa'];

              $("#salesman").append("<option value='"+salesman_id+"'>"+salesman_name+"</option>");
          }
          if(getCookie('salesman') != 'null') {
            $('#salesman').val(getCookie('salesman'));
          }
      }
  });
}

function loadFilterValues() {
  getInvoiceNumbersFilter();
  getRegionFilter();
  getCountryFilter();
  getVoivodeshipFilter();
  getClientFilter();
  getSalesmanFilter();
}

function appendShowInvoiceInfo() {
  $('#showInvoiceInfo').bind( "click", function() {
    showInvoices();
    $('#loadButtonSpan').addClass("spinner-border spinner-border-sm text-light");
    $('#loadButtonSpan').text("");
  });
}

function showInvoices(){
  var filters= getFilters();
  filters = checkFilters(filters);

  $.ajax({
      url: "./get_invoice_info.php",
      method: "POST",
      data: {data: JSON.stringify(filters)},
      dataType: 'json',
      success: function (jsonData) {
        var edictionAvailable = 0
        if(jsonData.length > 0) {
          var editionCheck = jsonData[0].edycja;
          edictionAvailable = jsonData[0].edycja == 0 ? false : true;
        }
        $("#data-table").dataTable().fnDestroy();
        $('#data-table').DataTable({
            "scrollX": true,
            data : jsonData,
            columns: [
                {data: 'faktura_numer'},
                {data: 'data_wystawienia'},
                {data: 'uzytkownik'},
                {data: 'waluta_kod'},
                {data: 'kurs'},
                {data: 'eksport'},
                {data: 'dostawa'},
                {data: 'przelew'},
                {data: 'kontrahent_nazwa'},
                {data: 'kraj_kod'},
                {data: 'wojewodztwo_kod'},
                {data: 'region_kod'},
                {
                    data: 'edycja',
                    visible: edictionAvailable,
                },
                {data: 'pozycja_faktura'},
                {data: 'towar_nazwa'},
                {data: 'ilosc'},
                {data: 'jednostka'},
                {data: 'cena'},
                {data: 'cena_zero'},
                {data: 'wartosc'},
                {data: 'marza'},
                {data: 'procent'}
            ]
        });
        $('#loadButtonSpan').removeClass("spinner-border spinner-border-sm text-danger");
        $('#loadButtonSpan').text("Załaduj");
      }
  })

}

function getFilters() {
    var filters = {};

    filters.invoice_number = $("#invoice_number").val();
    filters.invoice_date_from = $("#report_date_from").val();
    filters.invoice_date_to= $("#report_date_to").val();
    filters.salesman = $("#salesman").children("option:selected").val();
    filters.client = $("#client").children("option:selected").val();
    filters.country = $("#country").children("option:selected").val();
    filters.voivodeship = $("#voivodeship").children("option:selected").val();
    filters.region = $("#region").children("option:selected").val();
    return filters;
}

function checkFilters(filters) {

    if (!filters.invoice_date_from) {
      filters.invoice_date_from = null;
    }
    setCookie('invoice_date_from', filters.invoice_date_from);

    if (!filters.invoice_date_to) {
      filters.invoice_date_to = null;
    }
    setCookie('invoice_date_to', filters.invoice_date_to);

    if (isNaN(filters.invoice_number)) {
      filters.invoice_number = null;
    }
    setCookie('invoice_number', filters.invoice_number);

    if (isNaN(filters.salesman)) {
      filters.salesman = null;
    }
    setCookie('salesman', filters.salesman);

    if (isNaN(filters.client)) {
      filters.client = null;
    }
    setCookie('client', filters.client);

    if (isNaN(filters.country)) {
      filters.country = null;
    }
    setCookie('country', filters.country);

    if (isNaN(filters.voivodeship)) {
      filters.voivodeship = null;
    }
    setCookie('voivodeship', filters.voivodeship);

    if (isNaN(filters.region)) {
      filters.region = null;
    }
    setCookie('region', filters.region);

    return filters;
}

function setCookie(name, value)
{
  document.cookie=name + "=" + escape(value) + "; path=/; expires=" + expiry.toGMTString();
}

function loadDateCookies() {
  if(getCookie('invoice_date_from') != 'null') {
    $('#report_date_from').val(getCookie('invoice_date_from'));
  }

  if(getCookie('invoice_date_to') != 'null') {
    $('#report_date_to').val(getCookie('invoice_date_to'));
  }
}

function getCookie(name)
{
  var re = new RegExp(name + "=([^;]+)");
  var value = re.exec(document.cookie);
  return (value != null) ? unescape(value[1]) : null;
}
