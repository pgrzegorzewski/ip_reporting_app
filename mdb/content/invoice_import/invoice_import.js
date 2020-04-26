var items = [];

$(document).ready(function(){
    appendAddInvoice() ; //to be deleted
    $('#data-table').DataTable();

    $.ajax({
        url: "./invoice_import_filters.php",
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
        }
    });

    $.ajax({
        url: "./invoice_import_filters.php",
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
        }
    });

    $.ajax({
        url: "./invoice_import_filters.php",
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
        }
    });

    $.ajax({
        url: "./invoice_import_filters.php",
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
        }
    });

    $.ajax({
        url: "./invoice_import_filters.php",
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
        }
    });

    $.ajax({
        url: "./invoice_import_filters.php",
        type: 'post',
        data: {type:'item'},
        dataType: 'json',
        success:function(response){
            items = response;
        }
    });

});

$(document).ready(function(){
   $('#upload_csv').on('submit', function (event) {
        $('#import_label').text('');
        $('#import_label').addClass('spinner-border spinner-border-sm text-primary');

        event.preventDefault();
        $.ajax({
            url: "./import_csv.php",
            method: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (jsonData) {

                $('#import_label').removeClass('spinner-border spinner-border-sm text-primary');
                $('#import_label').text('Wybierz plik');

                var loopCnt = 0;
                var itemFoundFlag = 0;
                var towar = {
                  "id": 1
                };
                $("#data-table").dataTable().fnDestroy();
                $('#data-table').DataTable({
                    "scrollX": true,
                    data : jsonData,
                    columns: [
                        {data: 'lp'},
                        {
                          "render": function(data, type, row) {
                            var $select = $("<select class='form-control item'></select>", {
                                  "id": row[0] + "start",
                                  "value": data
                            });
                            var $emptyOption = $("<option></option>", {
                                "text": "Towar",
                                "value": 0
                            });
                            $select.append($emptyOption);
                            $.each(items, function(key, value) {
                              var $option = $("<option></option>", {
                                  "text": value['towar_nazwa'],
                                  "value": value['towar_id']
                              });
                              if(row['towar'] === value['towar_nazwa']) {
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

                          }
                        },
                        {data: 'towar'},
                        {"render": function(data, type, row) {
                          var $textInput = $("<input class='form-control' class ='amount' type='text' value='" + row['ilosc'] +"'>");
                          return $textInput.prop("outerHTML");

                          }
                        },
                        {"render": function(data, type, row) {
                          var $textInput = $("<input class='form-control' class ='unit' type='text' value='" + row['jm'] +"'>");
                          return $textInput.prop("outerHTML");

                          }
                        },
                        {"render": function(data, type, row) {
                          var $textInput = $("<input class='form-control' class ='price' type='text' value='" + row['cena'] +"'>");
                          return $textInput.prop("outerHTML");

                          }
                        },
                        {"render": function() {
                          return towar.id ;
                          }
                        },
                        {"render": function() {
                          return 0;
                          }
                        },
                        {"render": function() {
                          return 0;
                          }
                        },
                        {"render": function() {
                          return 0;
                          }
                        },
                        {data: 'edytuj'},
                    ]
                });
                higlightEmptyItem();
                appendAddInvoice();

                $('.table-remove').bind( "click", function() {
                  $(this).parents('tr').detach();
                });
            },
            error : function() {
              $('#import_label').removeClass('spinner-border spinner-border-sm text-primary');
              $('#import_label').text('Wybierz plik');
            },
        })
   })

});


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
  console.log(JSON.stringify(invoice_header));
  var checkInvoiceHeader = chechInvoiceHeader(invoice_header);

  if(checkInvoiceHeader == true) {
    $.ajax({
        url: "./add_invoice.php",
        method: "POST",
        data: {data: JSON.stringify(invoice_header)},
        dataType: 'json',
        success: function (data) {
            console.log(data.success);
            if(data.success == 0) {
              $('#invoice_number').css('border-color', 'red');
              $('#invoice_add_error').append('<br>Istnieje faktura o takim numerze');
              timer = setTimeout(function() {
                $('#invoice_number').css('border-color', '');
                $('#invoice_add_error').text('');
              }, 5000);
            } else {
              $('#invoice_add_error').css('color', 'green');
              $('#invoice_add_error').append('<br>Pomyślnie dodano fakturę!');
              timer = setTimeout(function() {
                $('#invoice_add_error').text('');
                $('#invoice_add_error').css('color', 'red');
              }, 5000);
            }
        }
    })
  }
}

function getInviceHeader() {
    var invoice_header = {};

    invoice_header.invoice_number = $("#invoice_number").val();
    invoice_header.invoice_date = $("#invoice_date").val();
    invoice_header.salesman = $("#salesman").children("option:selected").val();
    invoice_header.currency = $("#currency").children("option:selected").val();
    invoice_header.rate = $("#rate").val();
    invoice_header.export = $("#export_checkbox").is(":checked") == true ? 1 : 0;
    invoice_header.money_transfer = $("#export_checkbox").is(":checked") == true ? 1 : 0;
    invoice_header.delivery = $("#delivery_checkbox").is(":checked") == true ? 1 : 0;
    invoice_header.client = $("#client").children("option:selected").val();
    invoice_header.country = $("#country").children("option:selected").val();
    invoice_header.voivodeship = $("#voivodeship").children("option:selected").val();
    invoice_header.region = $("#region").children("option:selected").val();
    invoice_header.comment =  $("#comment").val();
    return invoice_header;
}

function chechInvoiceHeader(invoice_header) {
    $('#invoice_add_error').text('');
    var success = true;

    if (!invoice_header.invoice_date) {
      success = false;
      $('#invoice_date').css('border-color', 'red');
      $('#invoice_add_error').text('Wypełnij datę faktury');
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
