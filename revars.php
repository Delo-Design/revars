<?php defined('_JEXEC') or die;

/**
 * @package    Revars
 *
 * @author     Cymbal <cymbal@delo-design.ru> and Progreccor
 * @copyright  Copyright © 2020 Delo Design. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://hika.su
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;


/**
 * Revars plugin.
 *
 * @package   revars
 * @since     1.1
 */
class plgSystemRevars extends CMSPlugin
{

	/**
	 * Application object
	 *
	 * @var    CMSApplication
	 * @since  1.0.0
	 */
	protected $app;


	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;


	public function onAfterRender()
	{

		$admin      = $this->app->isClient('administrator');
		$customizer = !empty($this->app->input->get('customizer'));

		if ($admin || $customizer)
		{
			return;
		}

		$search    = [];
		$replace   = [];
		$variables = $this->params->get('variables');
		$replaces  = $this->params->get('replaces');
		$utms      = $this->params->get('utms');
		$body      = $this->app->getBody();
		$get       = $this->app->input->get->getArray();

		foreach ($get as $name => $item)
		{
			foreach ($utms as $variable)
			{
				if ($name == $variable->variable)
				{
					$variable->value = $item;
				}
			}
		}

		// системные переменные
		/*  можно дополнить переменные:
			- какой компонент работает,
			- какой view,
			- id активного меню
			- id активного пункта меню
			- название сайта из конфигурации,
			- время сервера,
			- время джумлы
			- имя пользователя
			- email пользователя
			- id пользователя
			- доп. поля пользователя
			- константа языка
			- загрузка модуля по id
			- загрузка позиции по названию
		*/

		$variables = array_merge([
			(object) [
				'variable' => 'server_name',
				'value'    => $_SERVER['SERVER_NAME'],
			],
			(object) [
				'variable' => 'http_host',
				'value'    => $_SERVER['HTTP_HOST'],
			],
			(object) [
				'variable' => 'request_uri',
				'value'    => $_SERVER['REQUEST_URI'],
			],
			(object) [
				'variable' => 'remote_addr',
				'value'    => $_SERVER['REMOTE_ADDR'],
			]
		], $variables);

		PluginHelper::importPlugin('revars');
		$dispatcher = \JEventDispatcher::getInstance();
		$results    = $dispatcher->trigger('onRevarsAddVariables');

		if (is_array($results))
		{
			$variables = array_merge($results, $variables);
		}

		foreach ($variables as $variable)
		{
			$search[]  = '{VAR_' . strtoupper($variable->variable) . '}';
			$replace[] = $variable->value;
		}

		foreach ($utms as $variable)
		{
			$search[]  = '{VAR_' . strtoupper($variable->variable) . '}';
			$replace[] = $variable->value;
		}

		$count       = 1;
		$searchCount = count($search);
		for ($i = 1; $i < $searchCount; $i++)
		{
			$body = str_replace($search, $replace, $body, $count);
		}

		foreach ($replaces as $replace)
		{
			$replaceString = str_replace($search, $replace, $replace->replace, $count);
			$body          = str_replace($replace->search, $replaceString, $body);
		}

		$this->app->setBody($body);

	}


}