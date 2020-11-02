<?php

if(!empty($_FILES['csv_file']['name'])) {
    $file_name = $_FILES['csv_file']['name'];
    $uploaddir = $_SERVER["DOCUMENT_ROOT"]. '/ip_reporting_app/uploads/';
    $uploadfile = $uploaddir . basename($_FILES['csv_file']['name']);

    if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $uploadfile)) {
        $file_data = fopen($uploadfile, 'r');

        while($row = fgetcsv($file_data, 0, ";")) {
          $row = array_map( "convert", $row );
          if($row[0]) {
            $data[] = array(
                'faktura_numer' => $row[0],
                'data_wystawienia' => date("Y-m-d", strtotime($row[1])),
                'kontrahent' => $row[2],
                'waluta_kod' => $row[3],
                'kurs' => (float)(str_replace(',', '.', $row[4])) == 1 ? 1 : (float)(str_replace(',', '.', ($row[4] / 100))) ,
                'kraj_kod' => $row[5],
                'wojewodztwo_nazwa' => $row[6],
                'sprzedawca' => $row[8],
                'przelew' => intVal($row[9]),
                'dostawa' => intVal($row[10]),
                'wartosc_faktury' => (float)(str_replace(',', '.', $row[11])),
                'towar_nazwa' => $row[12],
                'jednostka' => $row[14],
                'ilosc' => intVal($row[15]),
                'wartosc_pozycji' => (float)(str_replace(',', '.', $row[16])),
                'cena' => (float)str_replace(',', '.', $row[17]),
                'edytuj' => "<button type='button' class='table-remove btn btn-danger btn-rounded btn-sm my-0 waves-effect waves-light'>Usuń</button>"
            );
          }
        }
        echo json_encode($data);
    } else {
        echo "Upload failed";
    }


}

function convert( $str ) {
    return iconv( "Windows-1250", "UTF-8", $str );
}



?>
