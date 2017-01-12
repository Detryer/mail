<?php

namespace Application;

class Debug
{
	static function r()
	{
		echo "<pre>\n";
			self::cyclo_r(func_get_args());
		echo "</pre>\n";
	}

	static function sr()
	{
		echo "<pre>\n";
			self::cyclo_r(func_get_args());
			self::trace_dump();
		echo "</pre>\n";
	}

	static function fr()
	{
		ob_start();
			self::cyclo_r(func_get_args());
			debug_print_backtrace();
		self::saveOutput();
	}

	static function xfr()
	{
		$p = func_get_args();
		ob_start();
			self::cyclo_r($p);
			self::trace_dump();
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/temp/_'.$p[0], ob_get_clean());
	}

	static function lfr()
	{
		$p = func_get_args();
		ob_start();
			self::cyclo_r($p);
			self::trace_dump();
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/temp/_'.$p[0].'_'.str_replace(' ', '_', microtime()).'.log', ob_get_clean());		
	}

	private static function trace_dump()
	{
		ob_start();
			debug_print_backtrace();
		$s = ob_get_clean();
		$i = strpos($s, '#2  ');
		echo substr($s, $i);
	}

	private static function cyclo_r($p)
	{
		foreach ($p as $i => $v) {
			print_r($v);
			echo "\n". $i ."- - - - - - - - - - - - -\n\n";
		}
	}

	private static function saveOutput($file = '')
	{
		if ( $file === '' )	{
			$file = $_SERVER['DOCUMENT_ROOT'].'/temp/'.str_replace(' ', '_', microtime()).'.log';
		} else {
			$file = $_SERVER['DOCUMENT_ROOT'].'/temp/'.$file.'.log';
		}
		file_put_contents($file, ob_get_clean());
	}
}
