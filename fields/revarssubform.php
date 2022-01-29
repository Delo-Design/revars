<?php

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('subform');
JFormHelper::loadFieldClass('folderlist');

/**
 * Class JFormFieldRadicalsubform
 */
class JFormFieldRevarssubform extends JFormFieldSubform
{


	/**
	 * @var string
	 */
	public $type = 'Revarssubform';


	/**
	 * Allow to override renderer include paths in child fields
	 *
	 * @return  array
	 *
	 * @since   3.5
	 */
	protected function getLayoutPaths()
	{
		return [
			JPATH_ROOT . '/plugins/fields/revars/layouts',
			JPATH_ROOT . '/layouts'
		];
	}


}