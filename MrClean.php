<?php
// Sanitization script
// Removes: <, >, \\, ;, /*, */
// Also escapes apostrophes to allow their use in text fields.

	function clean($data)
	{
		$cleaned = trim(strip_tags($data));
		$Regex = array('(<|>)','(\/\/)','(\;)','(\/\*)','(\*\/)','/\'/');
		$replacement = array('','','','','\\\'');
		$cleaned = preg_replace($Regex, $replacement, $cleaned);

		return $cleaned;
	}

?>