$(document).ready(function () {

  getTypeSelect();
  getGroupSelect();

  $.ajax({
     method: "POST",
     data: {action : "getItems"},
     dataType: 'json',
     url: "./item_actions.php",
     success: function (data) {
         $("#data_refresh").attr("disabled", false);
         $('#data-table').DataTable({
            "scrollX": true,
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
  $('#editItemModal').on('show.bs.modal', function(e) {
    var id = $(e.relatedTarget).data('id');
    $.ajax({
       method: "POST",
       data: {action : "getItemData", itemId : id},
       dataType: 'json',
       url: "./item_actions.php",
       success: function (data) {
           $('#item_name').val(data[0]['towar_nazwa']);
           $('#group_name').val(data[0]['szereg_id']).change();
           $('#type_name').val(data[0]['rodzaj_id']).change();
           if(data[0]['jest_aktywny'] == 1) {
             $('#is_active').prop('checked', true);
           } else {
             $('#is_active').prop('checked', false);
           }
           $('#is_active').val(data[0]['jest_aktywny']);
           $('#price_go').val(data[0]['cena_go']).siblings().addClass('active');
           $('#price_po').val(data[0]['cena_po']).siblings().addClass('active');
           $('#price_gd').val(data[0]['cena_gd']).siblings().addClass('active');
           $('#price_pd').val(data[0]['cena_pd']).siblings().addClass('active');
           $('#itemId').val(id);
       },
    })
  });


});


$('#update_item_form').submit(function () {
  var form=document.getElementById('update_item_form');//retrieve the form as a DOM element

  var input = document.createElement('input');//prepare a new input DOM element
  input.setAttribute('name', 'action');//set the param name
  input.setAttribute('value', 'updateItem');//set the value
  input.setAttribute('type', 'hidden')//set the type, like "hidden" or other

  form.appendChild(input);//append the input to the form

  form.submit();
});


function getTypeSelect() {
  $.ajax({
      url: "./item_select_values.php",
      type: 'post',
      data: {type:'type'},
      dataType: 'json',
      success:function(response){
          var len = response.length;
          for( var i = 0; i<len; i++){
              console.log('elo');
              var type_id = response[i]['rodzaj_id'];
              var type_name = response[i]['rodzaj_nazwa'];

              $("#type_name").append("<option value='"+type_id+"'>"+type_name+"</option>");
          }
      }
  });
}


function getGroupSelect() {
  $.ajax({
      url: "./item_select_values.php",
      type: 'post',
      data: {type:'group'},
      dataType: 'json',
      success:function(response){
        console.log('elo2');
          var len = response.length;
          for( var i = 0; i<len; i++){
              var group_id = response[i]['szereg_id'];

              var group_name = response[i]['szereg_nazwa'];
              console.log(group_name);
              $("#group_name").append("<option value='"+group_id+"'>"+group_name+"</option>");
          }
      }
  });
}
