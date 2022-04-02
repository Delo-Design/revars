<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Revars
 *
 * @copyright   Copyright 2022 Progreccor
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

class JFormFieldExtra extends JFormField {

	function getInput() {
		JFactory::getDocument()->addStyleDeclaration(
			'.subform-table-sublayout-section .controls { margin-left: 0px; padding-right: 0; }'
		);
		JFactory::getDocument()->addStyleDeclaration(
			'.subform-table-sublayout-section .controls input { box-sizing: border-box;  }'
		);
		JFactory::getDocument()->addStyleDeclaration(
			'.subform-table-sublayout-section table th { width: 30%; } .subform-table-sublayout-section { max-width: 1400px;} #attrib-forutmtags .subform-table-sublayout-section table th { width: 18%; }'
		);

		return ;
	}

}