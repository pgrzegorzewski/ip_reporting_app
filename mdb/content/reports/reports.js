var today = new Date();
var expiry = new Date(today.getTime() + 30 * 24 * 3600 * 1000);  // 30 days

$(document).ready(function () {
  $('#dtBasicExample').DataTable({
  });
  $('.dataTables_length').addClass('bs-select');
  $('#data-table').DataTable();
});

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

      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},

         dataType: 'json',
         url: "./summary_by_region_report.php",
         success: function (data) {
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 data : data,
                 columns: [
                     {data: 'region_nazwa'},
                     {data: 'region_kod'},
                     {data: 'suma_wartosci'},
                     {data: 'suma_marz'},
                     {data: 'procent'}
                 ]
             });
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

      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},

         dataType: 'json',
         url: "./summary_by_salesman_report.php",
         success: function (data) {
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 data : data,
                 columns: [
                     {data: 'sprzedawca'},
                     {data: 'suma_wartosci'},
                     {data: 'suma_marz'},
                     {data: 'procent'}
                 ]
             });

             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.sprzedawca, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent), kolor:item.kolor});
             });

             loadSalesmanChart(chart_data);

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
      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},

         dataType: 'json',
         url: "./summary_by_client_report.php",
         success: function (data) {
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 data : data,
                 columns: [
                     {data: 'kontrahent'},
                     {data: 'suma_wartosci'},
                     {data: 'suma_marz'},
                     {data: 'procent'}
                 ]
             });

             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.kontrahent, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent)});
             });

             loadClientChart(chart_data);

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
      $.ajax({

         method: "POST",
         data: {dateFrom : $dateFrom, dateTo : $dateTo},

         dataType: 'json',
         url: "./summary_by_item_report.php",
         success: function (data) {
             $("#data_refresh").attr("disabled", false);
             $('#data-table').DataTable({
                 data : data,
                 columns: [
                     {data: 'towar'},
                     {data: 'ilosc'},
                     {data: 'suma_wartosci'},
                     {data: 'suma_marz'},
                     {data: 'procent'}
                 ]
             });


             var chart_data =  new Array();
             data.forEach((item, index) => {
               chart_data.push({label:item.towar, suma_wartosci:parseFloat(item.suma_wartosci), suma_marz:parseFloat(item.suma_marz), procent:parseFloat(item.procent),  ilosc:parseInt(item.ilosc)});
             });

             loadItemChart(chart_data);

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
