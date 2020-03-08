$(document).ready(function(){

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


});
