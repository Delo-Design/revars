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

	public static $params;

	/**
	 * @param $str
	 *
	 * @return mixed
	 *
	 * @since version
	 */
	public static function replace($str)
	{

		if(empty(self::$params))
		{
			//выборка элемента
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName(['params']))
				->from('#__extensions')
				->where( 'element=' . $db->quote('revars'));
			$extension = $db->setQuery( $query )->loadObject();
			self::$params = new Registry($extension->params);
		}

		$allVariables = [];
		$variables = self::$params->get('variables', []);
		$replaces = self::$params->get('replaces', []);
		$systemVariables = [
			[
				'variable' => 'server_name',
				'value' => $_SERVER['SERVER_NAME'],
			],
			[
				'variable' => 'http_host',
				'value' => $_SERVER['HTTP_HOST'],
			],
			[
				'variable' => 'request_uri',
				'value' => $_SERVER['REQUEST_URI'],
			],
			[
				'variable' => 'remote_addr',
				'value' => $_SERVER['REMOTE_ADDR'],
			]
		];

		foreach ($systemVariables as $variable)
		{
			$allVariables[] = (object)$variable;
		}

		foreach ($variables as $variable)
		{
			$allVariables[] = (object)$variable;
		}

		$allVariables = array_reverse($allVariables);

		foreach ($allVariables as $variable)
		{
			$str = str_replace('{VAR_' . strtoupper($variable->variable) . '}', $variable->value, $str);
		}

		foreach ($replaces as $replace)
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