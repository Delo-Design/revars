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
		Factory::getApplication()->getDocument()->addStyleDeclaration(<<<EOF
				.subform-table-sublayout-section .controls { margin-left: 0px; padding-right: 0; }
				.subform-table-sublayout-section .controls input { box-sizing: border-box;  }
				.subform-table-sublayout-section .controls textarea { box-sizing: border-box;  }
				.subform-table-sublayout-section table th { width: 30% !important;  } .subform-table-sublayout-section { max-width: 1440px;}
				.options-form .form-grid > .control-group:has(.subform-table-sublayout-section) > .control-label { display: none; }
				.options-form .form-grid > .control-group:has(.subform-table-sublayout-section) > .controls { margin-left: 0; width: 100%; max-width: 100%; }
				#attrib-forutmtags .subform-table-sublayout-section { overflow-x: auto; max-width: 100%; }
				#attrib-forutmtags .subform-table-sublayout-section table { min-width: 1320px; }
				#attrib-forutmtags .subform-table-sublayout-section table th { width: 15% !important; }
EOF
		);

		Factory::getApplication()->getDocument()->addScriptDeclaration(<<<EOF
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.subform-table-sublayout-section table').forEach(function (table) {
		table.classList.add('table-striped', 'table-bordered');
	});
});
EOF
		);

		return parent::getInput();
	}

}
