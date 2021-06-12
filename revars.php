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

		$vars = $this->params->get('variables');
		$utms = $this->params->get('utms');

		$r   = $this->app->input;
		$get = $r->get->getArray();

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


		$body = $this->app->getBody();

		$allVariables = [
			(object) [
				'variable' => '{VAR_SERVER_NAME}',
				'value'    => $_SERVER['SERVER_NAME'],
			],
			(object) [
				'variable' => '{VAR_HTTP_HOST}',
				'value'    => $_SERVER['HTTP_HOST'],
			],
			(object) [
				'variable' => '{VAR_REQUEST_URI}',
				'value'    => $_SERVER['REQUEST_URI'],
			],
			(object) [
				'variable' => '{VAR_REMOTE_ADDR}',
				'value'    => $_SERVER['REMOTE_ADDR'],
			]
		];


		// получаем переменные от сторонних плагинов
		PluginHelper::importPlugin('revars');
		$results = $this->app->triggerEvent('onRevarsAddVariables');

		if (is_array($results))
		{
			foreach ($results as $result)
			{
				if (is_array($result))
				{
					$allVariables = array_merge($result, $allVariables);
				}
			}
		}


		foreach ($vars as $variable)
		{
			$allVariables[] = (object) $variable;
		}

		foreach ($utms as $variable)
		{
			$allVariables[] = (object) $variable;
		}


		$allVariables = array_reverse($allVariables);
		$nesting      = (int) $this->params->get('nesting', 1);

		for ($i = 1; $i <= $nesting; $i++)
		{
			foreach ($allVariables as $variable)
			{
				$body = str_replace($variable->variable, $variable->value, $body);
			}
		}

		$this->app->setBody($body);
	}


}