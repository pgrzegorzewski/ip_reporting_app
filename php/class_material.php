<?php 

include './materials/class_class_subject_material.php';

$class = $_POST['classNumber'];
$subject = $_POST['subject'];

$material = printMaterial($class, $subject);

echo $material;

function printMaterial($class, $subject)
{
    $material = new ClassSubjectMaterial($class, $subject);
    return $material->getMaterialText();
}
    

?>