<?php defined('_JEXEC') or die;

/**
 * @package    Revars
 *
 * @author     Cymbal <cymbal@delo-design.ru> and Progreccor
 * @copyright  Copyright © 2022 Delo Design. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://hika.su
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;


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


	public function onExtensionAfterSave($context, $table, $isNew)
	{
		if ($table->element === 'revars')
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('params'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('element') . ' = ' . $db->quote('revars'));
			$db->setQuery($query);
			$object = $db->loadObject();
			$params = new Registry($object->params);
			$utms   = $params->get('utms');

			foreach ($utms as &$utm)
			{
				$utm->variableforcopy = '{VAR_' . strtoupper($utm->variable) . '}';
			}

			$params->set('utms', $utms);

			$query      = $db->getQuery(true);
			$fields     = [
				$db->quoteName('params') . ' = ' . $db->quote($params->toString()),
			];
			$conditions = [
				$db->quoteName('element') . ' = ' . $db->quote('revars'),
			];
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$db->execute();
		}
	}


	public function onAfterRender()
	{

		$admin      = $this->app->isClient('administrator');
		$customizer = !empty($this->app->input->get('customizer'));

		if ($admin || $customizer)
		{
			return;
		}

		$vars              = $this->params->get('variables', []);
		$utms              = $this->params->get('utms', []);
		$languageConstants = $this->params->get('constants', []);

		$r   = $this->app->input;
		$get = $r->get->getArray();

		if (!empty($utms))
		{
			foreach ($get as $name => $item)
			{
				foreach ($utms as $variable)
				{
					if ($name == $variable->variable)
					{
						$variable->value = strip_tags($item);
					}
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


		if (!empty($vars))
		{
			foreach ($vars as $variable)
			{
				$allVariables[] = (object) $variable;
			}
		}

		$allVariables = array_reverse($allVariables);
		$nesting      = (int) $this->params->get('nesting', 1);

		// запускаем в цикле, потому что мы можем построить переменные вида {VAR_{VAR_SUBDOMAIN}_PHONE_FULL},
		// то есть переменные вложенные друг в друга

		for ($i = 1; $i <= $nesting; $i++)
		{
			foreach ($allVariables as $variable)
			{
				$body = str_replace($variable->variable, $variable->value, $body);
			}
		}

		// обрабатываем метки utm
		if (!empty($utms))
		{
			foreach ($utms as $variable)
			{
				// добавляем им префикс VAR, оборачиваем в скобки и приводим к верхнему регистру
				$variable->variable = '{VAR_' . strtoupper($variable->variable) . '}';
				$body               = str_replace($variable->variable, $variable->value, $body);
			}
		}
		// обрабатываем языковые константы
		if (!empty($languageConstants))
		{
			foreach ($languageConstants as $variable)
			{
				$body = str_replace($variable->variable, Text::_(strtoupper(trim($variable->value))), $body);
			}
		}

		$this->app->setBody($body);
	}


}
