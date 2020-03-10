$(document).ready(function () {
    $('#dtBasicExample').DataTable({
    });
    $('.dataTables_length').addClass('bs-select');
     $('#data-table').DataTable();

});

$(document).on('click', '#data_refresh', function() {
  $("#data-table").dataTable().fnDestroy();
  $dateFrom = new Date($('#report_date_from').val()).toISOString().substring(0,10);
  $dateTo = new Date($('#report_date_to').val()).toISOString().substring(0,10);

  $.ajax({

     method: "POST",
     data: {dateFrom : $dateFrom, dateTo : $dateTo},
     // data: {
     //          dateFrom: '2020-01-01',
     //          dateTo: '2020-02-01'
     //        },
     dataType: 'json',
     url: "./summary_by_region_report.php",
     success: function (data) {

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
     }
  })
});
