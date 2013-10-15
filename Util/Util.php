<?php
namespace Hasheado\BlogBundle\Util;

class Util
{
	/**
	 * urlize() function
	 */
	public static function urlize($str, $replace = array(), $delimiter = '-')
	{
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}

	/**
	 * monthNameToNumber() function
	 */
	public static function monthNameToNumber($monthName = null)
	{
		$months = array(
			'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
			'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
			'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
		);

		$r = (!is_null($monthName))? $months[$monthName] : $months;

		return $r;
	}
}