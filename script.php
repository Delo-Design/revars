<?php
/**
 * @package    Revars
 *
 * @author     Cymbal <cymbal@delo-design.ru> and Progreccor
 * @copyright  Copyright © 2022 Delo Design. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://hika.su
 */

// No direct access
defined('_JEXEC') or die;

class plgSystemRevarsInstallerScript
{
	function preflight($type, $parent)
	{
		if (!(version_compare(PHP_VERSION, '7.0.0') >= 0))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_RADICALFORM_WRONG_PHP'), 'error');

			return false;
		}

		jimport('joomla.version');
		// and now we check Joomla version
		$jversion = new JVersion();

		if (!$jversion->isCompatible('3.8'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_RADICALFORM_WRONG_JOOMLA'), 'error');

			return false;
		}
	}


	function postflight($type, $parent)
	{

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')->set('enabled=1')->where('type=' . $db->q('plugin'))->where('element=' . $db->q('revars'));
		$db->setQuery($query)->execute();

		JFactory::getApplication()->enqueueMessage(JText::_('PLG_REVARS_WELCOME_MESSAGE'), 'notice');
	}
}
