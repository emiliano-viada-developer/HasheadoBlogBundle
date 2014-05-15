<?php

namespace Hasheado\BlogBundle\Twig\Extensions;

/*
 * The locale extension has the purpose to translate date, currency, etc 
 * (based on local)
 * Has a dependency with Apache INTL module
 */
class HasheadoLocaleExtension extends \Twig_Extension
{
	/*
	 * Defined filters
	 */
	public function getFilters()
	{
		return array(
			'price' => new \Twig_Filter_Method($this, 'priceFilter'),
			'localeDate' => new \Twig_Filter_Method($this, 'localeDateFilter'),
		);
	}

	/*
	 * Price Filter
	 */
	public function priceFilter($number, $decimals = 2, $locale = 'en')
	{
		//Add locale logic
		$localeParams = $this->getLocaleParams($locale);
		$decPoint = $localeParams['currency']['decPoint'];
		$thousandsSep = $localeParams['currency']['thousandsSep'];

		//If decimals are ,00 dont show it.
		$decimals = (ceil($number) > $number)? $decimals : 0;

		$price = number_format($number, $decimals, $decPoint, $thousandsSep);
		$price = '$'.$price;

		return $price;
	}

	/*
	 * localeDate Filter
	 */
	public function localeDateFilter($date, $dateType = 'medium', $timeType = 'none', $locale = 'en', $pattern = null)
	{
		$formats = array(
	        'full'   => \IntlDateFormatter::FULL,
	        'long'   => \IntlDateFormatter::LONG,
	        'medium' => \IntlDateFormatter::MEDIUM,
	        'short'  => \IntlDateFormatter::SHORT,
	        'none'   => \IntlDateFormatter::NONE,
	    );

		$dateFormater = \IntlDateFormatter::create(
							$locale,
							$formats[$dateType],
							$formats[$timeType],
							date_default_timezone_get(),
							null,
							$pattern);

	    return $dateFormater->format($date);
	}

	/*
	 * getLocaleParams() method
	 * @desc Gets Locale parameters
	 */
	protected function getLocaleParams($locale = 'en')
	{
		$params = array();

		switch ($locale) {
			case 'es':
				$params['currency']['thousandsSep'] = '.';
				$params['currency']['decPoint'] = ',';
				break;
			case 'en':
			default:
				$params['currency']['thousandsSep'] = ',';
				$params['currency']['decPoint'] = '.';
				break;
		}

		return $params;
	}

	/*
	 * Get extension name
	 */
	public function getName()
	{
		return 'locale_extension';
	}
}