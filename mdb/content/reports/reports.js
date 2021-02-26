var today = new Date();
var expiry = new Date(today.getTime() + 30 * 24 * 3600 * 1000);
var TIMER_SECONDS = 3000;

$(document).on('click', '#late_pay_table_button', function() {
    $('#late_pay_div').toggle();
    getLatPayValues();
});

$(document).on('click', '.user_late_pay_update', function() {
    $id = this.id;
    $rowIndex = ($(this).closest('td').parent()[0].sectionRowIndex);
    $latePayValue = $('#late_pay_datatable tr:eq(' + ($rowIndex + 1) + ') td:eq(2) input').val();

    $userId = $id.split('-')[0];
    $latePayMonth = $id.split('-')[1];
    $latePayYear = $id.split('-')[2];

    $.ajax({
       method: "POST",
       data: {action : "updateUserLatePayValue", userId : $userId, latePayYear : parseInt($latePayYear), latePayMonth: parseInt($latePayMonth), latePayValue: $latePayValue},
       dataType: 'json',
       url: "../admin/user/user_actions.php",
       success: function() {
       },
    });
    $('#late_pay_datatable tr:eq(' + ($rowIndex + 1) + ') td:eq(3) button').addClass('btn-success').removeClass('btn-info');
    timer = setTimeout(function() {
      $('#late_pay_datatable tr:eq(' + ($rowIndex + 1) + ') td:eq(3) button').addClass('btn-info').removeClass('btn-success');
    }, TIMER_SECONDS);
});

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
              $("#item").append("<option value='"+item_id+"'>"+item_name+"</option>");
          }
          if(getCookie('item') != 'null') {
            $('#item').val(getCookie('item'));
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
              var region_nazwa = response[i]['region_nazwa'];
              $("#region").append("<option value='"+region_id+"'>"+region_nazwa+"</option>");
          }
          if(getCookie('region') != 'null') {
            $('#region').val(getCookie('region'));
          }
      }
  });
}

$(document).on('click', '#region_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      getRegionChartTemplate();
      $('#region_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#region_summary_data_refresh_span').text('');

      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},
         dataType: 'json',
         url: "./summary_by_region_report.php",
         success: function (data) {
             $("#data-table").dataTable().fnDestroy();
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 dom: 'Bfrtip',
                 buttons: [
                  {
                    extend: "excelHtml5",
                    text: "Excel",
                    exportOptions: {
                      format: {
                        body: function ( data, row, column, node ) {
                          if (typeof data !== 'undefined') {
                              if (data !== null) {
                                  if (column === 2 || column === 3 || column > 4) {
                                        data = data.replace( /[\,]/g, "." );
                                      data = data.replace( /[^\d.-]/g, "" );
                                      return data;                                
                                  }
                              }
                          }
                          return data;
                        }
                      }
                    }
                  },
                  {
                    extend:'csvHtml5'
                  }   
                 ],
                 data : data,
                 columns: [
                     {data: 'region_nazwa'},
                     {data: 'region_kod'},
                     {
                        data: 'suma_wartosci',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                     },
                     {
                        data: 'suma_marz',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                     },
                     {
                       data: 'procent',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     }
                 ],
                 footerCallback: function ( row, data, start, end, display ) {
                     var api = this.api(), data;
                     var intVal = function ( i ) {
                         return typeof i === 'string' ?
                             i.replace(/[\$,]/g, '') * 1 :
                             typeof i === 'number' ?
                                 i : 0;
                     };

                     totalValue = api
                         .column(2, { search: 'applied' })
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                     pageTotalValue = api
                         .column( 2, { page: 'current'} )
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                       totalMargin = api
                           .column(3, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalmargin = api
                           .column( 3, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                     $( api.column( 2 ).footer() ).html(
                        'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display( pageTotalValue.toFixed(2)) + '<br> suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display( totalValue.toFixed(3))
                     );

                     $( api.column( 3).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalmargin.toFixed(2)) + '<br> suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                     );

                     $( api.column( 4 ).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalmargin / pageTotalValue) * 100).toFixed(2)) + '%  <br>całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin / totalValue) * 100).toFixed(2)) + '%'
                     );
                 },
             });

             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.region_nazwa, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent), kolor:item.kolor});
             });
             loadRegionChart(chart_data);
             $('#region_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
             $('#region_summary_data_refresh_span').text('Odśwież/załaduj');

             setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
             setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#country_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      getCountryChartTemplate();
      $('#country_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#country_summary_data_refresh_span').text('');

      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},
         dataType: 'json',
         url: "./summary_by_country_report.php",
         success: function (data) {
             $("#data-table").dataTable().fnDestroy();
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 dom: 'Bfrtip',
                 buttons: [
                  {
                    extend: "excelHtml5",
                    text: "Excel",
                    exportOptions: {
                      format: {
                        body: function ( data, row, column, node ) {
                          if (typeof data !== 'undefined') {
                              if (data !== null) {
                                  if (column === 2 || column === 3 || column > 4) {
                                        data = data.replace( /[\,]/g, "." );
                                      data = data.replace( /[^\d.-]/g, "" );
                                      return data;                                
                                  }
                              }
                          }
                          return data;
                        }
                      }
                    }
                  },
                  {
                    extend:'csvHtml5'
                  }   
                 ],
                 data : data,
                 columns: [
                     {data: 'kraj_nazwa'},
                     {data: 'kraj_kod'},
                     {
                        data: 'suma_wartosci',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                     },
                     {
                        data: 'suma_marz',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                     },
                     {
                       data: 'procent',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     }
                 ],
                 footerCallback: function ( row, data, start, end, display ) {
                     var api = this.api(), data;
                     var intVal = function ( i ) {
                         return typeof i === 'string' ?
                             i.replace(/[\$,]/g, '') * 1 :
                             typeof i === 'number' ?
                                 i : 0;
                     };

                     totalValue = api
                         .column(2, { search: 'applied' })
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                     pageTotalValue = api
                         .column( 2, { page: 'current'} )
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                       totalMargin = api
                           .column(3, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalmargin = api
                           .column( 3, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                     $( api.column( 2 ).footer() ).html(
                        'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display( pageTotalValue.toFixed(2)) + '<br> suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display( totalValue.toFixed(3))
                     );

                     $( api.column( 3).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalmargin.toFixed(2)) + '<br> suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                     );

                     $( api.column( 4 ).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalmargin / pageTotalValue) * 100).toFixed(2)) + '%  <br>całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin / totalValue) * 100).toFixed(2)) + '%'
                     );
                 },
             });

             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.kraj_nazwa, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent), kolor:item.kolor});
             });
             loadCountryChart(chart_data);
             $('#country_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
             $('#country_summary_data_refresh_span').text('Odśwież/załaduj');

             setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
             setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#voivodeship_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      getVoivodeshipChartTemplate();
      $('#voivodeship_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#voivodeship_summary_data_refresh_span').text('');

      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},
         dataType: 'json',
         url: "./summary_by_voivodeship_report.php",
         success: function (data) {
             $("#data-table").dataTable().fnDestroy();
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 dom: 'Bfrtip',
                 buttons: [
                  {
                    extend: "excelHtml5",
                    text: "Excel",
                    exportOptions: {
                      format: {
                        body: function ( data, row, column, node ) {
                          if (typeof data !== 'undefined') {
                              if (data !== null) {
                                  if (column === 2 || column === 3 || column > 4) {
                                        data = data.replace( /[\,]/g, "." );
                                      data = data.replace( /[^\d.-]/g, "" );
                                      return data;                                
                                  }
                              }
                          }
                          return data;
                        }
                      }
                    }
                  },
                  {
                    extend:'csvHtml5'
                  }   
                 ],
                 data : data,
                 columns: [
                     {data: 'wojewodztwo_nazwa'},
                     {data: 'wojewodztwo_kod'},
                     {
                        data: 'suma_wartosci',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                     },
                     {
                        data: 'suma_marz',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                     },
                     {
                       data: 'procent',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     }
                 ],
                 footerCallback: function ( row, data, start, end, display ) {
                     var api = this.api(), data;
                     var intVal = function ( i ) {
                         return typeof i === 'string' ?
                             i.replace(/[\$,]/g, '') * 1 :
                             typeof i === 'number' ?
                                 i : 0;
                     };

                     totalValue = api
                         .column(2, { search: 'applied' })
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                     pageTotalValue = api
                         .column( 2, { page: 'current'} )
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                       totalMargin = api
                           .column(3, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalmargin = api
                           .column( 3, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                     $( api.column( 2 ).footer() ).html(
                        'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display( pageTotalValue.toFixed(2)) + '<br> suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display( totalValue.toFixed(3))
                     );

                     $( api.column( 3).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalmargin.toFixed(2)) + '<br> suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                     );

                     $( api.column( 4 ).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalmargin / pageTotalValue) * 100).toFixed(2)) + '%  <br>całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin / totalValue) * 100).toFixed(2)) + '%'
                     );
                 },
             });

             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.wojewodztwo_nazwa, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent), kolor:item.kolor});
             });
             loadVoivodeshipChart(chart_data);
             $('#voivodeship_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
             $('#voivodeship_summary_data_refresh_span').text('Odśwież/załaduj');

             setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
             setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#salesman_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      getSalesmanChartTemplate();
      $('#salesman_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#salesman_summary_data_refresh_span').text('');

      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},

         dataType: 'json',
         url: "./summary_by_salesman_report.php",
         success: function (data) {
             $("#data-table").dataTable().fnDestroy();
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 dom: 'Bfrtip',
                 buttons: [
                  {
                    extend: "excelHtml5",
                    text: "Excel",
                    exportOptions: {
                      format: {
                        body: function ( data, row, column, node ) {
                          if (typeof data !== 'undefined') {
                              if (data !== null) {
                                  if (column === 1 || column === 2 || column > 3) {
                                        data = data.replace( /[\,]/g, "." );
                                      data = data.replace( /[^\d.-]/g, "" );
                                      return data;                                
                                  }
                              }
                          }
                          return data;
                        }
                      }
                    }
                  },
                  {
                    extend:'csvHtml5'
                  }   
                 ],
                 data : data,
                 columns: [
                     {data: 'sprzedawca'},
                     {
                       data: 'suma_wartosci',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     },
                     {
                       data: 'suma_marz',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     },
                     {
                       data: 'procent',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     },
                     {
                        data: 'kwota_przeterminowana',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                    },
                    {
                       data: 'premia_kwota',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                   },
                 ],
                 footerCallback: function ( row, data, start, end, display ) {
                     var api = this.api(), data;
                     var intVal = function ( i ) {
                         return typeof i === 'string' ?
                             i.replace(/[\$,]/g, '') * 1 :
                             typeof i === 'number' ?
                                 i : 0;
                     };

                     totalValue = api
                         .column(1, { search: 'applied' })
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                     pageTotalValue = api
                         .column( 1, { page: 'current'} )
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                       totalMargin = api
                           .column(2, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalMargin = api
                           .column( 2, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                     $( api.column( 1 ).footer() ).html(
                        'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalValue.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalValue.toFixed(2))
                     );

                     $( api.column( 2).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalMargin.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                     );

                     $( api.column( 3 ).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalMargin / pageTotalValue) * 100).toFixed(2)) + '% <br> całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin / totalValue) * 100).toFixed(2)) + '%'
                     );
                 },
             });

             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.sprzedawca, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent), kolor:item.kolor});
             });

             loadSalesmanChart(chart_data);
             $('#salesman_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
             $('#salesman_summary_data_refresh_span').text('Odśwież/załaduj');

             setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
             setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));

             getLatPayValues();
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#client_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      getClientChartTemplate();
      $('#client_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#client_summary_data_refresh_span').text('');
      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},

         dataType: 'json',
         url: "./summary_by_client_report.php",
         success: function (data) {
             $("#data-table").dataTable().fnDestroy();
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 dom: 'Bfrtip',
                 buttons: [
                  {
                    extend: "excelHtml5",
                    text: "Excel",
                    exportOptions: {
                      format: {
                        body: function ( data, row, column, node ) {
                          if (typeof data !== 'undefined') {
                              if (data !== null) {
                                  if (column === 1 || column === 2 || column > 3) {
                                        data = data.replace( /[\,]/g, "." );
                                      data = data.replace( /[^\d.-]/g, "" );
                                      return data;                                
                                  }
                              }
                          }
                          return data;
                        }
                      }
                    }
                  },
                  {
                    extend:'csvHtml5'
                  }   
                 ],
                 data : data,
                 columns: [
                     {data: 'kontrahent'},
                     {
                       data: 'suma_wartosci',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     },
                     {
                       data: 'suma_marz',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     },
                     {
                        data: 'procent',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                    }
                 ],
                 footerCallback: function ( row, data, start, end, display ) {
                     var api = this.api(), data;
                     var intVal = function ( i ) {
                         return typeof i === 'string' ?
                             i.replace(/[\$,]/g, '') * 1 :
                             typeof i === 'number' ?
                                 i : 0;
                     };

                     totalValue = api
                         .column(1, { search: 'applied' })
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                     pageTotalValue = api
                         .column( 1, { page: 'current'} )
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                       totalMargin = api
                           .column(2, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalMargin = api
                           .column( 2, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                     $( api.column( 1 ).footer() ).html(
                        'karta:  ' +  $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalValue.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalValue.toFixed(2))
                     );

                     $( api.column( 2 ).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalMargin.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                     );

                     $( api.column( 3 ).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalMargin/pageTotalValue)*100).toFixed(2)) + '% <br> całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin/totalValue)*100).toFixed(2)) + '%'
                     );
                 },
             });

             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.kontrahent, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent)});
             });

             loadClientChart(chart_data);
             $('#client_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
             $('#client_summary_data_refresh_span').text('Odśwież/załaduj');

             setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
             setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#item_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      getItemChartTemplate();
      $('#item_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#item_summary_data_refresh_span').text('');
      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},

         dataType: 'json',
         url: "./summary_by_item_report.php",
         success: function (data) {
             $("#data-table").dataTable().fnDestroy();
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 dom: 'Bfrtip',
                 buttons: [
                  {
                    extend: "excelHtml5",
                    text: "Excel",
                    exportOptions: {
                      format: {
                        body: function ( data, row, column, node ) {
                          if (typeof data !== 'undefined') {
                              if (data !== null) {
                                  if (column === 1 || column === 2 || column > 3) {
                                        data = data.replace( /[\,]/g, "." );
                                      data = data.replace( /[^\d.-]/g, "" );
                                      return data;                                
                                  }
                              }
                          }
                          return data;
                        }
                      }
                    }
                  },
                  {
                    extend:'csvHtml5'
                  }   
                 ],
                 data : data,
                 columns: [
                     {data: 'towar'},
                     {
                       data: 'ilosc',
                       className: "text-right"
                     },
                     {
                       data: 'suma_wartosci',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     },
                     {
                       data: 'suma_marz',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"},
                     {
                       data: 'procent',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     }
                 ],
                 footerCallback: function ( row, data, start, end, display ) {
                     var api = this.api(), data;
                     var intVal = function ( i ) {
                         return typeof i === 'string' ?
                             i.replace(/[\$,]/g, '') * 1 :
                             typeof i === 'number' ?
                                 i : 0;
                     };

                     totalAmount = api
                         .column(1, { search: 'applied' })
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                     pageTotalAmount = api
                         .column( 1, { page: 'current'} )
                         .data()
                         .reduce( function (a, b) {
                             return intVal(a) + intVal(b);
                         }, 0 );

                       totalValue = api
                           .column(2, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalValue = api
                           .column( 2, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       totalMargin = api
                           .column(3, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalMargin = api
                           .column( 3, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                     $( api.column( 1 ).footer() ).html(
                        'karta:  ' + pageTotalAmount + '<br>  suma całkowita:  ' + totalAmount
                     );

                     $( api.column( 2).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalValue.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalValue.toFixed(2))
                     );

                     $( api.column( 3).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalMargin.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                     );

                     $( api.column( 4 ).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalMargin / pageTotalValue) * 100).toFixed(2)) + '% <br> całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin / totalValue) * 100).toFixed(2)) + '%'
                     );
                 },
             });

             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.towar, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent),  ilosc:parseInt(item.ilosc)});
             });

             loadItemChart(chart_data);
             $('#item_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
             $('#item_summary_data_refresh_span').text('Odśwież/załaduj');

             setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
             setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#invoice_summary_by_item_data_refresh', function() {

  if($('#item').val()) {
    if ($('#report_date_from').val() && $('#report_date_to').val() ) {
      if ($('#report_date_from').val() < $('#report_date_to').val()) {

        $('#error_msg').text('');
        $("#report_date_to").css("border-bottom", "none");
        $("#report_date_from").css("border-bottom", "none");
        $("#item").css("border-bottom", "none");

        $("#data-table").dataTable().fnDestroy();
        $("#data_refresh").attr("disabled", true);
        $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
        $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
        $itemId = $('#item').val();
        getInvoiceByItemChartTemplate();
        $('#item_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
        $('#item_summary_data_refresh_span').text('');
        $.ajax({

          method: "POST",
          data: {dateFrom : $dateFrom, dateTo : $dateTo, item: $itemId},

          dataType: 'json',
          url: "./invoice_summary_by_item_report.php",
          success: function (data) {
              $("#data-table").dataTable().fnDestroy();
              $("#data_refresh").attr("disabled", false);
              $('#data-table').DataTable({
                  dom: 'Bfrtip',
                  buttons: [
                    {
                      extend: "excelHtml5",
                      text: "Excel",
                      exportOptions: {
                        format: {
                          body: function ( data, row, column, node ) {
                            if (typeof data !== 'undefined') {
                                if (data !== null) {
                                    if (column === 1 || column === 2 || column > 3) {
                                          data = data.replace( /[\,]/g, "." );
                                        data = data.replace( /[^\d.-]/g, "" );
                                        return data;                                
                                    }
                                }
                            }
                            return data;
                          }
                        }
                      }
                    },
                    {
                      extend:'csvHtml5'
                    }   
                  ],
                  data : data,
                  columns: [
                      {data: 'towar'},
                      {data: 'faktura_numer'},
                      {data: 'kontrahent_nazwa'},
                      {data: 'sprzedawca'},
                      {
                        data: 'ilosc',
                        className: "text-right"
                      },
                      {
                        data: 'suma_wartosci',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                      },
                      {
                        data: 'suma_marz',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"},
                      {
                        data: 'procent',
                        render: $.fn.dataTable.render.number( ' ', ',', 2),
                        className: "text-right"
                      }
                  ],
                  footerCallback: function ( row, data, start, end, display ) {
                      var api = this.api(), data;
                      var intVal = function ( i ) {
                          return typeof i === 'string' ?
                              i.replace(/[\$,]/g, '') * 1 :
                              typeof i === 'number' ?
                                  i : 0;
                      };

                      totalAmount = api
                          .column(4, { search: 'applied' })
                          .data()
                          .reduce( function (a, b) {
                              return intVal(a) + intVal(b);
                          }, 0 );

                      pageTotalAmount = api
                          .column( 4, { page: 'current'} )
                          .data()
                          .reduce( function (a, b) {
                              return intVal(a) + intVal(b);
                          }, 0 );

                        totalValue = api
                            .column(5, { search: 'applied' })
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );

                        pageTotalValue = api
                            .column( 5, { page: 'current'} )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );

                        totalMargin = api
                            .column(6, { search: 'applied' })
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );

                        pageTotalMargin = api
                            .column( 6, { page: 'current'} )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );

                      $( api.column( 4).footer() ).html(
                          'karta:  ' + pageTotalAmount + '<br>  suma całkowita:  ' + totalAmount
                      );

                      $( api.column( 5).footer() ).html(
                          'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalValue.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalValue.toFixed(2))
                      );

                      $( api.column( 6).footer() ).html(
                          'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalMargin.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                      );

                      $( api.column( 7 ).footer() ).html(
                          'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalMargin / pageTotalValue) * 100).toFixed(2)) + '% <br> całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin / totalValue) * 100).toFixed(2)) + '%'
                      );
                  },
              });

              var chart_data =  new Array();
              data.forEach((item, index) => {
                chart_data.push({label:item.faktura_numer, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent),  ilosc:parseInt(item.ilosc)});
              });

              loadInvoiceItemChart(chart_data);
              $('#invoice_by_item_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
              $('#invoice_by_item_summary_data_refresh_span').text('Odśwież/załaduj');

              setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
              setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
              setCookie('item', $('#item').val());
          },
          always: function() {
            $("#data_refresh").attr("disabled", false);
            }
        })
      } else {
        $('#error_msg').text('Nieprawidłowe daty');
        $("#report_date_to").css("border-bottom", "1px solid red");
        $("#report_date_from").css("border-bottom", "1px solid red");
      }
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Wybierz towar');
    $("#item").css("border-bottom", "1px solid red");
  }
});

$(document).on('click', '#invoice_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      $salesman = $("#salesman").children("option:selected").val();
      $exportType = getExportTypeReport();

      $('#invoice_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#invoice_summary_data_refresh_span').text('');
      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo, salesman: $salesman, exportType : $exportType},

         dataType: 'json',
         url: "./invoice_summary_report.php",
         success: function (data) {
             $("#data-table").dataTable().fnDestroy();
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 dom: 'Bfrtip',
                 buttons: [
                  {
                    extend: "excelHtml5",
                    text: "Excel",
                    exportOptions: {
                      format: {
                        body: function ( data, row, column, node ) {
                          if (typeof data !== 'undefined') {
                              if (data !== null) {
                                  if (column > 4) {
                                        data = data.replace( /[\,]/g, "." );
                                      data = data.replace( /[^\d.-]/g, "" );
                                      return data;                                
                                  }
                              }
                          }
                          return data;
                        }
                      }
                    }
                  },
                  {
                    extend:'csvHtml5'
                  }   
                 ],
                 data : data,
                 columns: [
                     {
                       data: 'faktura_numer'
                     },
                     {
                       data: 'data_wystawienia',
                     },
                     {
                      data: 'uwagi',
                     },
                     {
                       data: 'kontrahent_nazwa',
                     },
                     {
                       data: 'sprzedawca',
                     },
                     {
                       data: 'suma_wartosci',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     },
                     {
                       data: 'suma_marz',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"},
                     {
                       data: 'procent',
                       render: $.fn.dataTable.render.number( ' ', ',', 2),
                       className: "text-right"
                     }
                 ],
                 columnDefs: [
                  { 
                    "targets": 2,
                    "render": function (data, type, full, meta) {
                      return type === 'display'? '<div title="' + data + '">' + data : data;
                    }     
                  },
                 ],
                 footerCallback: function ( row, data, start, end, display ) {
                     var api = this.api(), data;
                     var intVal = function ( i ) {
                         return typeof i === 'string' ?
                             i.replace(/[\$,]/g, '') * 1 :
                             typeof i === 'number' ?
                                 i : 0;
                     };

                       totalValue = api
                           .column(5, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalValue = api
                           .column( 5, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       totalMargin = api
                           .column(6, { search: 'applied' })
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                       pageTotalMargin = api
                           .column( 6, { page: 'current'} )
                           .data()
                           .reduce( function (a, b) {
                               return intVal(a) + intVal(b);
                           }, 0 );

                     $( api.column( 5).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalValue.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalValue.toFixed(2))
                     );

                     $( api.column( 6).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalMargin.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                     );

                     $( api.column( 7 ).footer() ).html(
                         'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalMargin / pageTotalValue) * 100).toFixed(2)) + '% <br> całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin / totalValue) * 100).toFixed(2)) + '%'
                     );
                 },
             });

             $('#invoice_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
             $('#invoice_summary_data_refresh_span').text('Odśwież/załaduj');

             setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
             setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
             setCookie('salesman',  $("#salesman").children("option:selected").val());
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#error_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      $('#error_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#error_summary_data_refresh_span').text('');
      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},

         dataType: 'json',
         url: "./summary_error_report.php",
         success: function (data) {
             $("#data-table").dataTable().fnDestroy();
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 dom: 'Bfrtip',
                 buttons: [
                  'excelHtml5',
                  'csvHtml5'
                 ],
                 data : data,
                 columns: [
                     {data: 'faktura_numer'},
                     {data: 'data_wystawienia'},
                     {data: 'kontrahent'},
                     {data: 'sprzedawca'},
                     {data: 'region'},
                     {data: 'kraj'},
                     {data: 'wojewodztwo'},
                     {data: 'waluta'},
                     {data: 'eksport'},
                     {data: 'kurs'},
                     {data: 'cena_zero'},
                     {data: 'cena'}
                 ],
             });
             $('#error_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
             $('#error_summary_data_refresh_span').text('Odśwież/załaduj');

             setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
             setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#item_region_summary_data_refresh', function() {

  if ($('#report_date_from').val() && $('#report_date_to').val()) {
    if ($('#report_date_from').val() < $('#report_date_to').val()) {

      $('#error_msg').text('');
      $("#report_date_to").css("border-bottom", "none");
      $("#report_date_from").css("border-bottom", "none");

      $("#data-table").dataTable().fnDestroy();
      $("#data_refresh").attr("disabled", true);
      getItemRegionChartTemplate();
      $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
      $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);
      $region = $("#region").children("option:selected").val();

      $('#item_region_summary_data_refresh_span').addClass('spinner-border spinner-border-sm text-light');
      $('#item_region_summary_data_refresh_span').text('');
      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo, region: $region},

         dataType: 'json',
         url: "./summary_by_item_region_report.php",
         success: function (data) {
          $("#data-table").dataTable().fnDestroy();
          $("#data_refresh").attr("disabled", false);
          $('#data-table').DataTable({
              dom: 'Bfrtip',
              buttons: [
                {
                  extend: "excelHtml5",
                  text: "Excel",
                  exportOptions: {
                    format: {
                      body: function ( data, row, column, node ) {
                        if (typeof data !== 'undefined') {
                            if (data !== null) {
                                if (column > 1) {
                                      data = data.replace( /[\,]/g, "." );
                                    data = data.replace( /[^\d.-]/g, "" );
                                    return data;                                
                                }
                            }
                        }
                        return data;
                      }
                    }
                  }
                },
                {
                  extend:'csvHtml5'
                }   
               ],
              data : data,
              columns: [
                  {data: 'towar'},
                  {data: 'region'},
                  {
                    data: 'ilosc',
                    className: "text-right"
                  },
                  {
                    data: 'suma_wartosci',
                    render: $.fn.dataTable.render.number( ' ', ',', 2),
                    className: "text-right"
                  },
                  {
                    data: 'suma_marz',
                    render: $.fn.dataTable.render.number( ' ', ',', 2),
                    className: "text-right"},
                  {
                    data: 'procent',
                    render: $.fn.dataTable.render.number( ' ', ',', 2),
                    className: "text-right"
                  }
              ],
              footerCallback: function ( row, data, start, end, display ) {
                  var api = this.api(), data;
                  var intVal = function ( i ) {
                      return typeof i === 'string' ?
                          i.replace(/[\$,]/g, '') * 1 :
                          typeof i === 'number' ?
                              i : 0;
                  };

                  totalAmount = api
                      .column(2, { search: 'applied' })
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );

                  pageTotalAmount = api
                      .column( 2, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0 );

                    totalValue = api
                        .column(3, { search: 'applied' })
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    pageTotalValue = api
                        .column( 3, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    totalMargin = api
                        .column(4, { search: 'applied' })
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    pageTotalMargin = api
                        .column( 4, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                  $( api.column( 2 ).footer() ).html(
                     'karta:  ' + pageTotalAmount + '<br>  suma całkowita:  ' + totalAmount
                  );

                  $( api.column( 3).footer() ).html(
                      'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalValue.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalValue.toFixed(2))
                  );

                  $( api.column( 4).footer() ).html(
                      'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(pageTotalMargin.toFixed(2)) + '<br>  suma całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(totalMargin.toFixed(2))
                  );

                  $( api.column( 5 ).footer() ).html(
                      'karta:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((pageTotalMargin / pageTotalValue) * 100).toFixed(2)) + '% <br> całkowita:  ' + $.fn.dataTable.render.number( ' ', ',', 2).display(((totalMargin / totalValue) * 100).toFixed(2)) + '%'
                  );
              },
          });
          var chart_data =  new Array();
          data.forEach((item, index) => {
            if(isNaN($("#region").children("option:selected").val())) {
              chart_data.push({label:item.towar+ ' '+ item.region, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent),  ilosc:parseInt(item.ilosc)});
            } else {
              chart_data.push({label:item.towar, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent),  ilosc:parseInt(item.ilosc)});
            }
          });

          loadItemRegionChart(chart_data);
          $('#item_region_summary_data_refresh_span').removeClass('spinner-border spinner-border-sm text-light');
          $('#item_region_summary_data_refresh_span').text('Odśwież/załaduj');

          setCookie('report_date_from',  new Date($('#report_date_from').val()).toISOString().substring(0,10));
          setCookie('report_date_to',  new Date($('#report_date_to').val()).toISOString().substring(0,10));
          setCookie('region',  $("#region").children("option:selected").val());
         },
         always: function() {
           $("#data_refresh").attr("disabled", false);
          }
      })
    } else {
      $('#error_msg').text('Nieprawidłowe daty');
      $("#report_date_to").css("border-bottom", "1px solid red");
      $("#report_date_from").css("border-bottom", "1px solid red");
    }
  } else {
    $('#error_msg').text('Nieprawidłowe daty');
    $("#report_date_to").css("border-bottom", "1px solid red");
    $("#report_date_from").css("border-bottom", "1px solid red");
  }

});

$(document).on('click', '#summary_by_region_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./summary_by_region_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
       }
  })
});

$(document).on('click', '#summary_by_item_region_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./summary_by_item_region_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
             getRegionFilter();
             $('#region').val( getCookie('region'));
       }
  })
});

$(document).on('click', '#summary_by_country_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./summary_by_country_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
       }
  })
});


$(document).on('click', '#summary_by_voivodeship_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./summary_by_voivodeship_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
       }
  })
});

$(document).on('click', '#summary_by_salesman_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./summary_by_salesman_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
       }
  })
});

$(document).on('click', '#summary_by_client_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./summary_by_client_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
       }
  })
});

$(document).on('click', '#summary_by_item_show', function() {
  clearChartTemplate();
$.ajax({
        method: "GET",
        url: "./summary_by_item_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
       }
  })

});

$(document).on('click', '#invoice_summary_report_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./invoice_summary_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
             getSalesmanFilter();
             $('#salesman').val( getCookie('salesman'));
       }
  })
});

$(document).on('click', '#error_summary_report_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./summary_error_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
       }
  })
});

$(document).on('click', '#invoice_summary_by_item_show', function() {
  clearChartTemplate();
  $.ajax({
        method: "GET",
        url: "./invoice_summary_by_item_report_template.php",
        success: function(data){
             $('#report_div').empty();
             $('#report_div').append(data);
             $('#report_date_from').val( getCookie('report_date_from'));
             $('#report_date_to').val( getCookie('report_date_to'));
             getItemFilter();
             $('#item').val( getCookie('item'));
       }
  })
});

$(document).on('click', '.btn-report', function() {
  $('.btn-report').css({"border-color": "red","border-width":"0px","border-style":"solid"});
  $(this).css({"border-color": "red","border-width":"2px","border-style":"solid"});
});

function getItemChartTemplate() {
  $.ajax({
          method: "GET",
          url: "./charts/item_charts_template.php",
          success: function(data){
               $('#chart_div').empty();
               $('#chart_div').append(data);
         }
    });
}


function getInvoiceByItemChartTemplate() {
  $.ajax({
          method: "GET",
          url: "./charts/item_invoice_charts_template.php",
          success: function(data){
               $('#chart_div').empty();
               $('#chart_div').append(data);
         }
    });
}

function getItemRegionChartTemplate() {
  $.ajax({
          method: "GET",
          url: "./charts/item_region_charts_template.php",
          success: function(data){
               $('#chart_div').empty();
               $('#chart_div').append(data);
         }
    });
}


function getClientChartTemplate() {
  $.ajax({
          method: "GET",
          url: "./charts/client_charts_template.php",
          success: function(data){
               $('#chart_div').empty();
               $('#chart_div').append(data);
         }
    });
}

function getRegionChartTemplate() {
  $.ajax({
          method: "GET",
          url: "./charts/region_charts_template.php",
          success: function(data){
               $('#chart_div').empty();
               $('#chart_div').append(data);
         }
    });
}

function getCountryChartTemplate() {
  $.ajax({
          method: "GET",
          url: "./charts/country_charts_template.php",
          success: function(data){
               $('#chart_div').empty();
               $('#chart_div').append(data);
         }
    });
}

function getVoivodeshipChartTemplate() {
  $.ajax({
          method: "GET",
          url: "./charts/voivodeship_charts_template.php",
          success: function(data){
               $('#chart_div').empty();
               $('#chart_div').append(data);
         }
    });
}

function getExportTypeReport() {
  return $('input[name=export_radios]:checked').val();
}

function getSalesmanChartTemplate() {
  $.ajax({
          method: "GET",
          url: "./charts/salesman_charts_template.php",
          success: function(data){
               $('#chart_div').empty();
               $('#chart_div').append(data);
         }
    });
}

function getLatPayValues() {

  $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
  $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);

  $.ajax({
      url: "./user_late_pay_values.php",
      type: 'post',
      data: {dateFrom: $dateFrom, dateTo: $dateTo},
      dataType: 'json',
      success:function(jsonData){
        $("#late_pay_datatable").dataTable().fnDestroy();
        $('#late_pay_datatable').DataTable({
            "paging": true,
            data : jsonData,
            columns: [
                {
                    data: 'data',
                    width: '20%'
                },
                {
                    data: 'sprzedawca',
                    width: '30%'
                },
                {
                  "render": function(data, type, row) {
                      var $textInput = $("<input class='form-control' class ='late_pay_value-input' type='number' step='0.01' min = '0.01' value='" + row['wartosc_przeterminowana'] + "'>");
                      return $textInput.prop("outerHTML");
                    },
                    width: '30%'
                },
                {
                    data: 'edytuj',
                    width: '20%'
                }
              ]
            });
      }
  });
}

function clearChartTemplate() {
  $('#chart_div').empty();
}


function setCookie(name, value)
{
  document.cookie=name + "=" + escape(value) + "; path=/; expires=" + expiry.toGMTString();
}

function getCookie(name)
{
  var re = new RegExp(name + "=([^;]+)");
  var value = re.exec(document.cookie);
  return (value != null) ? unescape(value[1]) : null;
}
