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
                 data: 'bonus',
                 render: $.fn.dataTable.render.number( '', '.', 2),
               },
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
  getFiletValues();
})

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
           $('#bonus').val((data[0]['bonus']).toFixed(2)).siblings().addClass('active');
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


function getRegionFilter() {
  $.ajax({
      url: "../../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'region'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var region_id = response[i]['region_id'];
              var region_name = response[i]['region_nazwa'];

              $("#region").append("<option value='"+region_id+"'>"+region_name+"</option>");
          }
      }
  });
}

function getCountryFilter() {
  $.ajax({
      url: "../../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type: 'country'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i < len; i++){
              var country_id = response[i]['kraj_id'];
              var country_name = response[i]['kraj_nazwa'];

              $("#country").append("<option value='"+country_id+"'>"+country_name+"</option>");
          }
      }
  });
}

function getVoivodeshipFilter() {
  $.ajax({
      url: "../../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'voivodeship'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var voivodeship_id = response[i]['wojewodztwo_id'];
              var voivodeship_name = response[i]['wojewodztwo_nazwa'];

              $("#voivodeship").append("<option value='"+voivodeship_id+"'>"+voivodeship_name+"</option>");
          }
      }
  });
}

function getSalesmanFilter() {
  $.ajax({
      url: "../../invoice_import/invoice_import_filters.php",
      type: 'post',
      data: {type:'salesman'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              var salesman_id = response[i]['uzytkownik_id'];
              var salesman_name = response[i]['uzytkownik_nazwa'];

              $("#salesman").append("<option value='"+salesman_id+"'>"+salesman_name+"</option>");
          }
      }
  });
}



function getFiletValues() {
  getRegionFilter();
  getCountryFilter();
  getVoivodeshipFilter();
  getSalesmanFilter();
}
