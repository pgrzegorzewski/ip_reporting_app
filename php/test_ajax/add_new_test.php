<?php 

session_start();

include '../../app/connect.php';

if(isset($_POST['questionAnswers']) && isset($_POST['questions']) && isset($_POST['testClass']) && isset($_POST['testCategory']) && isset($_POST['testName']))
{ 
    $testClass = $_POST['testClass'];   
    $testName = $_POST['testName'];
    $questionAnswers = $_POST['questionAnswers'];
    $questions= $_POST['questions'];
    
    $userSql =  pg_query($connection, "SELECT
                                            user_id
                                      FROM
                                            usr.tbl_user 
                                      WHERE username = '".$_SESSION['user']."'");
    
    $row = pg_fetch_assoc($userSql);
    $userId = $row['user_id'];
    echo $userId;
        
    $result =  pg_query($connection, "
                                        INSERT INTO
                                        questions.tbl_test
                                        (
                                            test_name,
                                            created_datetime,
                                            created_by_user_id,
                                            class_number
                                        )
                                        VALUES (
                                            '".$testName."',
                                            CURRENT_TIMESTAMP,
                                            ".$userId.",
                                            ".$testClass."
                                            )
                                        RETURNING test_id;");
    
    $row = pg_fetch_assoc($result);
    $testId = $row['test_id'];
    
    for ($i = 0; $i < sizeof($questions); $i++)
    {
        $result =  pg_query($connection, "
                                        INSERT INTO
                                        questions.tbl_question
                                        (
                                            question_text,
                                            category_id,
                                            question_order,
                                            is_image,
                                            test_id
                                            )
                                        VALUES (
                                            '".$questions[$i]."',
                                            1,
                                            ".($i+1).",
                                            0::BIT,
                                            ".$testId."
                                        )RETURNING question_id;");
        
        $row = pg_fetch_assoc($result);
        $questionId = $row['question_id'];
        
        for ($j = 0; $j < sizeof($questionAnswers); $j++)
        {
            if(($i+1) == $questionAnswers[$j][0])
            {
                $letter;
                switch ($questionAnswers[$j][3]):
                case 1:
                    $letter = "A";
                        break;
                case 2:
                    $letter = "B";
                    break;
                case 3:
                    $letter = "C";
                    break;
                case 4:
                    $letter = "D";
                    break;
                endswitch;
                
                $isAnswerTrue;
                
                switch ($questionAnswers[$j][2]):
                case 'true':
                    $isAnswerTrue = "1::BIT";
                    break;
                case 'false':
                    $isAnswerTrue = "0::BIT";
                    break;
                 endswitch;
                    
                pg_query($connection, "
                                        INSERT INTO 
                                          questions.tbl_question_answer
                                        ( 
                                          question_id,
                                          answer_text,
                                          is_true,
                                          question_answer_order,
                                          question_answer_label,
                                          is_image
                                        )
                                        VALUES (
                                          ".$questionId.", '".$questionAnswers[$j][1]."', ".$isAnswerTrue.", ".$questionAnswers[$j][3].", '".$letter."', 0::BIT)
                                        ;");
            }
        }
    }
    echo 'success - You have created a test';
}
else 
{
    echo 'some informations are missing';
} 
?>