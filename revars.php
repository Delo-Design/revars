<?php
/**
 * @package    revars
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

/**
 * Uniqmetaid plugin.
 *
 * @package   uniqmetaid
 * @since     1.0.0
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
	 * Database object
	 *
	 * @var    DatabaseDriver
	 * @since  1.0.0
	 */
	protected $db;

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
		$vars=$this->params->get('variables');
		$reps=$this->params->get('replaces');
		$customizer = !empty($this->app->input->get('customizer'));

		$r     = $this->app->input;
		$get   = $r->get->getArray();


		if($admin || $customizer)
		{
			return;
		}

		JLoader::register('RevarsHelper', JPATH_SITE . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['plugins', 'system', 'revars', 'helper.php']));

		$body = $this->app->getBody();
		$body = RevarsHelper::replace($body, $vars, $reps);
		$this->app->setBody($body);
	}


}