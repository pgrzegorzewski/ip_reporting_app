$(document).ready(function () {

  $.ajax({
     method: "POST",
     data: {action : "getItems"},
     dataType: 'json',
     url: "./item_actions.php",
     success: function (data) {
         $("#data_refresh").attr("disabled", false);
         console.log('test');
         $('#data-table').DataTable({
             data : data,
             columns: [
                 {data: 'towar_id'},
                 {data: 'towar_nazwa'},
                 {data: 'jest_aktywny'},
                 {data: 'szereg_nazwa'},
                 {data: 'rodzaj_nazwa'},
                 {data: 'cena_go'},
                 {data: 'cena_po'},
                 {data: 'cena_gd'},
                 {data: 'cena_pd'},
                 {data: 'edycja'}
             ]
         });
     },
  })
});

$(document).ready(function() {

  $('#editUserModal').on('show.bs.modal', function(e) {

    $('#assign_temporary_pwd_error').text('');
    $('#assign_temporary_pwd_success').text('');
    $('#password_temporary').val('');

    var id = $(e.relatedTarget).data('id');
    $.ajax({
       method: "POST",
       data: {action : "getUserData", userId : id},
       dataType: 'json',
       url: "./user_actions.php",
       success: function (data) {
           $('#username').val(data[0]['username']);
           $('#last_name').val(data[0]['nazwisko']);
           $('#first_name').val(data[0]['imie']);
           $('#role').val(data[0]['rola_nazwa']).change();
           if(data[0]['jest_aktywny'] == 1) {
             $('#is_active').prop('checked', true);
           } else {
             $('#is_active').prop('checked', false);
           }
           $('#is_active').val(data[0]['jest_aktywny']);
           $('#userId').val(id);
       },
    })
  });
});



$('#update_user_form').submit(function () {
  var form=document.getElementById('update_user_form');//retrieve the form as a DOM element

  var input = document.createElement('input');//prepare a new input DOM element
  input.setAttribute('name', 'action');//set the param name
  input.setAttribute('value', 'updateUser');//set the value
  input.setAttribute('type', 'hidden')//set the type, like "hidden" or other

  form.appendChild(input);//append the input to the form

  form.submit();
});
