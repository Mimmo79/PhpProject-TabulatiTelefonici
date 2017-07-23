******** explode()

<?php
// Example 1
$pizza  = "piece1 piece2 piece3 piece4 piece5 piece6";
$pieces = explode(" ", $pizza);
echo $pieces[0]; // piece1
echo $pieces[1]; // piece2

// Example 2
$data = "foo:*:1023:1000::/home/foo:/bin/sh";
list($user, $pass, $uid, $gid, $gecos, $home, $shell) = explode(":", $data);
echo $user; // foo
echo $pass; // *

?>


******** list()

<?php

$info = array('caffè', 'scuro', 'caffeina');

// assegna a tutte le variabili
list($bevanda, $colore, $componente) = $info;
echo "Il $bevanda è $colore e la $componente lo rende speciale.\n";

// assegna solo in parte
list($bevanda, , $componente) = $info;
echo "Il $bevanda ha la $componente.\n";

// oppure assegnamo solo l'ultima variabile
list( , , $componente) = $info;
echo"Ho voglia di $bevanda!\n";

?>


******** array_shift()

<?php
$stack = array("orange", "banana", "apple", "raspberry");
$fruit = array_shift($stack);
print_r($stack);
?>

The above example will output:

Array
(
    [0] => banana
    [1] => apple
    [2] => raspberry
)

and orange will be assigned to $fruit.



*********** Mysql
inserire un campo data come stringa es. '2017-10-11' , '20171011' è valido
inserire un campo data come intero es. 20171011 è valido





