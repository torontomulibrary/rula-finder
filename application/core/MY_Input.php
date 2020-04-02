<?php

class MY_Input extends CI_Input {
	function _clean_input_keys($str)
	{
		if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str))
		{
			if(isset($_GET[$str])) unset($_GET[$str]);
			if(isset($_POST[$str])) unset($_POST[$str]);
			if(isset($_COOKIE[$str])) unset($_COOKIE[$str]);
			
			log_message('warn', 'Unset GET/POST/COOKIE var : '.$str);
			
			return "badvar"; //Set the return string to something harmless. Doesn't matter since it won't be used regardless
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			$str = $this->uni->clean_string($str);
		}

		return $str;
	}
}