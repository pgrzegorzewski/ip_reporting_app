$(document).ready(function(){

    $('#data-table').DataTable();

    $.ajax({
        url: "./invoice_import_filters.php",
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

    $.ajax({
        url: "./invoice_import_filters.php",
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

    $.ajax({
        url: "./invoice_import_filters.php",
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

    $.ajax({
        url: "./invoice_import_filters.php",
        type: 'post',
        data: {type:'client'},
        dataType: 'json',
        success:function(response){
            var len = response.length;
            for( var i = 0; i<len; i++){
                var client_id = response[i]['kontrahent_id'];
                var client_name = response[i]['kontrahent_nazwa'];

                $("#client").append("<option value='"+client_id+"'>"+client_name+"</option>");
            }
        }
    });

    $.ajax({
        url: "./invoice_import_filters.php",
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

});

$(document).ready(function(){
   $('#upload_csv').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: "./import_csv.php",
            method: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (jsonData) {
                $('#csv_file').val('');
                $("#data-table").dataTable().fnDestroy();
                $('#data-table').DataTable({
                    data : jsonData,
                    columns: [
                        {data: 'lp'},
                        {data: 'cena zero'},
                        {data: 'towar'},
                        {data: 'nazwa'},
                        {data: 'ilosc'},
                        {data: 'jm'},
                        {data: 'cena'},
                        {data: 'edytuj'}
                    ]
                });
                $("#data-table tr td").each(function() {
                  if( $(this).index() < $("#data-table").find("tbody tr:first td").length - 1) {
                    $(this).attr("contenteditable", true);
                  }
                });

                $('.table-remove').bind( "click", function() {
                  $(this).parents('tr').detach();
                });
            }
        })
   })
});
