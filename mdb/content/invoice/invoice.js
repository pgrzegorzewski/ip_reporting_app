var today = new Date();
var expiry = new Date(today.getTime() + 30 * 24 * 3600 * 1000);
var TIMER_SECONDS = 3000;
var clients = [];
var originalModal ='';

$(document).ready(function () {
  originalModal  = $('#editInvoiceItemModal').clone();
});

$(document).on('hidden.bs.modal', '.modal', function () {
   $("#editInvoiceItemModal").remove();
   originalModal.insertAfter(".nav");
   bindModal();
 });

$(document).ready(function() {
  bindModal();
});

function bindModal() {
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

}

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
    loadDateCookies();
    appendShowInvoiceInfo();
    loadFilterValues();
    setRadiosFromCookies();
    setCurrencyFromCookie();

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

    $('#report_date_to').on('change', function() {
      getInvoiceNumbersFilter();
    });

    $('#report_date_from').on('change', function() {
      getInvoiceNumbersFilter();
    });
});

$(document).ready(function(){
  $('#clientEdit').change(function() {
    var id = $("#clientEdit").children("option:selected").val();
    var clientObj = clients.find(obj => {
      return obj.kontrahent_id === id
    });
    if(clientObj) {
      $('#bonusEdit').val(((clientObj.bonus) * 100).toFixed(2)).siblings().addClass('active');
    }
  })
});

$(document).ready(function(){
  $('#currencyEdit').change(function() {
    var currencyId = $("#currencyEdit").children("option:selected").val();
    if(currencyId == 1) {
      $('#rateEdit').val(1).siblings().addClass('active');
    }
  })
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
         bonus: $('#bonus').val(),
         country: $('#countryEdit').val(),
         voivodship: $('#voivodeshipEdit').val(),
         region: $('#regionEdit').val(),
         note : $('#noteEdit').val(),
         invoiceActive: $('#headerActiveEdit').is(':checked') ? 0 : 1,
         invoicePricesEdit: $('#invoicePricesEdit').is(':checked') ? 1 : 0,
         invoiceItemId: id
       },
       dataType: 'json',
       url: "./invoice_actions.php",
       success: function(data) {
         $('#invoiceHeaderUpdateResult').text(data);

       },
    });
    if($('#invoicePricesEdit').is(':checked') == 1) {
      $(function () {
        $('#editInvoiceItemModal').modal('toggle');
        window.location.reload(true);
      });
    }
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
         $('#bonus').val((data[0]['bonus_wprowadzony'] * 100).toFixed(2));
         $('#bonusEdit').val((data[0]['bonus_aktualny'] * 100).toFixed(2));
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
  var dateFrom = null;
  var dateTo = null;
  if($('#report_date_from').val()) {
    dateFrom = $('#report_date_from').val();
  }
  if($('#report_date_to').val()) {
    dateTo = $('#report_date_to').val();
  }

  $.ajax({
      url: "../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {
              type:'invoice_number',
              dateFrom: dateFrom,
              dateTo: dateTo
            },
      dataType: 'json',
      success:function(response){
          $("#invoice_number").children().remove().end();
          $("#invoice_number").append("<option>Numer faktury</option>");
          if(response) {
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
          clients = response;
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
        var editIconAvailable = 0
        if(jsonData.length > 0) {
          var editionCheck = jsonData[0].edycja;
          editIconAvailable = jsonData[0].edycja == 0 ? false : true;
        }
        $('#data-table').dataTable().fnDestroy();
        $('#data-table').DataTable({
            "scrollX": true,
            "paging": true,
            data : jsonData,
            columns: [
                {
                    data: 'faktura_numer',
                    width: '100px',
                },
                {
                  data: 'data_wystawienia',
                  width: '100px',
                },
                {
                    data: 'uzytkownik',
                    width: '120px',
                },
                {
                    data: 'waluta_kod',
                    width: '50px',
                },
                {
                    data: 'kurs',
                    width: '40px',
                },
                {
                    data: 'eksport',
                    width: '40px',
                },
                {
                    data: 'dostawa',
                    width: '40px',
                },
                {
                    data: 'przelew',
                    width: '40px',
                },
                {
                    data: 'kontrahent_nazwa',
                    width: '140px',
                },
                {
                    data: 'kraj_kod',
                    width: '60px',
                },
                {
                    data: 'wojewodztwo_kod',
                    width: '60px',
                },
                {
                    data: 'region_kod',
                    width: '60px',
                },
                {
                    data: 'bonus',
                    render: $.fn.dataTable.render.number( ' ', '.', 2),
                    width: '60px',
                },
                {
                    data: 'edycja',
                    visible: editIconAvailable,
                    width: '80px',
                },
                {
                  data: 'pozycja_faktura',
                  width: '40px',
                },
                {
                  data: 'towar_nazwa',
                  width: '120px',
                },
                {
                  data: 'ilosc',
                  width: '80px',
                },
                {
                  data: 'jednostka',
                  width: '40px',
                },
                {
                  data: 'cena',
                  width: '70px',
                },
                {
                    data: 'cena_zero',
                    width: '70px',
                },
                {
                  data: 'wartosc',
                  render: $.fn.dataTable.render.number( ' ', '.', 2),
                  width: '100px'
                },
                {
                  data: 'marza',
                  render: $.fn.dataTable.render.number( ' ', '.', 2),
                  width: '100px'
                },
                {
                  data: 'procent',
                  render: function(data) {
                    return (data*100).toFixed(2) + '%';
                  },
                  width: '70px',
                },
                {
                  data: 'uwagi',
                  width: '280px',
                }
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
    filters.export = $('input[name=export_radios]:checked').val();
    filters.pay = $('input[name=pay_radios]:checked').val();
    filters.delivery = $('input[name=delivery_radios]:checked').val();
    filters.currency = $("#currency").children("option:selected").val();
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

$(document).ready(function(){
  $('#getPriceZeroButtonItemEdit').click(function(){
    if($("#itemEdit").children("option:selected").val() || isNaN($("#itemEdit").children("option:selected").val())) {
      getItemPriceZero( $("#itemEdit").children("option:selected").val(), 'edit');
    }
  });
});

$(document).ready(function(){
  $('#getPriceZeroButtonItemAdd').click(function(){
    if($("#itemAdd").children("option:selected").val() || isNaN($("#itemAdd").children("option:selected").val())) {
      getItemPriceZero( $("#itemAdd").children("option:selected").val(), 'add');
    }
  });
});

function getItemPriceZero(itemId, type) {
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
      var priceZero = 0;
      if($("#transferCheckboxEdit").is(":checked") == false && $("#deliveryCheckboxEdit").is(":checked") == false) {
        priceZero = ((data[0]['cena_go'] * 100) / (100 - $('#bonus').val())).toFixed(2);
      } else if ($("#transferCheckboxEdit").is(":checked") == true && $("#deliveryCheckboxEdit").is(":checked") == false) {
        priceZero = ((data[0]['cena_po'] * 100) / (100 - $('#bonus').val())).toFixed(2);
      } else if ($("#transferCheckboxEdit").is(":checked") == false && $("#deliveryCheckboxEdit").is(":checked") == true) {
        priceZero = ((data[0]['cena_gd'] * 100) / (100 - $('#bonus').val())).toFixed(2);
      } else if ($("#transferCheckboxEdit").is(":checked") == true && $("#deliveryCheckboxEdit").is(":checked") == true) {
        priceZero = ((data[0]['cena_pd'] * 100) / (100 - $('#bonus').val())).toFixed(2);
      }

      var rate = $("#rateEdit").val() ? $("#rateEdit").val() : 1;
      if(type == 'edit') {
        $('#priceZeroEdit').val(priceZero);
      } else {
        $('#priceZeroAdd').val(priceZero);
      }
    }
  });
}

function updateItemPrices(itemId, type) {

  var rate = $("#rateEdit").val() ? $("#rateEdit").val() : 1;
  if(type == 'edit') {
    var priceZero = $('#priceZeroEdit').val();
    $('#valueEdit').val( ($('#amountEdit').val() * $('#priceEdit').val() * rate ).toFixed(2));
    var margin = ($('#amountEdit').val() * $('#priceEdit').val() * rate) - ($('#amountEdit').val() * priceZero);
    $('#marginEdit').val(margin.toFixed(2));
    $('#percentEdit').val((margin/($('#amountEdit').val() *$('#priceEdit').val() * rate)).toFixed(6));
  } else {
    var priceZero = $('#priceZeroAdd').val();
    $('#valueAdd').val( ($('#amountAdd').val() * $('#priceAdd').val() * rate).toFixed(2));
    var margin = ($('#amountAdd').val() * $('#priceAdd').val() * rate) - ($('#amountAdd').val() * priceZero);
    $('#marginAdd').val(margin.toFixed(2));
    $('#percentAdd').val((margin/($('#amountAdd').val() *$('#priceAdd').val() * rate)).toFixed(6));
  }
}

function checkAddItemForm() {
  updateItemPrices($("#itemAdd").children("option:selected").val(), 'add');
  var success = validateItemAddData();
  return success;
}

function checkEditItemForm() {
  updateItemPrices($("#itemEdit").children("option:selected").val(), 'edit');
  var success = validateItemEditData();
  return success;
}

function validateItemAddData() {
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


function validateItemEditData() {
  success = true;
  if (!$("#itemEdit").children("option:selected").val() || isNaN($("#itemEdit").children("option:selected").val())) {
      success = false;
      $("#itemEdit").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#itemEdit').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#amountEdit").val() || isNaN($("#amountEdit").val()) || $("#amountEdit").val() <= 0) {
      success = false;
      $("#amountEdit").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#amountEdit').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#priceEdit").val() || isNaN($("#priceEdit").val()) || $("#priceEdit").val() <= 0) {
      success = false;
      $("#priceEdit").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#priceEdit').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#priceZeroEdit").val() || isNaN($("#priceZeroEdit").val()) || $("#priceZeroEdit").val() <= 0) {
      success = false;
      $("#priceZeroEdit").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#priceZeroEdit').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#valueEdit").val() || isNaN($("#valueEdit").val()) || $("#valueEdit").val() <= 0) {
      success = false;
      $("#valueEdit").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#valueEdit').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#marginEdit").val() || isNaN($("#marginEdit").val()) ) {
      success = false;
      $("#marginEdit").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#marginEdit').css('border-color', '');
      }, TIMER_SECONDS);
  }
  if (!$("#percentEdit").val() || isNaN($("#percentEdit").val()) ) {
      success = false;
      $("#percentEdit").css('border-color', 'red');
      timer = setTimeout(function() {
        $('#percentEdit').css('border-color', '');
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

    if (isNaN(filters.delivery)) {
      filters.delivery = 1;
    }
    setCookie('delivery', filters.delivery);

    if (isNaN(filters.export)) {
      filters.export = 1;
    }
    setCookie('export', filters.export);

    if (isNaN(filters.pay)) {
      filters.pay = 1;
    }
    setCookie('pay', filters.pay);

    if (isNaN(filters.salesman)) {
      filters.salesman = null;
    }
    setCookie('salesman', filters.salesman);

    if (isNaN(filters.client)) {
      filters.client = null;
    }
    setCookie('client', filters.client);

    if (isNaN(filters.currency)) {
      filters.currency = null;
    }
    setCookie('currency', filters.currency);

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



function setRadiosFromCookies() {
  if(getCookie('export') != 'null') {
    $('#export_radio_'+ getCookie('export')).attr('checked', true);
  }

  if(getCookie('pay') != 'null') {
    $('#pay_radio_'+ getCookie('pay')).attr('checked', true);
  }

  if(getCookie('delivery') != 'null') {
    $('#delivery_radio_'+ getCookie('delivery')).attr('checked', true);
  }
}

function setCurrencyFromCookie() {
  if(getCookie('currency') != 'null') {
    $('#currency').val(getCookie('currency'));
  }
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
