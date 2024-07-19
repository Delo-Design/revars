<?php namespace Joomla\Plugin\System\Revars\Field;

defined('_JEXEC') or die;

/**
 * @package    Revars
 *
 * @author     Cymbal <cymbal@delo-design.ru> and Progreccor
 * @copyright  Copyright © 2022 Delo Design. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://hika.su
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\SubformField;

class RevarssubformField extends SubformField
{

	public $type = 'revarssubform';

	public function getInput()
	{
		Factory::getDocument()->addStyleDeclaration(<<<EOF
				.subform-table-sublayout-section .controls { margin-left: 0px; padding-right: 0; }
				.subform-table-sublayout-section .controls input { box-sizing: border-box;  }
				.subform-table-sublayout-section table th { width: 30% !important;  } .subform-table-sublayout-section { max-width: 1440px;} #attrib-forutmtags .subform-table-sublayout-section table th { width: 18% !important; }
EOF
		);

		return parent::getInput();
	}

}