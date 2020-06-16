<?php
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

defined('_JEXEC') or die;

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

		$admin = $this->app->isClient('administrator');
		$customizer = !empty($this->app->input->get('customizer'));

		if($admin || $customizer)
		{
			return;
		}

		$vars=$this->params->get('variables');
		$reps=$this->params->get('replaces');
		$utms=$this->params->get('utms');

		$r     = $this->app->input;
		$get   = $r->get->getArray();

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

		foreach ($utms as $variable)
		{
			$allVariables[] = (object)$variable;
		}


		$allVariables = array_reverse($allVariables);

		foreach ($allVariables as $variable)
		{
			$body = str_replace('{VAR_' . strtoupper($variable->variable) . '}', $variable->value, $body);
		}

		foreach ($reps as $replace)
		{
			$replaceString = $replace->replace;

			foreach ($allVariables as $variable)
			{
				$replaceString = str_replace('{VAR_' . strtoupper($variable->variable) . '}', $variable->value, $replaceString);
			}

			$body = str_replace($replace->search, $replaceString, $body);
		}

		$this->app->setBody($body);
	}


}