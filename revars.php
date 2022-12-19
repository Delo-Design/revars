<?php
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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;


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

		$body = $this->app->getBody();
		$this->app->setBody($this->renderVariables($body));
	}

	/**
	 * @param   string  $body
	 *
	 * @return string $body
	 *
	 * @since 1.4.1
	 */
	public function renderVariables(string $body)
	{

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

		$allVariables = $this->getVariables();
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

		return $body;
	}

	/**
	 * Adds Revars variables to Mail template, where it will replaces with
	 * \Joomla\CMS\Mail\MailTemplate::replaceTags method.
	 *
	 * @param                                  $template_id
	 * @param   \Joomla\CMS\Mail\MailTemplate  $Mailtemplate
	 *
	 * @author Sergey Tolkachyov
	 * @see    \Joomla\CMS\Mail\MailTemplate::send
	 * @since  1.4.1
	 */
	public function onMailBeforeRendering($template_id, Joomla\CMS\Mail\MailTemplate &$Mailtemplate): void
	{
		$allVariables              = $this->getVariables();
		$revars_rendered_variables = [];

		/**
		 * Joomla нужен готовый ассоциативный массив без вложенных переменных
		 * и без фигурных скобок. Рендерим их здесь.
		 */

		foreach ($allVariables as $variable)
		{
			if (substr_count($variable->value, '{') > 0)
			{
				$variable->value = $this->renderVariables($variable->value);
			}
			$revars_rendered_variables[str_replace(['{', '}'], '', $variable->variable)] = $variable->value;
		}

		$Mailtemplate->addTemplateData($revars_rendered_variables);
	}

	/**
	 * Return an array with all the revars variables for replace
	 *
	 * @return array
	 *
	 * @since 1.4.1
	 */
	private function getVariables(): array
	{
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

		return array_reverse($allVariables);
	}
}
