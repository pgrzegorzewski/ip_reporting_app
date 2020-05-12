$(document).ready(function () {

  $.ajax({
     method: "POST",
     data: {action : "getClients"},
     dataType: 'json',
     url: "./client_actions.php",
     success: function (data) {
       var editIconAvailable = 0
       if(data.length > 0) {
         var editionCheck = data[0].edycja_dostep;
         editIconAvailable = data[0].edycja_dostep == 0 ? false : true;
       }
       $("#data_refresh").attr("disabled", false);
       $('#data-table').DataTable({
          "scrollX": true,
           data : data,
           columns: [
               {data: 'kontrahent_id'},
               {data: 'kontrahent_nazwa'},
               {data: 'ulica'},
               {data: 'nr_domu'},
               {data: 'kod_pocztowy'},
               {data: 'miasto'},
               {data: 'kraj'},
               {data: 'jest_aktywny'},
               {data: 'czarna_lista'},
               {
                   data: 'edycja',
                   visible: editIconAvailable,
               }
           ]
       });
     },
  })
});

$(document).ready(function() {
  $('#editClientModal').on('show.bs.modal', function(e) {
    var id = $(e.relatedTarget).data('id');
    $.ajax({
       method: "POST",
       data: {action : "getClientData", clientId : id},
       dataType: 'json',
       url: "./client_actions.php",
       success: function (data) {
           $('#client_name').val(data[0]['kontrahent_nazwa']);
           $('#street').val(data[0]['ulica']);
           $('#address_2').val(data[0]['nr_domu']);
           $('#post_code').val(data[0]['kod_pocztowy']);
           $('#city').val(data[0]['miasto']);
           $('#country').val(data[0]['kraj']);
           if(data[0]['jest_aktywny'] == 1) {
             $('#is_active').prop('checked', true);
           } else {
             $('#is_active').prop('checked', false);
           }
           if(data[0]['czarna_lista'] == 1) {
             $('#black_list').prop('checked', true);
           } else {
             $('#black_list').prop('checked', false);
           }
           $('#clientId').val(id);
       },
    })
  });
});

$('#update_client_form').submit(function () {
  var form=document.getElementById('update_client_form');
  var input = document.createElement('input');
  input.setAttribute('name', 'action');
  input.setAttribute('value', 'updateClient');
  input.setAttribute('type', 'hidden')

  form.appendChild(input);
  form.submit();
});


$('#add_client_form').submit(function () {
  var form=document.getElementById('add_client_form');

  var input = document.createElement('input');
  input.setAttribute('name', 'action');
  input.setAttribute('value', 'addClient');
  input.setAttribute('type', 'hidden')

  form.appendChild(input);

  form.submit();
});
