$(document).ready(function () {

  $.ajax({
     method: "POST",
     data: {action : "getUsers"},
     dataType: 'json',
     url: "./user_actions.php",
     success: function (data) {
         $("#data_refresh").attr("disabled", false);
         $('#data-table').DataTable({
             data : data,
             columns: [
                 {data: 'uzytkownik_id'},
                 {data: 'username'},
                 {data: 'imie'},
                 {data: 'nazwisko'},
                 {data: 'jest_aktywny'},
                 {data: 'rola_nazwa'},
                 {data: 'edycja'}
             ]
         });

     },
  })

});

$(document).ready(function() {
  $('#editUserModal').on('show.bs.modal', function(e) {
    var id = $(e.relatedTarget).data('id');
  });
});
