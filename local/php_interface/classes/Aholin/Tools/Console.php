<?php

namespace Aholin\Tools;

class Console
{

	public static function write( $text, $color = '', $eol = PHP_EOL )
	{
		switch ($color)
		{
			case 'green':
				fwrite(STDOUT, "\033[0;32m".$text."\033[0m".$eol);
				break;
			case 'red':
				fwrite(STDOUT, "\033[0;31m".$text."\033[0m".$eol);
				break;
			case 'yellow':
				fwrite(STDOUT, "\033[1;33m".$text."\033[0m".$eol);
				break;
			case 'purple':
				fwrite(STDOUT, "\033[0;35m".$text."\033[0m".$eol);
				break;
			case 'cyan':
				fwrite(STDOUT, "\033[0;36m".$text."\033[0m".$eol);
				break;
			default:
				fwrite(STDOUT, $text.$eol);
				break;
		}
	}
}