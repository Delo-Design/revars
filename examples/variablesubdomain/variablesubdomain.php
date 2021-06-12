<?php defined('_JEXEC') or die;

/**
 * @package    Revars
 *
 * @author     Cymbal <cymbal@delo-design.ru> and Progreccor
 * @copyright  Copyright © 2021 Delo Design. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;


/**
 * Variablesubdomain plugin.
 *
 * @package   revars
 * @since     1.0
 */
class plgRevarsVariablesubdomain extends CMSPlugin
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


	public function onRevarsAddVariables()
	{
		JLoader::register('plgSystemMultisiteswitch', JPATH_PLUGINS . '/system/multisiteswitch/multisiteswitch.php');

		return [
			(object) [
				'variable' => '{VAR_SUBDOMAIN}',
				'value'    => strtoupper(plgSystemMultisiteswitch::$subDomain)
			]
		];
	}

}