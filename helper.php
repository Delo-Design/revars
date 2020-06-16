<?php

/**
 * @package    revars
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;


class RevarsHelper
{

	/**
	 * @param $str - тело сайта
	 *
	 * @param $vars - переменные
	 * @param $reps - замены
	 *
	 *
	 * @return mixed
	 *
	 * @since version
	 */
	public static function replace($str, $vars, $reps)
	{



		$allVariables = [
			(object) [
				'variable' => 'server_name',
				'value' => $_SERVER['SERVER_NAME'],
			],
			(object) [
				'variable' => 'http_host',
				'value' => $_SERVER['HTTP_HOST'],
			],
			(object) [
				'variable' => 'request_uri',
				'value' => $_SERVER['REQUEST_URI'],
			],
			(object) [
				'variable' => 'remote_addr',
				'value' => $_SERVER['REMOTE_ADDR'],
			]
		];

		foreach ($vars as $variable)
		{
			$allVariables[] = (object)$variable;
		}

		$allVariables = array_reverse($allVariables);

		foreach ($allVariables as $variable)
		{
			$str = str_replace('{VAR_' . strtoupper($variable->variable) . '}', $variable->value, $str);
		}

		foreach ($reps as $replace)
		{
			$replaceString = $replace->replace;

			foreach ($allVariables as $variable)
			{
				$replaceString = str_replace('{VAR_' . strtoupper($variable->variable) . '}', $variable->value, $replaceString);
			}

			$str = str_replace($replace->search, $replaceString, $str);
		}

		return $str;
	}


	//todo добавить contentPrepare и в админке выбор где вызвать
}