$(document).ready(function () {

  $.ajax({
     method: "POST",
     data: {
              action : "getUsers"
           },
     dataType: 'json',
     url: "./user_actions.php",
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
               {data: 'uzytkownik_id'},
               {data: 'username'},
               {data: 'imie'},
               {data: 'nazwisko'},
               {data: 'jest_aktywny'},
               {data: 'rola_nazwa'},
               {
                   data: 'edycja',
                   visible: editIconAvailable,
               }
           ]
       });
     },
  })
});

$(document).ready(function () {
    var currentDate = new Date(),
    currentMonth = currentDate.getMonth(),
    currentYear = currentDate.getFullYear();

  $('#late_pay_year').val(currentYear).siblings().addClass('active');
  $('#late_pay_month').val(currentMonth).siblings().addClass('active');
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
  var form=document.getElementById('update_user_form');

  var input = document.createElement('input');
  input.setAttribute('name', 'action');
  input.setAttribute('value', 'updateUser');
  input.setAttribute('type', 'hidden');

  form.appendChild(input);

  form.submit();
});

$('#add_user_form').submit(function () {
  var form=document.getElementById('add_user_form');

  var input = document.createElement('input');
  input.setAttribute('name', 'action');
  input.setAttribute('value', 'addUser');
  input.setAttribute('type', 'hidden');

  form.appendChild(input);

  form.submit();
});
