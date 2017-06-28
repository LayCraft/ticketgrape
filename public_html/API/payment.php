<?php

//PAYMENT FORM

session_start(); // Need to investigate why this is here. It works without.
global $environment;
$environment = "pilot";

echo '<p>Payflow direct credit card processing - basic demo</p>';

if(empty($_POST)) {

?>
<form method="post" autocomplete="off">
  Credit card: <input type="text" name="CREDITCARD" value="4111111111111111">
  <br>Expiration date: <input type="text" name="EXPDATE" value="1215">
  <br>Card security code: <input type="text" name="CVV2" value="123" disabled>
  <br>Amount: <input type="text" name="AMOUNT" placeholder="0.00">
  <br><input type="submit" value="Pay Now">
</form>
<?php

} else {
	require_once('PayflowNVPAPI.php');
}
