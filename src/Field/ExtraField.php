<?php namespace Joomla\Plugin\System\Revars\Field;

/**
 * @package     Joomla.Plugin
 * @subpackage  System.Revars
 *
 * @copyright   Copyright 2022 Progreccor
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

class ExtraField extends \Joomla\CMS\Form\FormField
{

	function getInput()
	{
		Factory::getDocument()->addStyleDeclaration(
			'.subform-table-sublayout-section .controls { margin-left: 0px; padding-right: 0; }'
		);
		Factory::getDocument()->addStyleDeclaration(
			'.subform-table-sublayout-section .controls input { box-sizing: border-box;  }'
		);
		Factory::getDocument()->addStyleDeclaration(
			'.subform-table-sublayout-section table th { width: 30% !important;  } .subform-table-sublayout-section { max-width: 1440px;} #attrib-forutmtags .subform-table-sublayout-section table th { width: 18% !important; }'
		);

		return;
	}

}