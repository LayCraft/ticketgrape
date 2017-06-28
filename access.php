<?php


// CHOOSE A DB CONNECTION:

// final deepblue database
//$mysqli = new mysqli("127.0.0.1", "c199grp06", "teambeard", "c199grp06");

// use this one if your IP is whitelisted or from deepblue
$mysqli = new mysqli("tiffanytang.ca", "marystai_comp199", "teambeard42", "marystai_c199grp06");

// use an ssh tunnel to connect from anywhere after running the following command from putty/terminal:
// (it is slow, and you must leave the ssh session open)
// ssh -L 3310:tiffanytang.ca:3306 cst###@deepblue.cs.camosun.bc.ca
// $mysqli = new mysqli("127.0.0.1", "marystai_comp199", "teambeard42", "marystai_c199grp06", "3310");

// tiffany's local db, you probably can't use this one :)
//$mysqli = new mysqli("127.0.0.1", "c199grp06", "teambeard", "marystai_c199grp06");
?>
