var originalModal ='';
$(document).ready(function () {
  originalModal  = $('#editUserModal').clone();
});

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

$(document).on('hidden.bs.modal', '.modal', function () {

   $("#editUserModal").remove();
   originalModal.insertAfter("#addUserModal");
   bindModal();
 });

$(document).ready(function() {
  bindModal();
});

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
         if(data[0]['stanowisko'] != 'handlowiec') {
           $('.late_pay').prop('disabled', true);
         } else {
           $('.late_pay').prop('disabled', false);
         }
     },
  });

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
  });
});



function bindModal() {
  $('#editUserModal').on('show.bs.modal', function(e) {

    var currentDate = new Date(),
    currentMonth = currentDate.getMonth(),
    currentYear = currentDate.getFullYear();

    $('#late_pay_year').val(currentYear).siblings().addClass('active');
    $('#late_pay_month').val(currentMonth).siblings().addClass('active');
    $('#late_pay_value').val(currentMonth).siblings().addClass('active');

    $('#assign_temporary_pwd_error').text('');
    $('#assign_temporary_pwd_success').text('');
    $('#late_pay_error').text('');
    $('#late_pay_success').text('');
    $('#late_pay_value').val('');
    $('#password_temporary').val('');

    var id = $(e.relatedTarget).data('id');

    getLatePay(id, parseInt($('#late_pay_year').val()), parseInt($('#late_pay_month').val()));

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
           if(data[0]['stanowisko'] != 'handlowiec') {
             $('.late_pay').prop('disabled', true);
           } else {
             $('.late_pay').prop('disabled', false);
           }
       },
    });

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
    });

  $('#late_pay_month').change(function(){
    getLatePay(id, parseInt($('#late_pay_year').val()), parseInt($('#late_pay_month').val()));
  })

  $('#late_pay_year').change(function(){
    getLatePay(id, parseInt($('#late_pay_year').val()), parseInt($('#late_pay_month').val()));
  })


  $('#late_pay_value_save').click(function () {
      var latePayYear = $('#late_pay_year').val();
      var latePayMonth = $('#late_pay_month').val();
      var latePayValue = $('#late_pay_value').val();

      if(!latePayYear || latePayYear < 0 || !latePayMonth || latePayMonth < 1 || latePayMonth > 12 || !latePayValue || latePayValue < 0) {
        $('#late_pay_success').text('');
        $('#late_pay_error').text('Nieprawidłowe wartości');
      } else {
        $.ajax({
           method: "POST",
           data: {action : "updateUserLatePayValue", userId : id, latePayYear : parseInt(latePayYear), latePayMonth: parseInt(latePayMonth), latePayValue: latePayValue},
           dataType: 'json',
           url: "./user_actions.php",
           success: function(data) {
             $('#assign_temporary_pwd_error').text('');
             $('#assign_temporary_pwd_success').text('Hasło przypisane pomyślnie');
           },
        })
         $('#late_pay_error').text('');
         $('#late_pay_success').text('Wartość wprowadzona');
      }
    });
  });
}

function getLatePay(userId, year, month) {
  $('#late_pay_error').text('');
  $('#late_pay_success').text('');

  $.ajax({
     method: "POST",
     data: {action : "getUserLatePay", userId : userId, latePayYear : parseInt(year), latePayMonth: parseInt(month)},
     dataType: 'json',
     url: "./user_actions.php",
     success: function(data) {
       $('#late_pay_value').val(data);
     }
  })
}


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
