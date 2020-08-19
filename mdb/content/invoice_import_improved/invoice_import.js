var items = [];
var INVOICE_HEADER_FIELDS = ['faktura_numer', 'data_wystawienia', 'kontrahent', 'waluta_kod', 'kurs', 'kraj_kod', 'wojewodztwo_nazwa', 'dostawa', 'przelew', 'wartosc_faktury'];
var INVOICE_ITEM_FIELDS = ['towar', 'nazwa towaru', 'jm', 'ilość', 'wartość pozycji', 'cena'];
var formatter = new Intl.NumberFormat('ru-RU', {
  style: 'currency',
  currency: 'PLN',
});
var clients = [];

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
  $('#recalculatePricesButton').click(function(){
    updateItemPrices(getSelectedItems());
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
        var defaultVal = 0;
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
              console.log(jsonData);
              console.log(getInvoiceNumbers(jsonData));
              console.log(getInvoiceHeaders(jsonData));
              $invoiceNumbers = $.uniqueSort(getInvoiceNumbers(jsonData));

              $.each($invoiceNumbers, function( index, value ){
                  $('#import_invoice_numbers').css('display', 'block');
                  $('#import_invoice_numbers').append("<button class='btn btn-info'>"+ value+ "</button>");
              });


                 $('#import_label').removeClass('spinner-border spinner-border-sm text-primary');
                 $('#import_label').text('Wybierz plik');
                // $('#recalculatePricesButton').prop("disabled", false);
                //
                // var loopCnt = 0;
                // var itemFoundFlag = 0;
                // var towar = {
                //   "id": 1
                // };
                // $("#data-table").dataTable().fnDestroy();
                // $('#data-table').DataTable({
                //     "scrollX": true,
                //     "paging": false,
                //     data : jsonData,
                //     columns: [
                //         {data: 'lp'},
                //         {
                //           "render": function(data, type, row) {
                //             var $select = $("<select class='form-control item'></select>", {
                //                   "id": row[0] + "start",
                //                   "value": data
                //             });
                //             var $emptyOption = $("<option></option>", {
                //                 "text": "Towar",
                //                 "value": 0,
                //                 "selected": "selected",
                //             });
                //             $select.append($emptyOption);
                //             $.each(items, function(key, value) {
                //               var $option = $("<option></option>", {
                //                   "text": value['towar_nazwa'],
                //                   "value": value['towar_id'],
                //               });
                //               if(row['towar'] === value['towar_nazwa']) {
                //                 $option.attr("selected", "selected");
                //                 itemFoundFlag = 1;
                //               }
                //               $select.append($option);
                //             });
                //             loopCnt++;
                //             if(itemFoundFlag == 1) {
                //               $select.css('border', '2px solid green');
                //             } else {
                //               $select.css('border', '2px solid red');
                //             }
                //             itemFoundFlag = 0;
                //             return $select.prop("outerHTML");
                //
                //           },
                //           "width": "15%",
                //         },
                //         {data: 'towar'},
                //         {"render": function(data, type, row) {
                //             var $textInput = $("<input class='form-control' class ='amount' type='number' step='1' min = '1' value='" + row['ilosc'] +"'>");
                //             return $textInput.prop("outerHTML");
                //           },
                //           "width": "15%",
                //         },
                //         {"render": function(data, type, row) {
                //             var $textInput = $("<input class='form-control' class ='unit' type='text' value='" + row['jm'] +"'>");
                //             return $textInput.prop("outerHTML");
                //           },
                //          "width": "70px",
                //         },
                //         {
                //           "render": function(data, type, row) {
                //               var $textInput = $("<input class='form-control' class ='price' type='number' step='0.01' min = '0.01' value='" + row['cena'] +"'>");
                //               return $textInput.prop("outerHTML");
                //             },
                //           "width": "15%",
                //         },
                //         {
                //           "render": function() {
                //             return 0;
                //           },
                //           "width": "10%",
                //         },
                //         {
                //           "render": function() {
                //             return 0;
                //           },
                //           "width": "10%",
                //         },
                //         {
                //           "render": function() {
                //             return 0;
                //           },
                //           "width": "10%",
                //         },
                //         {
                //           "render": function() {
                //             return 0;
                //           },
                //           "width": "10%",
                //         },
                //         {data: 'edytuj'},
                //     ]
                // });
                // higlightEmptyItem();
                // appendAddInvoice();
                // updateItemPrices(getSelectedItems());
                // calculateSummaryValues(getSelectedItems());
                // $('.table-remove').bind( "click", function() {
                //   $(this).parents('tr').detach();
                // });
            },
            error : function() {
              $('#import_label').removeClass('spinner-border spinner-border-sm text-primary');
              $('#import_label').text('Wybierz plik');
            },
        })
   })

});

function getInvoiceNumbers(json) {
  invoiceNumbers = [];
  $.each(json, function(index, value) {
    console.log(json[index]['faktura_numer']);
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
  var items = [];
  var iterator = 0;
  $('#data-table tbody tr td:nth-child(2) select').children("option:selected").each( function(){
    items.push({itemId: $(this).val(), amount: $('#data-table tbody tr td:nth-child(4) input')[iterator].value, 'price': $('#data-table tbody tr td:nth-child(6) input')[iterator].value} );
    iterator++;
  });
  return items;
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
  $.ajax({
    method: "POST",
    global: false,
    async: false,
    data: {
        action : "getItemPrices",
        item : itemObj.itemId,
        amount: 1
    },
    dataType: 'json',
    url: "./invoice_import_actions.php",
    success: function (data) {
      var priceZero = 0;
      if($("#transfer_checkbox").is(":checked") == false && $("#delivery_checkbox").is(":checked") == false) {
        priceZero = ((data[0]['cena_go'] * 100) / (100 - $('#bonus').val())).toFixed(2);
        $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html(priceZero);
      } else if ($("#transfer_checkbox").is(":checked") == true && $("#delivery_checkbox").is(":checked") == false) {
        priceZero = ((data[0]['cena_po'] * 100) / (100 - $('#bonus').val())).toFixed(2);
        $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html(priceZero);
      } else if ($("#transfer_checkbox").is(":checked") == false && $("#delivery_checkbox").is(":checked") == true) {
        priceZero = ((data[0]['cena_gd'] * 100) / (100 - $('#bonus').val())).toFixed(2);
        $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html(priceZero);
      } else if ($("#transfer_checkbox").is(":checked") == true && $("#delivery_checkbox").is(":checked") == true) {
        priceZero = ((data[0]['cena_pd'] * 100) / (100 - $('#bonus').val())).toFixed(2);
        $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(7)').html(priceZero);
      }
      var rate = $("#rate").val() ? $("#rate").val() : 1;
      $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(8)').html($.fn.dataTable.render.number( ' ', '.', 2).display((itemObj.amount * itemObj.price * rate).toFixed(2)) );
      var margin = (itemObj.amount * itemObj.price * rate) - (itemObj.amount * priceZero);
      $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(9)').html($.fn.dataTable.render.number( ' ', '.', 2).display(margin.toFixed(2)) );
      $('#data-table tbody tr:nth-child(' + (index + 1) + ') td:nth-child(10)').html(((margin/(itemObj.amount * itemObj.price * rate))*100).toFixed(1));
      calculateSummaryValues(getSelectedItems());
    }
  });
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
}

function addInvoice() {
  var invoice_header = getInviceHeader();
  var checkInvoiceHeader = checkInvoiceHeaderData(invoice_header);
  var checkInvoiceItems = checkInvoiceItemsData();
  showErrorsAlert(checkInvoiceHeader, checkInvoiceItems);
  var invoiceId  = 0;
  if(checkInvoiceHeader == true && checkInvoiceItems == true) {
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
            } else {
              invoiceId = data.faktura_id;
              if(invoiceId != 0)
              {
                addInvoiceItems(invoiceId);
              }
            }
        }
    })
  }
}

function addInvoiceItems(invoiceId)
{
  var items = $('#data-table tbody tr');
  $.each(items, function(indexTr, tr){
      $.ajax({
          url: "./add_invoice_item.php",
          method: "POST",
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

$(document).ajaxStop(function(){
  var items = $('#data-table tbody tr');
  if(addedItems == items.length) {
    $('#invoice_add_success').prop("hidden", false);
    timer = setTimeout(function() {
      $('#invoice_add_success').prop("hidden", true);
      window.location.reload(true);
    }, 3000);
  } else {
    $('#invoice_add_error').append('<br>Błąd podczas dodawania faktury');
    $('#invoice_add_error').prop("hidden", false);
    timer = setTimeout(function() {
      $('#invoice_add_error').text('');
      $('#invoice_add_error').prop("hidden", true);
    }, 5000);
  }
  addedItems = 0;
});

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
    invoice_item.item_index =  $("td:nth-child(1)", row).html();
    invoice_item.item_id = $("td:nth-child(2) select", row).children("option:selected").val();
    invoice_item.item_amount = $("td:nth-child(4) input", row).val();
    invoice_item.item_unit = $("td:nth-child(5) input", row).val();
    invoice_item.item_price = $("td:nth-child(6) input", row).val();
    invoice_item.item_price_zero = $("td:nth-child(7)", row).html();
    invoice_item.item_value = $("td:nth-child(8)", row).html().replace(/\s/g, '');
    invoice_item.item_margin = $("td:nth-child(9)", row).html().replace(/\s/g, '');
    invoice_item.item_percent = (invoice_item.item_margin / (invoice_item.item_amount * invoice_item.item_price)).toFixed(6);
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
