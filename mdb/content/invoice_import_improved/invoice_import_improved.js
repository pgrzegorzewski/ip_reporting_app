var items = [];
var addedItems = 0;
var INVOICE_HEADER_FIELDS = ['faktura_numer', 'data_wystawienia', 'kontrahent', 'waluta_kod', 'kurs', 'kraj_kod', 'wojewodztwo_nazwa', 'dostawa', 'przelew', 'wartosc_faktury', 'sprzedawca'];
var INVOICE_ITEM_FIELDS = ['towar', 'nazwa towaru', 'jm', 'ilość', 'wartość pozycji', 'cena'];
var formatter = new Intl.NumberFormat('ru-RU', {
  style: 'currency',
  currency: 'PLN',
});
var clients = [];
var salesmen = [];
var voivodeships = [];
var countries = [];


$(document).ready(function(){
    appendAddInvoice();
    $("#data-table").dataTable().fnDestroy();
    $('#data-table').DataTable({
        "scrollX": true,
        "paging": false,
        columns: [
            {},
            {
              "width": "15%",
            },
            {},
            {
              "width": "15%",
            },
            {
             "width": "70px",
            },
            {
              "width": "15%",
            },
            {
              "width": "10%",
            },
            {
              "width": "10%",
            },
            {
              "width": "10%",
            },
            {
              "width": "10%",
            },
            {},
        ]
    });
    importFilters();
});


$(document).ready(function(){
  $('#client').change(function() {
    var id = $("#client").children("option:selected").val();

    var clientObj = clients.find(obj => {
      return obj.kontrahent_id === id
    });

    if(clientObj) {
       if(clientObj.wojewodztwo_id) {
         $('#voivodeship').val(clientObj.wojewodztwo_id).change();
       } else {
         $("#voivodeship").val($("#voivodeship option:first").val());
       }
       if(clientObj.region_id != null && clientObj.region_id != '') {
         $('#region').val(clientObj.region_id).change();
       } else {
         $("#region").val($("#region option:first").val());
       }
       if(clientObj.kraj_id != null && clientObj.kraj_id != '') {
         $('#country').val(clientObj.kraj_id).change();
       } else {
          $("#country").val($("#country option:first").val());
       }
    }
  })
});


$(document).ready(function(){
  $('#recalculatePricesButton').click(function(){
    updateItemPrices(getSelectedItems());
    calculateSummaryValues(getSelectedItems());
  });
});

$(document).ready(function(){
  $('#client').change(function() {
    var id = $("#client").children("option:selected").val();
    var clientObj = clients.find(obj => {
      return obj.kontrahent_id === id
    });
    if(clientObj) {
      $('#bonus').val(((clientObj.bonus) * 100).toFixed(2)).siblings().addClass('active');
    }
  })
});

$(document).ready(function(){
  $('#currency').change(function() {
    var currencyId = $("#currency").children("option:selected").val();
    if(currencyId == 1) {
      $('#rate').val(1).siblings().addClass('active');
    }
  })
});

$(document).ready(function(){
   $('#upload_csv').on('submit', function (event) {
        $('#import_label').text('');
        $('#import_label').addClass('spinner-border spinner-border-sm text-primary');
        $('#import_invoice_numbers').empty();
        $('#invoices_already_imported').prop("hidden", true);
        var defaultVal = 0;
        invoiceHeaders = [];
        invoiceNumbers = [];

        event.preventDefault();
        $.ajax({
            url: "./import_csv.php",
            method: "POST",
            data: new FormData(this),
            dataType: 'json',
            global: false,
            contentType: false,
            cache: false,
            processData: false,
            success: function (jsonData) {
              invoiceNumbers = $.uniqueSort(getInvoiceNumbers(jsonData));
              invoiceHeaders = getInvoiceHeaders(jsonData);
              invoiceItems = getInvoiceItems(jsonData);
              $.each(invoiceNumbers, function( index, value ){
                  $('#import_invoice_numbers').css('display', 'block');
                  $('#import_invoice_numbers').append("<button class='btn btn-info invoiceToImport'><span>"+ value+ "</span></button>");
              });

              $('.invoiceToImport').bind('click', loadInvoiceToImport);

               $('#import_label').removeClass('spinner-border spinner-border-sm text-primary');
               $('#import_label').text('Wybierz plik');
               markAlreadyImportedInvoices();
               if(invoiceHeaders.length == $('.invoiceToImport:disabled').length && invoiceHeaders.length > 0) {
                 $('#invoices_already_imported').prop("hidden", false);
               } else {
                 loadNextInvoice();
               }

            },
            error : function() {
              $('#import_label').removeClass('spinner-border spinner-border-sm text-primary');
              $('#import_label').text('Wybierz plik');
            },
        })
   })

});

function loadNextInvoice() {
  var notImportedInvoiceIndexes = [];
  var currentInvoiceIndex = -1;
  $.each(invoiceNumbers, function(index) {
    if(!$('.invoiceToImport span:contains('+ invoiceNumbers[index]  +')').parent().is(":disabled")) {
      notImportedInvoiceIndexes.push(index);
    }
    if($('.invoiceToImport span:contains('+ invoiceNumbers[index]  +')').parent().css("border-width") != "0px") {
      currentInvoiceIndex = index;
    }
  });

  if(notImportedInvoiceIndexes.length > 0) {
    if(currentInvoiceIndex == -1) {
      $('.invoiceToImport span:contains('+ invoiceNumbers[notImportedInvoiceIndexes[0]]  +')').parent().click();
    }
    if(currentInvoiceIndex >= 0) {
      var nextInvoiceNumberIndex = getNextHigherValue(notImportedInvoiceIndexes, currentInvoiceIndex);
      if(nextInvoiceNumberIndex >= 0) {
        $('.invoiceToImport span:contains('+ invoiceNumbers[nextInvoiceNumberIndex]  +')').parent().click();
      } else if (nextInvoiceNumberIndex == -1 && notImportedInvoiceIndexes.length > 1) {
        $('.invoiceToImport span:contains('+ invoiceNumbers[notImportedInvoiceIndexes[0]]  +')').parent().click();
      }
    }
  }


}

function getNextHigherValue(array, number) {
  for (var i = 0; i < array.length; i ++) {
    if (array[i] > number) {
      return array[i];
    }
  }
  return -1;
}

function markAlreadyImportedInvoices() {
  $.each(invoiceNumbers, function(index) {
    $.ajax({
        method: "POST",
        global: false,
        data: { action : "isInvoiceImported",
                invoice_number: invoiceNumbers[index]
              },
        dataType: 'json',
        async: false,
        url: "./invoice_import_actions.php",
        success: function (jsonData) {
            if(jsonData.isInvoiceImported == 1) {
              markInvoiceAsImported(invoiceNumbers[index]);
            };
        }
    })
  });
}

function markInvoiceAsImported(invoice) {
  $('.invoiceToImport span:contains('+ invoice +')').parent().removeClass('btn-info').addClass('btn-success').attr("disabled", true);
}

function loadInvoiceToImport() {

  $('.invoiceToImport').css({"border-color": "red","border-width":"0px","border-style":"solid"});
  $(this).css({"border-color": "red","border-width":"2px","border-style":"solid"});

  $('.loading').css("display", "block");
  var invoiceNumber = $(this).text();
  var currentInvoiceHeader = arrayLookup(invoiceHeaders, 'faktura_numer', $(this).text());
  timer = setTimeout(function() {
    clearLoadedHeader();
    setCurrentHeader(currentInvoiceHeader);
    loadItemsDataTable(invoiceItems[ invoiceNumber]);
    $('.loading').css("display", "none");
  }, 500);
}


function loadItemsDataTable(jsonData) {
  $('#recalculatePricesButton').prop("disabled", false);

  var loopCnt = 0;
  var itemFoundFlag = 0;
  var towar = {
    "id": 1
  };
  $("#data-table").dataTable().fnDestroy();
  $('#data-table').DataTable({
      "scrollX": true,
      "paging": false,
      data : jsonData,
      columns: [
        {
          "render": function() {
            return loopCnt+1;
          }
        },
          {
            "render": function(data, type, row) {
              var $select = $("<select class='form-control item'></select>", {
                    "id": row[0] + "start",
                    "value": data
              });
              var $emptyOption = $("<option></option>", {
                  "text": "Towar",
                  "value": 0,
                  "selected": "selected",
              });
              $select.append($emptyOption);
              $.each(items, function(key, value) {
                var $option = $("<option></option>", {
                    "text": value['towar_nazwa'],
                    "value": value['towar_id'],
                });
                if(row['towar_nazwa'] === value['towar_nazwa']) {
                  $option.attr("selected", "selected");
                  itemFoundFlag = 1;
                }
                $select.append($option);
              });
              loopCnt++;
              if(itemFoundFlag == 1) {
                $select.css('border', '2px solid green');
              } else {
                $select.css('border', '2px solid red');
              }
              itemFoundFlag = 0;
              return $select.prop("outerHTML");

            },
            "width": "15%",
          },
          {data: 'towar_nazwa'},
          {"render": function(data, type, row) {
              var $textInput = $("<input class='form-control' class ='amount' type='number' step='1' min = '1' value='" + row['ilosc'] +"'>");
              return $textInput.prop("outerHTML");
            },
            "width": "15%",
          },
          {"render": function(data, type, row) {
              var $textInput = $("<input class='form-control' class ='unit' type='text' value='" + row['jednostka'] +"'>");
              return $textInput.prop("outerHTML");
            },
           "width": "70px",
          },
          {
            "render": function(data, type, row) {
                var $textInput = $("<input class='form-control' class ='price' type='number' step='0.01' min = '0.01' value='" + row['cena'] +"'>");
                return $textInput.prop("outerHTML");
              },
            "width": "15%",
          },
          {
            "render": function() {
              return 0;
            },
            "width": "10%",
          },
          {
            "render": function() {
              return 0;
            },
            "width": "10%",
          },
          {
            "render": function() {
              return 0;
            },
            "width": "10%",
          },
          {
            "render": function() {
              return 0;
            },
            "width": "10%",
          },
          {data: 'edytuj'},
      ]
  });
  higlightEmptyItem();
  appendAddInvoice();
  updateItemPrices(getSelectedItems());
  calculateSummaryValues(getSelectedItems());
  $('.table-remove').bind( "click", function() {
    $(this).parents('tr').detach();
  });
}

function clearLoadedHeader() {
  $('#invoice_number').val('').siblings().removeClass('active');
  $('#invoice_date').val('').siblings().removeClass('active');
  $("#salesman").val($("#salesman option:first").val());
  $("#currency").val($("#currency option:first").val());
  $("#rate").val('').siblings().removeClass('active');
  $('#export_checkbox').prop('checked', false);
  $('#transfer_checkbox').prop('checked', false);
  $('#delivery_checkbox').prop('checked', false);
  $("#client").val($("#client option:first").val());
  $('#bonus').val(0);
  $("#country").val($("#country option:first").val());
  $("#voivodeship").val($("#voivodeship option:first").val());
  $("#region").val($("#region option:first").val());
  $('#comment').val('');
}

function setCurrentHeader(currentInvoiceHeaderObject) {
  $('#invoice_number').val(currentInvoiceHeaderObject.faktura_numer).siblings().addClass('active');
  $('#invoice_date').val(currentInvoiceHeaderObject.data_wystawienia).siblings().addClass('active');
  var salesmanNameExternal = currentInvoiceHeaderObject.sprzedawca;
  var salesmanObj = salesmen.find(obj => {
    return obj.uzytkownik_nazwa_zewnetrzna === salesmanNameExternal
  });
  if(salesmanObj && salesmanObj.uzytkownik_id != 'undefined') {
    $("#salesman").val(salesmanObj.uzytkownik_id);
  }
  $("#currency option").filter(function() {
    return $(this).text() == currentInvoiceHeaderObject.waluta_kod;
  }).prop('selected', true);
  $("#rate").val(currentInvoiceHeaderObject.kurs).siblings().addClass('active');
  if(currentInvoiceHeaderObject.kraj_kod == 'PL') {
    $('#export_checkbox').prop('checked', false);
  } else {
    $('#export_checkbox').prop('checked', true);
  }
  if(currentInvoiceHeaderObject.przelew == 1) {
    $('#transfer_checkbox').prop('checked', true);
  } else {
    $('#transfer_checkbox').prop('checked', false);
  }
  if(currentInvoiceHeaderObject.dostawa == 1) {
    $('#delivery_checkbox').prop('checked', true);
  } else {
    $('#delivery_checkbox').prop('checked', false);
  }
  var clientNameExternal = currentInvoiceHeaderObject.kontrahent;
  var clientObj = clients.find(obj => {
    return obj.kontrahent_nazwa_zewnetrzna === clientNameExternal
  });
  if(clientObj && clientObj.kontrahent_id != 'undefined') {
  //get default contry region voivodeship - change
    $("#client").val(clientObj.kontrahent_id).change();
  }
  var countryCode = currentInvoiceHeaderObject.kraj_kod;
  var countryObj = countries.find(obj => {
    return obj.kraj_kod === countryCode
  });
  if(countryObj && countryObj.kraj_id != 'undefined') {
    $("#country").val(countryObj.kraj_id);
  }
  var voivodeshipName = currentInvoiceHeaderObject.wojewodztwo_nazwa;
  var voivodeshipObj = voivodeships.find(obj => {
    return obj.wojewodztwo_nazwa_pelna === voivodeshipName
  });
  if(voivodeshipObj && voivodeshipObj.wojewodztwo_id != 'undefined') {
    $("#voivodeship").val(voivodeshipObj.wojewodztwo_id);
  }
}

function arrayLookup(array, prop, val) {
    for (var i = 0, len = array.length; i < len; i++) {
        if (array[i].hasOwnProperty(prop) && array[i][prop] === val) {
            return array[i];
        }
    }
    return null;
}

function getInvoiceNumbers(json) {
  invoiceNumbers = [];
  $.each(json, function(index, value) {
    if(!invoiceNumbers.includes(json[index]['faktura_numer'])) {
      invoiceNumbers.push(json[index]['faktura_numer']);
    }
  });
  return invoiceNumbers;
}

function getInvoiceHeaders(json) {
  invoiceNumbers = [];
  invoiceHeaders = [];
  $.each(json, function(index, value) {
    if(!invoiceNumbers.includes(json[index]['faktura_numer'])) {
      invoiceNumbers.push(json[index]['faktura_numer']);
      invoiceHeaders.push(json[index]);

      $.each(invoiceHeaders, function(key, innerValue) {
        if(INVOICE_ITEM_FIELDS.includes(key)) {
          delete invoiceHeaders[index][key];
        }
      });
    }
  });
  return invoiceHeaders;
}

function getInvoiceItems (json) {
  invoiceItems = [];

  $.each(json, function(index, value) {

    if(!invoiceItems.hasOwnProperty(json[index]['faktura_numer'])) {
      invoiceItems[json[index]['faktura_numer']] = [];
    }

    var clearedItemObject = b = $.extend( true, {}, json[index]);
    $.each(clearedItemObject, function(key, innerValue) {
      if(INVOICE_HEADER_FIELDS.includes(key)) {
        delete clearedItemObject[key];
      }
    });
    invoiceItems[json[index]['faktura_numer']].push(clearedItemObject);

  });
  return invoiceItems;
}

$(document).ready(function() {
  $('#recalculatePricesButton').prop("disabled", false);
  $('#invoiceItemRowAdd').on('click', 'i', () => {
    const clone = $('#data-table').find('tr').last().clone(true).removeClass('hide table-line');
    if (isNaN(parseInt($('#data-table tr:last td:first').html()))) {
      if ( $('#data-table').find('tr').length > 1) {
        $('#data-table tr:last').remove();
      }
      invoiceItemCreateManuallyFirstRecord();
      setDefaultvaluesForFirstRow($('#data-table tr:last'));
      higlightEmptyItem();
      $('.table-remove').bind( "click", function() {
        $(this).parents('tr').detach();
      });
    } else {
      nextVal = clone.find("td:first").html();
      setInvoiceItemRowValues(clone);
      $('#data-table').append(clone);
    }
  });
});

function invoiceItemCreateManuallyFirstRecord()
{
  var tr = document.createElement('tr');
  for(var i = 0; i < 11; i++) {
    var td = document.createElement('td');
    tr.append(td);
  }
  $('#data-table').append(tr);
}

function setDefaultvaluesForFirstRow(row)
{
  row.find("td:first").html('1');
  var select = $("<select class='form-control item'></select>");
  var emptyOption = $("<option></option>", {
      "text": "Towar",
      "value": 0,
      "selected": "selected",
  });
  select.append(emptyOption);
  $.each(items, function(key, value) {
    var option = $("<option></option>", {
        "text": value['towar_nazwa'],
        "value": value['towar_id'],
    });
    select.append(option);
  });
  row.find("td:nth-child(2)").append(select);
  row.find("td:nth-child(2) select").val(0).css("border", "2px solid red");
  row.find("td:nth-child(3)").html('Ręcznie dodany towar');
  var amountInput = $("<input class='form-control' class ='amount' type='number' step='1' value='0' min ='0'>");
  row.find("td:nth-child(4)").append(amountInput);
  var unitInput = $("<input class='form-control' class ='unit' type='text' value='szt'>");
  row.find("td:nth-child(5)").append(unitInput);
  var priceInput = $("<input class='form-control' class ='unit' type='number' step='0.01' value='0'  min ='0'>");
  row.find("td:nth-child(6)").append(priceInput);
  row.find("td:nth-child(7)").html(0);
  row.find("td:nth-child(8)").html(0);
  row.find("td:nth-child(9)").html(0);
  row.find("td:nth-child(10)").html(0);
  var deleteButton = "<button type='button' class='table-remove btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light'>Usuń</button>";
  row.find("td:nth-child(11)").append(deleteButton);
}

function setInvoiceItemRowValues(row)
{
  nextItemId = row.find("td:first").html();
  row.find("td:first").html(parseInt(nextItemId) + 1);
  row.find("td:nth-child(2) select").val(0).css("border", "2px solid red");
  row.find("td:nth-child(3)").html('Ręcznie dodany towar');
  row.find("td:nth-child(4) input").val(0);
  row.find("td:nth-child(5) input").val('szt');
  row.find("td:nth-child(6) input").val(0);
  row.find("td:nth-child(7)").html(0);
  row.find("td:nth-child(8)").html(0);
  row.find("td:nth-child(9)").html(0);
  row.find("td:nth-child(10)").html(0);
}

function getSelectedItems() {
  var itemsToImport = [];
  var iterator = 0;
  $('#data-table tbody tr td:nth-child(2) select').children("option:selected").each( function(){
    itemsToImport.push({
      'itemId': $(this).val(),
      'amount': $('#data-table tbody tr td:nth-child(4) input')[iterator].value,
      'price': $('#data-table tbody tr td:nth-child(6) input')[iterator].value,
      'item': items.find(obj => {
                return obj.towar_id == $(this).val()
              })
    });
    iterator++;
  });
  return itemsToImport;
}

function updateItemPrices(items) {
  $.each(items, function(index, value) {
    if(items[index].itemId != 0) {
      updateItemPricesRow(items[index], index);
    }
  });

}

function calculateSummaryValues(items) {
  var summaryPricesZero = 0;
  var summaryValue = 0;
  var summaryMargin = 0;
  $.each(items, function(index, value) {
    if(items[index].itemId != 0) {
      summaryPricesZero += parseFloat($('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html().replace(/ /g,''));
      summaryValue += parseFloat($('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(8)').html().replace(/ /g,''));
      summaryMargin += parseFloat($('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(9)').html().replace(/ /g,''));
    }
  })
  //$('tfoot:nth-child(1) tr th:nth-child(2)').html(beautifyNumberPrint(summaryPricesZero.toFixed(2)));
  $('tfoot:nth-child(1) tr th:nth-child(2)').html(beautifyNumberPrint(summaryValue.toFixed(2)));
  $('tfoot:nth-child(1) tr th:nth-child(3)').html(beautifyNumberPrint(summaryMargin.toFixed(2)));
  !isNaN(((summaryMargin / summaryValue) * 100).toFixed(2)) ? $('tfoot:nth-child(1) tr th:nth-child(4)').html(beautifyNumberPrint(((summaryMargin / summaryValue) * 100).toFixed(2))) : $('tfoot:nth-child(1) tr th:nth-child(4)').html(0);


}

function updateItemPricesRow(itemObj, index) {
  var priceZero = 0;
  if($("#transfer_checkbox").is(":checked") == false && $("#delivery_checkbox").is(":checked") == false) {
    priceZero = ((itemObj.item.cena_go * 100) / (100 - $('#bonus').val())).toFixed(2);
    $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html(priceZero);
  } else if ($("#transfer_checkbox").is(":checked") == true && $("#delivery_checkbox").is(":checked") == false) {
    priceZero = ((itemObj.item.cena_po * 100) / (100 - $('#bonus').val())).toFixed(2);
    $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html(priceZero);
  } else if ($("#transfer_checkbox").is(":checked") == false && $("#delivery_checkbox").is(":checked") == true) {
    priceZero = ((itemObj.item.cena_gd * 100) / (100 - $('#bonus').val())).toFixed(2);
    $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html(priceZero);
  } else if ($("#transfer_checkbox").is(":checked") == true && $("#delivery_checkbox").is(":checked") == true) {
    priceZero = ((itemObj.item.cena_pd * 100) / (100 - $('#bonus').val())).toFixed(2);
    $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html(priceZero);
  }
  var rate = $("#rate").val() ? $("#rate").val() : 1;
  $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(8)').html($.fn.dataTable.render.number( ' ', '.', 2).display((itemObj.amount * itemObj.price * rate).toFixed(2)) );
  var margin = (itemObj.amount * itemObj.price * rate) - (itemObj.amount * priceZero);
  $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(9)').html($.fn.dataTable.render.number( ' ', '.', 2).display(margin.toFixed(2)) );
  $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(10)').html(((margin/(itemObj.amount * itemObj.price * rate))*100).toFixed(1));

}

function importRegionFilter() {
  $.ajax({
      url: "./invoice_import_filters.php",
      type: 'post',
      global: false,
      data: {type:'region'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var region_id = response[i]['region_id'];
              var region_name = response[i]['region_nazwa'];

              $("#region").append("<option value='"+region_id+"'>"+region_name+"</option>");
          }
      }
  });
}

function importCountryFilter() {
  $.ajax({
      url: "./invoice_import_filters.php",
      type: 'post',
      global: false,
      data: {type: 'country'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          countries = response;
          for( var i = 0; i < len; i++){
              var country_id = response[i]['kraj_id'];
              var country_name = response[i]['kraj_nazwa'];

              $("#country").append("<option value='"+country_id+"'>"+country_name+"</option>");
          }
      }
  });
}

function importVoivodeshipFilter() {
  $.ajax({
      url: "./invoice_import_filters.php",
      type: 'post',
      global: false,
      data: {type:'voivodeship'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          voivodeships = response;
          for( var i = 0; i<len; i++){
              var voivodeship_id = response[i]['wojewodztwo_id'];
              var voivodeship_name = response[i]['wojewodztwo_nazwa'];

              $("#voivodeship").append("<option value='"+voivodeship_id+"'>"+voivodeship_name+"</option>");
          }
      }
  });
}

function importClientFilter() {
  $.ajax({
      url: "./invoice_import_filters.php",
      type: 'post',
      global: false,
      data: {type:'client_active_only'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          clients = response;
          for( var i = 0; i<len; i++){
              var client_id = response[i]['kontrahent_id'];
              var client_name = response[i]['kontrahent_nazwa'];

              $("#client").append("<option value='"+client_id+"'>"+client_name+"</option>");
          }
      }
  });
}

function importSalesmanFilter() {
  $.ajax({
      url: "./invoice_import_filters.php",
      type: 'post',
      global: false,
      data: {type:'salesman_active_only'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          salesmen = response;
          for( var i = 0; i<len; i++){
              var salesman_id = response[i]['uzytkownik_id'];
              var salesman_name = response[i]['uzytkownik_nazwa'];

              $("#salesman").append("<option value='"+salesman_id+"'>"+salesman_name+"</option>");
          }
      }
  });
}

function importItemFilter() {
  $.ajax({
      url: "./invoice_import_filters.php",
      type: 'post',
      global: false,
      data: {type:'item'},
      dataType: 'json',
      success:function(response){
          items = response;
          var len = response.length;
          for( var i = 0; i<len; i++){
              var item_id = response[i]['towar_id'];
              var item_name = response[i]['towar_nazwa'];
              $("#item_calculator_select").append("<option value='"+item_id+"'>"+item_name+"</option>");
          }
      }
  });
}

function importFilters() {
  importRegionFilter();
  importCountryFilter();
  importVoivodeshipFilter();
  importClientFilter();
  importSalesmanFilter();
  importItemFilter();
}

$(document).ready(function(){
  $('#show_price_calculator').click(function() {
    $('#price_calculator_div').toggle();
  });
});

$(document).ready(function(){
  $('#item_calculator_select').change(function() {
    getCalculatedItemPrices();
  });
  $('#calculator_amount').change(function() {
    getCalculatedItemPrices();
  });
});

function getCalculatedItemPrices() {
  $.ajax({
    method: "POST",
    global: false,
    data: {
        action : "getItemPrices",
        item : $('#item_calculator_select').children(":selected").val(),
        amount: $('#calculator_amount').val()
    },
    dataType: 'json',
    url: "./invoice_import_actions.php",
    success: function (data) {
        $('#price_go_calculator').val(data[0]['cena_go']);
        $('#price_po_calculator').val(data[0]['cena_po']);
        $('#price_gd_calculator').val(data[0]['cena_gd']);
        $('#price_pd_calculator').val(data[0]['cena_pd']);
    }
  });
}

function higlightEmptyItem() {
  $("select.item").change(function(){
      if($(this).children("option:selected").val() == 0) {
        $(this).css('border', '2px solid red');
      } else {
        $(this).css('border', '2px solid green');
      }
  });
}

function appendAddInvoice() {
  $("#import_invoice_div").empty();
  $("#import_invoice_div").append("<button id ='addInoviceBtn' class='btn btn-success'>Dodaj fakturę</button>");
  $('#addInoviceBtn').bind( "click", function() {
    addInvoice();
  });
  if($('.invoiceToImport').length > 0) {
    $("#import_invoice_div").append("<button id ='getNextInvoice' class='btn btn-info'>Załaduj kolejną <i class='fas fa-arrow-right'></i></button>");
    $('#getNextInvoice').bind( "click", function() {
      loadNextInvoice();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
}

function addInvoice() {
  addedItems = 0;
  var itemsToAdd = $('#data-table tbody tr').length;
  var invoice_header = getInviceHeader();
  var checkInvoiceHeader = checkInvoiceHeaderData(invoice_header);
  var checkInvoiceItems = checkInvoiceItemsData();
  showErrorsAlert(checkInvoiceHeader, checkInvoiceItems);
  var invoiceId  = 0;
  if(checkInvoiceHeader == true && checkInvoiceItems == true) {
    $('#addInoviceBtn').attr('disabled', true);
    updateItemPrices(getSelectedItems());
    $.ajax({
        url: "./add_invoice.php",
        method: "POST",
        data: {data: JSON.stringify(invoice_header)},
        dataType: 'json',
        success: function (data) {
            if(data.success == 0) {
              $('#invoice_number').css('border-color', 'red');
              $('#invoice_add_error').append('<br>Istnieje faktura o takim numerze');
              $('#invoice_add_error').prop("hidden", false);
              timer = setTimeout(function() {
                $('#invoice_number').css('border-color', '');
                $('#invoice_add_error').text('');
                $('#invoice_add_error').prop("hidden", true);
              }, 5000);
              $('#addInoviceBtn').attr('disabled', false);
            } else {
              invoiceId = data.faktura_id;
              if(invoiceId != 0)
              {
                addInvoiceItems(invoiceId);
                $('#addInoviceBtn').attr('disabled', false);
                if(addedItems == itemsToAdd) {
                  markInvoiceAsImported(invoice_header.invoice_number);
                  loadNextInvoice();
                  window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                  $('#invoice_number').css('border-color', 'red');
                  $('#invoice_add_error').append('<br>Błąd podczas dodawania pozycji faktury');
                  $('#invoice_add_error').prop("hidden", false);
                  timer = setTimeout(function() {
                    $('#invoice_number').css('border-color', '');
                    $('#invoice_add_error').text('');
                    $('#invoice_add_error').prop("hidden", true);
                  }, 5000);
                }
                if(invoiceHeaders.length == $('.invoiceToImport:disabled').length && invoiceHeaders.length > 0) {
                  reloadScreen();
                }
              }
              $('#addInoviceBtn').attr('disabled', false);
            }
        }
    })
  }
}

function addInvoiceItems(invoiceId) {
  var items = $('#data-table tbody tr');
  $.each(items, function(indexTr, tr){
      $.ajax({
          url: "./add_invoice_item.php",
          method: "POST",
          async: false,
          data: {
            data: JSON.stringify(getInviceItemData(tr)),
            invoice_id: invoiceId
          },
          dataType: 'json',
          success: function (data) {
            if(data.success == 0) {
              console.log('coś poszlo nie tak');
            } else {
              addedItems++;
            }
          }
      })
  });
  return 0;
}

function reloadScreen() {
  $('#invoice_add_success').prop("hidden", false);
  timer = setTimeout(function() {
    $('#invoice_add_success').prop("hidden", true);
    window.location.reload(true);
  }, 3000);
}

function showErrorsAlert(errorCheckInvoiceHeader, errorCheckInvoiceItems) {
  if (!errorCheckInvoiceHeader || !errorCheckInvoiceItems) {
      $('#invoice_add_error').prop("hidden", false);
      timer = setTimeout(function() {
        $('#invoice_add_error').prop("hidden", true);
      }, 5000);
  }
}

function checkInvoiceItemsData() {
  var success = true;
  var items = $('#data-table tbody tr');
  var errorItems = [];
  if (items.length == 0) {
    $('#invoice_add_error').append('<br>Dodaj pozycje faktury');
    success = false;
  }
  $.each(items, function(indexTr, tr) {
    var cells = $("td", tr);
    cells.each(function(index, td){
      if(index == 0 &&  isNaN(parseInt($(this).html())) )
      {
        $('#invoice_add_error').append('<br>Dodaj pozycje faktury');
        success = false;
      }
      if(index == 1 && $("select", td).val() == 0) {
        if(errorItems.indexOf(indexTr + 1) === -1) {
          errorItems.push(indexTr + 1)
        }
        success = false;
      } else if ( (index == 3 || index == 5) && ( $("input", td).val() <= 0 || !$("input", td).val()) ) {
        if(errorItems.indexOf(indexTr + 1) === -1) {
          errorItems.push(indexTr + 1)
        }
        success = false;
        highlightErrorTableValue($("input", td));
      } else if ( (index == 6 || index == 7) && ( $(this).html() <= 0 || $(this).html() == '' ) ) {
        if(errorItems.indexOf(indexTr + 1) === -1) {
          errorItems.push(indexTr + 1)
        }
        success = false;
        highlightErrorTableValue($(this));
      }
    })
  });
  $.each(errorItems, function(index, item){
    $('#invoice_add_error').append('<br>Uzupełnij dane o towarze '+ (item));
  });
  return success;
}

function highlightErrorTableValue (element) {
  element.css('border-bottom', '3px solid red');
  timer = setTimeout(function() {
    $(element).css('border', '');
  }, 5000);
}

function getInviceItemData(row) {
    var invoice_item = {};
    var rate = $("#rate").val() ? $("#rate").val() : 1;
    invoice_item.item_index =  $("td:nth-child(1)", row).html();
    invoice_item.item_id = $("td:nth-child(2) select", row).children("option:selected").val();
    invoice_item.item_amount = $("td:nth-child(4) input", row).val();
    invoice_item.item_unit = $("td:nth-child(5) input", row).val();
    invoice_item.item_price = $("td:nth-child(6) input", row).val();
    invoice_item.item_price_zero = $("td:nth-child(7)", row).html();
    invoice_item.item_value = $("td:nth-child(8)", row).html().replace(/\s/g, '');
    invoice_item.item_margin = $("td:nth-child(9)", row).html().replace(/\s/g, '');
    invoice_item.item_percent = (invoice_item.item_margin / (invoice_item.item_amount * invoice_item.item_price * rate)).toFixed(6);
    return invoice_item;
}

function getInviceHeader() {
    var invoice_header = {};
    invoice_header.invoice_number = $("#invoice_number").val();
    invoice_header.invoice_date = $("#invoice_date").val();
    invoice_header.salesman = $("#salesman").children("option:selected").val();
    invoice_header.currency = $("#currency").children("option:selected").val();
    invoice_header.rate = $("#rate").val();
    invoice_header.export = $("#export_checkbox").is(":checked") == true ? 1 : 0;
    invoice_header.money_transfer = $("#transfer_checkbox").is(":checked") == true ? 1 : 0;
    invoice_header.delivery = $("#delivery_checkbox").is(":checked") == true ? 1 : 0;
    invoice_header.client = $("#client").children("option:selected").val();
    invoice_header.country = $("#country").children("option:selected").val();
    invoice_header.voivodeship = $("#voivodeship").children("option:selected").val();
    invoice_header.region = $("#region").children("option:selected").val();
    invoice_header.comment =  $("#comment").val();
    invoice_header.bonus = $("#bonus").val();
    return invoice_header;
}

function checkInvoiceHeaderData(invoice_header) {
    $('#invoice_add_error').text('');
    var success = true;

    if (!invoice_header.invoice_number) {
      success = false;
      $('#invoice_number').css('border-color', 'red');
      $('#invoice_add_error').text('Wypełnij numer faktury');
      timer9 = setTimeout(function() {
        $('#invoice_number').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    if (!invoice_header.invoice_date) {
      success = false;
      $('#invoice_date').css('border-color', 'red');
      $('#invoice_add_error').append('<br>Wypełnij datę faktury');
      timer = setTimeout(function() {
        $('#invoice_date').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    if (isNaN(invoice_header.salesman)) {
      success = false;
      $('#salesman').css('border-color', 'red');
      $('#invoice_add_error').append('<br>Dodaj informację o handlowcu');
      timer2 = setTimeout(function() {
        $('#salesman').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    if (isNaN(invoice_header.currency)) {
      success = false;
      $('#currency').css('border-color', 'red');
      $('#invoice_add_error').append('<br>Dodaj informację o walucie');
      timer3 = setTimeout(function() {
        $('#currency').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    if (isNaN(invoice_header.rate) || !invoice_header.rate) {
      success = false;
      $('#rate').css('border-color', 'red');
      $('#invoice_add_error').append('<br>Dodaj informację o kursie');
      timer4 = setTimeout(function() {
        $('#rate').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    if (isNaN(invoice_header.client)) {
      success = false;
      $('#client').css('border-color', 'red');
      $('#invoice_add_error').append('<br>Dodaj informację o kliencie');
      timer5 = setTimeout(function() {
        $('#client').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    if (isNaN(invoice_header.country)) {
      success = false;
      $('#country').css('border-color', 'red');
      $('#invoice_add_error').append('<br>Dodaj informację o kraju');
      timer6 = setTimeout(function() {
        $('#country').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    if (isNaN(invoice_header.voivodeship)) {
      success = false;
      $('#voivodeship').css('border-color', 'red');
      $('#invoice_add_error').append('<br>Dodaj informację o województwie');
      timer7 = setTimeout(function() {
        $('#voivodeship').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    if (isNaN(invoice_header.region)) {
      success = false;
      $('#region').css('border-color', 'red');
      $('#invoice_add_error').append('<br>Dodaj informację o regionie');
      timer8 = setTimeout(function() {
        $('#region').css('border-color', '');
        $('#invoice_add_error').text('');
      }, 5000);
    }

    return success;
}

function beautifyNumberPrint(x) {
    var parts = x.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    return parts.join(".");
}
