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
                'cena zero' => $row[1],
                'towar' => $row[2],
                'nazwa' => $row[3],
                'ilosc' => $row[4],
                'jm' => $row[5],
                'cena' => $row[6]
            );
        }
        echo json_encode($data);
    } else {
        echo "Upload failed";
    }


}


?>
