<?php
// $cars = array("volvo","BMW","Toyota");
// print_r($cars);
// echo"I like ".$cars[0];

$cars= array("volvo","bmw", "toyota");
echo"I like".$cars[1]."and".$cars[2];

$age = array ("Peter"=>"43","Jane"=>"28","James"=>"40");
print_r($age);
echo"br>";

foreach($age as $key=>$value){
    echo"My name is:".$key."and age is:".$value;
    echo"<br/r>";
}
$zap = array(
    array("Ankit","Ram","Shyam"),
    array()
);
print_r($zap);

?>