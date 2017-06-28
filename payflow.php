<?php
// Request constructor for PayPal Payflow Gateway API
// Contains credentials, place in non-public facing directory
$request = array(
    "PARTNER" => "PayPal",
    "VENDOR" => "snordgruv",
    "USER" => "snordgruv",
    "PWD" => "teambeard42", 
    "TENDER" => "C",
    "TRXTYPE" => "S",
    "CURRENCY" => "USD",
    "AMT" => $_POST['AMOUNT'],
    "ACCT" => $_POST['CREDITCARD'],
    "EXPDATE" => $_POST['EXPDATE']
  );

?>
