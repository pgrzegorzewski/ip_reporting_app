var today = new Date();
var expiry = new Date(today.getTime() + 30 * 24 * 3600 * 1000);
var TIMER_SECONDS = 3000;

$(document).ready(function(){
    $('#editInvoiceItemModal').on('show.bs.modal', function(e) {
      $('#invoiceHeaderEditButton').removeClass('btn-danger').addClass('btn-info');
      $('#itemUpdateButton').removeClass('btn-danger').addClass('btn-info');
      $("#headerActiveEdit").prop('checked', false);
      $("#itemActiveEdit").prop('checked', false);
      clearErrorMessages();
      var invoiceItemId = $(e.relatedTarget).data('id');
      getInvoiceHeaderData(invoiceItemId);
      getInvoiceItemData(invoiceItemId);
      $('#invoiceHeaderEditButton').click(function () {
        updateInvoiceHeader(invoiceItemId);
      })
    });

    appendShowInvoiceInfo();
    loadFilterValues();
    loadDateCookies();

    $('#data-table').DataTable({
        "scrollX": true,
    });


    $("#headerActiveEdit").change(function() {
      if(this.checked) {
        $('#invoiceHeaderEditButton').removeClass('btn-info').addClass('btn-danger');
      } else {
          $('#invoiceHeaderEditButton').removeClass('btn-danger').addClass('btn-info');
      }
    });

    $("#itemActiveEdit").change(function() {
      if(this.checked) {
        $('#itemUpdateButton').removeClass('btn-info').addClass('btn-danger');
      } else {
          $('#itemUpdateButton').removeClass('btn-danger').addClass('btn-info');
      }
    });

    $("#invoiceItemEditButton").click(function(){
      $('#invoiceItemEditButton').prop("disabled", true);
      $('#invoiceItemEditButton').removeClass('btn-info').addClass('btn-success');
      $('#invoiceItemAddButton').prop("disabled", false);
      $('#invoiceItemAddButton').removeClass('btn-success').addClass('btn-info');

      $('#editInvoiceItemForm').show();
      $('#addInvoiceItemForm').hide();
    });


    $("#invoiceItemAddButton").click(function(){
      $('#invoiceItemAddButton').prop("disabled", true);
      $('#invoiceItemAddButton').removeClass('btn-info').addClass('btn-success');
      $('#invoiceItemEditButton').prop("disabled", false);
      $('#invoiceItemEditButton').removeClass('btn-success').addClass('btn-info');
      $('#addInvoiceItemForm').show();
      $('#editInvoiceItemForm').hide();
    });
});


function updateInvoiceHeader(id)
{
  if(checkInvoiceHeaderInput() == true) {

    $.ajax({
       method: "POST",
       data: {
         action : "updateInvoiceHeader",
         invoiceNumber : $('#invoiceNumberEdit').val(),
         invoiceDate : $('#invoiceDateEdit').val(),
         salesman: $('#salesmanEdit').val(),
         currency: $('#currencyEdit').val(),
         rate: $('#rateEdit').val(),
         export: $('#exportCheckboxEdit').is(':checked')  ? 1 : 0,
         transfer: $('#transferCheckboxEdit').is(':checked') ? 1 : 0,
         delivery: $('#deliveryCheckboxEdit').is(':checked') ? 1 : 0,
         client: $('#clientEdit').val(),
         country: $('#countryEdit').val(),
         voivodship: $('#voivodeshipEdit').val(),
         region: $('#regionEdit').val(),
         note : $('#noteEdit').val(),
         invoiceActive: $('#headerActiveEdit').is(':checked') ? 0 : 1,
         invoiceItemId: id
       },
       dataType: 'json',
       url: "./invoice_actions.php",
       success: function(data) {
         $('#invoiceHeaderUpdateResult').text(data);

       },
    })
  }
}

$('#exportCheckboxEdit').prop('checked', true);

function checkInvoiceHeaderInput() {
  var success = true;
  var invoiceNumber = $('#invoiceNumberEdit').val();

  if(!invoiceNumber || invoiceNumber.length < 4) {
    success = false;
    $('#invoiceNumberError').text('Numer faktury musi mieć ponad 3 znaki');
    $("#invoiceNumberEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#invoiceNumberEdit').css('border-color', '');
      $('#invoiceNumberError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#invoiceNumberError').text('');
  }

  var today = new Date();
  var invoiceDate = new Date($('#invoiceDateEdit').val());
  if(!invoiceDate || isNaN(invoiceDate.getTime()) || invoiceDate.getTime() > today.getTime() ) {
    $('#invoiceDateError').text('Nieprawidłowa data');
    success = false;
    $("#invoiceDateEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#invoiceDateEdit').css('border-color', '');
      $('#invoiceDateError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#invoiceDateError').text('');
  }

  var salesman = $('#salesmanEdit').val();
  if(!salesman || isNaN(salesman)) {
    $('#salesmanError').text('Wybierz sprzedawcę');
    success = false;
    $("#salesmanEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#salesmanEdit').css('border-color', '');
      $('#salesmanError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#salesmanError').text('');
  }

  var currency = $('#currencyEdit').val();
  if(!currency || isNaN(currency)) {
    $('#currencyError').text('Wybierz walutę');
    success = false;
    $("#currencyEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#currencyEdit').css('border-color', '');
      $('#currencyError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#currencyError').text('');
  }

  var rate = $('#rateEdit').val();
  if(!rate || isNaN(rate)) {
    $('#rateError').text('Brak kursu');
    success = false;
    $("#rateEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#rateEdit').css('border-color', '');
      $('#rateError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#rateError').text('');
  }

  var client = $('#clientEdit').val();
  if(!client || isNaN(client)) {
    $('#clientError').text('Wybierz kontrahenta');
    success = false;
    $("#clientEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#clientEdit').css('border-color', '');
      $('#clientError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#clientError').text('');
  }

  var country = $('#countryEdit').val();
  if(!country || isNaN(country)) {
    $('#countryError').text('Wybierz kraj');
    success = false;
    $("#countryEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#countryEdit').css('border-color', '');
      $('#countryError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#countryError').text('');
  }

  var voivodeship = $('#voivodeshipEdit').val();
  if(!voivodeship || isNaN(voivodeship)) {
    $('#voivodeshipError').text('Wybierz województwo');
    success = false;
    $("#voivodeshipEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#voivodeshipEdit').css('border-color', '');
      $('#voivodeshipError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#voivodeshipError').text('');
  }

  var region = $('#regionEdit').val();
  if(!region || isNaN(region)) {
    $('#regionError').text('Wybierz region');
    success = false;
    $("#regionEdit").css('border-color', 'red');
    timer = setTimeout(function() {
      $('#regionEdit').css('border-color', '');
      $('#regionError').text('');
    }, TIMER_SECONDS);
  } else {
      $('#regionError').text('');
  }
  return success;
}

function clearErrorMessages() {
  $('#invoiceNumberError').text('');
  $('#invoiceDateError').text('');
  $('#salesmanError').text('');
  $('#currencyError').text('');
  $('#rateError').text('');
  $('#clientError').text('');
  $('#countryError').text('');
  $('#voivodeshipError').text('');
  $('#regionError').text('');
  $('#invoiceHeaderUpdateResult').text('');
}

function getInvoiceHeaderData(id) {
  $.ajax({
     method: "POST",
     data: {action : "getInvoiceHeaderData", invoiceItemId : id},
     dataType: 'json',
     url: "./invoice_actions.php",
     success: function (data) {
         $('#invoiceNumberEdit').val(data[0]['faktura_numer']);
         $('#invoiceDateEdit').val(data[0]['data_wystawienia']);
         $('#salesmanEdit').val(data[0]['uzytkownik_id']).change();
         $('#currencyEdit').val(data[0]['waluta_id']).change();
         $('#rateEdit').val(data[0]['kurs']).change();
         if(data[0]['eksport'] == 1) {
           $('#exportCheckboxEdit').prop('checked', true);
         } else {
           $('#exportCheckboxEdit').prop('checked', false);
         }
         if(data[0]['dostawa'] == 1) {
           $('#deliveryCheckboxEdit').prop('checked', true);
         } else {
           $('#deliveryCheckboxEdit').prop('checked', false);
         }
         if(data[0]['przelew'] == 1) {
           $('#transferCheckboxEdit').prop('checked', true);
         } else {
           $('#transferCheckboxEdit').prop('checked', false);
         }
         $('#clientEdit').val(data[0]['kontrahent_id']).change();
         $('#countryEdit').val(data[0]['kraj_id']).change();
         $('#voivodeshipEdit').val(data[0]['wojewodztwo_id']).change();
         $('#regionEdit').val(data[0]['region_id']).change();
         $('#noteEdit').val(data[0]['uwagi']);

     },
  })
}

function getInvoiceItemData(id) {
  $.ajax({
     method: "POST",
     data: {action : "getInvoiceItemData", invoiceItemId : id},
     dataType: 'json',
     url: "./invoice_actions.php",
     success: function (data) {
         $('#itemEdit').val(data[0]['towar_id']).change();
         $('#amountEdit').val(data[0]['ilosc']);
         $('#unitEdit').val(data[0]['jednostka']);
         $('#priceEdit').val(data[0]['cena']);
         $('#priceZeroEdit').val(data[0]['cena_zero']);
         $('#valueEdit').val(data[0]['wartosc']);
         $('#marginEdit').val(data[0]['marza']);
         $('#percentEdit').val(data[0]['procent']);
         $('#invoiceItemId').val(id);
         $('#relatedInvoiceItemId').val(id);
     },
  })
}

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
              $("#regionEdit").append("<option value='"+region_id+"'>"+region_name+"</option>");
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
              $("#countryEdit").append("<option value='"+country_id+"'>"+country_name+"</option>");
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
              $("#voivodeshipEdit").append("<option value='"+voivodeship_id+"'>"+voivodeship_name+"</option>");
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
              $("#clientEdit").append("<option value='"+client_id+"'>"+client_name+"</option>");
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
              $("#salesmanEdit").append("<option value='"+salesman_id+"'>"+salesman_name+"</option>");
          }
          if(getCookie('salesman') != 'null') {
            $('#salesman').val(getCookie('salesman'));
          }
      }
  });
}

function getItemFilter() {
  $.ajax({
      url: "../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'item'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var item_id = response[i]['towar_id'];
              var item_name = response[i]['towar_nazwa'];

              $("#itemEdit").append("<option value='"+item_id+"'>"+item_name+"</option>");
              $("#itemAdd").append("<option value='"+item_id+"'>"+item_name+"</option>");
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
  getItemFilter();
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
                {data: 'procent'},
                {data: 'uwagi'}
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

$(document).ready(function(){
  $('#recalculatePricesButtonItemEdit').click(function(){
    if($("#itemEdit").children("option:selected").val() || isNaN($("#itemEdit").children("option:selected").val())) {
      updateItemPrices( $("#itemEdit").children("option:selected").val(), 'edit');
    }
  });
});

$(document).ready(function(){
  $('#recalculatePricesButtonItemAdd').click(function(){
    if($("#itemAdd").children("option:selected").val() || isNaN($("#itemAdd").children("option:selected").val())) {
      updateItemPrices($("#itemAdd").children("option:selected").val(), 'add');
    }
  });
});

function updateItemPrices(itemId, type) {
  $.ajax({
    method: "POST",
    global: false,
    data: {
        action : "getItemPrices",
        item : itemId,
        amount: 1
    },
    dataType: 'json',
    url: "../invoice_import/invoice_import_actions.php",
    success: function (data) {
      console.log(data);
      var priceZero = 0;
      if($("#transferCheckboxEdit").is(":checked") == false && $("#deliveryCheckboxEdit").is(":checked") == false) {
        priceZero = data[0]['cena_go'];
      } else if ($("#transferCheckboxEdit").is(":checked") == true && $("#deliveryCheckboxEdit").is(":checked") == false) {
        priceZero = data[0]['cena_po'];
      } else if ($("#transferCheckboxEdit").is(":checked") == false && $("#deliveryCheckboxEdit").is(":checked") == true) {
        priceZero = data[0]['cena_gd'];
      } else if ($("#transferCheckboxEdit").is(":checked") == true && $("#deliveryCheckboxEdit").is(":checked") == true) {
        priceZero = data[0]['cena_po'];
      }

      if(type == 'edit') {
        $('#priceZeroEdit').val(priceZero);
        $('#valueEdit').val( ($('#amountEdit').val() * $('#priceEdit').val() ).toFixed(2));
        var margin = ($('#amountEdit').val() * $('#priceEdit').val()) - ($('#amountEdit').val() * priceZero);
        $('#marginEdit').val(margin.toFixed(2));
        $('#percentEdit').val((margin/($('#amountEdit').val() *$('#priceEdit').val())).toFixed(6));
      } else {
        $('#priceZeroAdd').val(priceZero);
        $('#valueAdd').val( ($('#amountAdd').val() * $('#priceAdd').val() ).toFixed(2));
        var margin = ($('#amountAdd').val() * $('#priceAdd').val()) - ($('#amountAdd').val() * priceZero);
        $('#marginAdd').val(margin.toFixed(2));
        $('#percentAdd').val((margin/($('#amountAdd').val() *$('#priceAdd').val())).toFixed(6));
      }
    }
  });
}

function checkAddItemForm() {
  var success = validateItemData();
  return success;
}

function validateItemData() {
  success = true;
  if (!$("#itemAdd").children("option:selected").val() || isNaN($("#itemAdd").children("option:selected").val())) {
      success = false;
      $("#itemAdd").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#itemAdd').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#amountAdd").val() || isNaN($("#amountAdd").val()) || $("#amountAdd").val() <= 0) {
      success = false;
      $("#amountAdd").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#amountAdd').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#priceAdd").val() || isNaN($("#priceAdd").val()) || $("#priceAdd").val() <= 0) {
      success = false;
      $("#priceAdd").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#priceAdd').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#priceZeroAdd").val() || isNaN($("#priceZeroAdd").val()) || $("#priceZeroAdd").val() <= 0) {
      success = false;
      $("#priceZeroAdd").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#priceZeroAdd').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#valueAdd").val() || isNaN($("#valueAdd").val()) || $("#valueAdd").val() <= 0) {
      success = false;
      $("#valueAdd").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#valueAdd').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#marginAdd").val() || isNaN($("#marginAdd").val()) ) {
      success = false;
      $("#marginAdd").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#marginAdd').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#percentAdd").val() || isNaN($("#percentAdd").val()) ) {
      success = false;
      $("#percentAdd").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#percentAdd').css('border-color', '');
      }, TIMER_SECONDS);
  }
  return success;
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
