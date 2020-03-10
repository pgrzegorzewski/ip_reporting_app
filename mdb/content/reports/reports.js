$(document).ready(function () {
    $('#dtBasicExample').DataTable({
    });
    $('.dataTables_length').addClass('bs-select');
     $('#data-table').DataTable();

});

$(document).on('click', '#data_refresh', function() {

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
         },
         always: function() {
           console.log('elo');
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
