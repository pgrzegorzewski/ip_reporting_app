<?php

require '../app/connect.php';

class PredefinedTest
{  

    public function predefinedTestListGet($connection)
    {
        $testList = "
                            SELECT
                                  test_id,
                                  test_name
                            FROM  questions.tbl_test
                          ";
        
        $result =  pg_query($connection, $testList);
        $counter = 1;
        while($row = pg_fetch_assoc($result))
        {
            echo "<button class = 'predefined_test_btn' id = '".$row['test_id']."'>".$counter. ".".$row['test_name']."</button>&nbsp;&nbsp;&nbsp;&nbsp;";
            $counter++;
        }
        
    }
}