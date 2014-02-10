<?php
$array = array('DB');


for ($i=0;$i < count($array); $i++ )
{

    $x++;
    $empresa["hostname"][$i]="localhost";
    $empresa["port"][$i]="5432";
    $empresa["database"][$i]=$array[$i];
    $empresa["login"][$i]="postgres";
    $empresa["password"][$i]="";
    $empresa["gestor"][$i]="POSTGRES";
    $empresa["width"][$i]="80";
    $empresa["height"][$i]="40";
    $empresa["logo"][$i]="logo.jpg";

}
//este 
?>
