<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array (
		'code' => '95',
		'patterns' => array (
				'national' => array (
						'general' => '/^[14578]\\d{5,7}|[26]\\d{5,8}|9(?:[258]|4\\d{1,2}|[679]\\d?)\\d{6}$/',
						'fixed' => '/^1(?:2\\d{1,2}|[3-5]\\d|6\\d?|[89][0-6]\\d)\\d{4}|2(?:[236-9]\\d{4}|4(?:0\\d{5}|\\d{4})|5(?:1\\d{3,6}|[02-9]\\d{3,5}))|4(?:2[245-8]|[346][2-6]|5[3-5])\\d{4}|5(?:2(?:20?|[3-8])|3[2-68]|4(?:21?|[4-8])|5[23]|6[2-4]|7[2-8]|8[24-7]|9[2-7])\\d{4}|6(?:0[23]|1[2356]|[24][2-6]|3[24-6]|5[2-4]|6[2-8]|7(?:[2367]|4\\d|5\\d?|8[145]\\d)|8[245]|9[24])\\d{4}|7(?:[04][24-8]|[15][2-7]|22|3[2-4])\\d{4}|8(?:1(?:2\\d?|[3-689])|2[2-8]|3[24]|4[24-7]|5[245]|6[23])\\d{4}$/',
						'mobile' => '/^17[01]\\d{4}|9(?:2[0-4]|4(?:0[0-4]\\d|[1379]\\d|[24][0-589]\\d|5\\d{2}|88)|5[0-6]|61?\\d|73\\d|8\\d|9(?:1\\d|[089]))\\d{5}$/',
						'voip' => '/^1333\\d{4}$/',
						'emergency' => '/^199$/' 
				),
				'possible' => array (
						'general' => '/^\\d{5,10}$/',
						'fixed' => '/^\\d{5,9}$/',
						'mobile' => '/^\\d{7,10}$/',
						'voip' => '/^\\d{8}$/',
						'emergency' => '/^\\d{3}$/' 
				) 
		) 
);
