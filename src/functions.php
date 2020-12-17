<?php

function env($key, $default = null)
{
	$envFilePath = __DIR__.'/../.env';
	$keyQuoted = '"'.$key.'"';
	$value = exec("awk -F '[=]' '$1==$keyQuoted {print $2}' $envFilePath");
	if ($value === "") {
		return $default;
	}
	return $value;
}