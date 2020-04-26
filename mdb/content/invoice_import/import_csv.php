<?php

if(!empty($_FILES['csv_file']['name'])) {

    $file_name = $_FILES['csv_file']['name'];
    $uploaddir = '/opt/lampp/temp/';
    $uploadfile = $uploaddir . basename($_FILES['csv_file']['name']);


    if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $uploadfile)) {
        $file_data = fopen($uploadfile, 'r');
        fgetcsv($file_data);

        while($row = fgetcsv($file_data)) {
            $data[] = array(
                'lp' => $row[0],
                'towar' => $row[1],
                'ilosc' => $row[2],
                'jm' => $row[3],
                'cena' => $row[4],
                'edytuj' => "<button type='button' class='table-remove btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light'>Usuń</button><button type='button' class='row-edit btn btn-info btn-rounded btn-sm my-0 waves-effect waves-light'>Edytuj</button>"
            );
        }
        echo json_encode($data);
    } else {
        echo "Upload failed";
    }


}


?>
