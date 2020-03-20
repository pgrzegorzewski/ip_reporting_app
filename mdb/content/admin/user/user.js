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
