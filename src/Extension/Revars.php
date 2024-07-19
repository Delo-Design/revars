<?php namespace Joomla\Plugin\System\Revars\Extension;

defined('_JEXEC') or die;

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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

/**
 * Revars plugin.
 *
 * @package   revars
 * @since     1.1
 */
class Revars extends CMSPlugin implements SubscriberInterface
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

	protected $variables_all = [];

	protected $variables_prepare = ['keys' => [], 'values' => []];

	public static function getSubscribedEvents(): array
	{
		return [
			'onBeforeRender'        => 'onBeforeRender',
			'onAfterRender'         => 'onAfterRender',
			'onMailBeforeRendering' => 'onMailBeforeRendering',
		];
	}

	public function onBeforeRender()
	{
		$admin      = $this->app->isClient('administrator');
		$customizer = !empty($this->app->input->get('customizer'));

		if ($admin || $customizer)
		{
			return;
		}

		$this->prepareVariables();
		$nesting = (int) $this->params->get('nesting', 1);
		$title   = Factory::getDocument()->getTitle();

		for ($i = 1; $i <= $nesting; $i++)
		{
			$title = str_replace($this->variables_prepare['keys'], $this->variables_prepare['values'], $title);
		}

		Factory::getDocument()->setTitle($title);
	}

	public function onAfterRender()
	{

		$admin      = $this->app->isClient('administrator');
		$customizer = !empty($this->app->input->get('customizer'));

		if ($admin || $customizer)
		{
			return;
		}

		$utmtags           = $this->params->get('utmtags');
		$languageConstants = $this->params->get('constants');

		$r          = $this->app->input;
		$get        = $r->get->getArray();
		$weHaveUTMS = false;
		if (!empty($utmtags))
		{
			foreach ($get as $name => $item)
			{
				foreach ($utmtags as $variable)
				{
					if ($name == $variable->variable)
					{
						$variable->value = strip_tags($item);
						$weHaveUTMS      = true;
					}
				}
			}
		}

		$body = $this->app->getBody();

		$this->prepareVariables();
		$nesting = (int) $this->params->get('nesting', 1);

		// запускаем в цикле, потому что мы можем построить переменные вида {VAR_{VAR_SUBDOMAIN}_PHONE_FULL},
		// то есть переменные вложенные друг в друга

		for ($i = 1; $i <= $nesting; $i++)
		{
			$body = str_replace($this->variables_prepare['keys'], $this->variables_prepare['values'], $body);
		}

		// обрабатываем метки utm
		if ($weHaveUTMS)
		{
			foreach ($utmtags as $variable)
			{
				// добавляем им префикс VAR, оборачиваем в скобки и приводим к верхнему регистру
				$splitedBody = explode($variable->opentag, $body, 2);
				// если тег нашли - будем менять
				if (count($splitedBody) > 1)
				{
					$latestChunk = explode($variable->closetag, $body, 2);
					// проверяем есть ли оконечный тег
					if (count($latestChunk) > 1)
					{
						$body = $splitedBody[0] . $variable->opentag . $variable->opentag2 . $variable->value . $variable->closetag2 . $variable->closetag . $latestChunk[1];
					}
				}
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

	public function onMailBeforeRendering(Event $event): void
	{
		$template           = $event->getArgument(1);
		$all_variables      = $this->getVariables();
		$template_variables = [];

		// пока без вложенных переменных
		foreach ($all_variables as $variable)
		{
			$template_variables[str_replace(['{', '}'], '', $variable->variable)] = $variable->value;
		}

		$template->addTemplateData($template_variables);
	}

	public function getVariables()
	{
		$this->prepareVariables();

		return $this->variables_all;
	}

	public function getPrepareVariables()
	{
		$this->prepareVariables();

		return $this->variables_prepare;
	}

	protected function prepareVariables()
	{
		if (count($this->variables_all) > 0)
		{
			return true;
		}

		$vars         = $this->params->get('variables');
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

		if (is_array($results) && !empty($results))
		{
			if (is_object($results[0]))
			{
				$allVariables = array_merge($results, $allVariables);
			}
			else
			{
				foreach ($results as $result)
				{
					if (is_array($result))
					{
						$allVariables = array_merge($result, $allVariables);
					}
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

		foreach ($allVariables as $variable)
		{
			$this->variables_prepare['keys'][]   = $variable->variable;
			$this->variables_prepare['values'][] = $variable->value;
		}

		$this->variables_all = array_reverse($allVariables);

		return true;
	}

}
