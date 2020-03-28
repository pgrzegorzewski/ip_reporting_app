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

    $('#tmp_password_btn').click(function () {
      var password_temporary = $('#password_temporary').val();
      if(password_temporary.length < 4) {
        $('#assign_temporary_pwd_error').text('Tymczasowe hasło musi mieć co najmniej 4 znaki');
      } else {
        $.ajax({
           method: "POST",
           data: {action : "assignTemporaryPassword", userId : id, passwordTemporary : password_temporary},
           dataType: 'json',
           url: "./user_actions.php",
           success: function(data) {
             console.log('test');
             $('#assign_temporary_pwd_error').text('');
             $('#assign_temporary_pwd_success').text('Hasło przypisane pomyślnie');
           },
        })
        $('#assign_temporary_pwd_error').text('');
        $('#assign_temporary_pwd_success').text('Hasło przypisane pomyślnie');
      }
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
