<?php namespace Joomla\Plugin\System\Revars\Formula;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Language\Text;
use RPN\FunctionsCollectionsInterface;

defined('_JEXEC') or die;

class JoomlaFuntions implements FunctionsCollectionsInterface
{
	protected array $functions = [
		'lang'         => 1,
		'sprintf'       => 2,
		'dateNow'       => 0,
		'dateNowFormat' => 1,
		'dateFormat'    => 2,
	];

	public function __invoke()
	{
		$output = [];

		foreach ($this->functions as $fn => $args)
		{
			$output[] = [$fn, $args, $this->$fn(...)];
		}

		return $output;
	}

	public function lang($a)
	{
		return Text::_($a);
	}

	public function sprintf($a, $b)
	{
		return Text::sprintf($a, $b);
	}

	public function dateNow()
	{
		return (new Date('now'))->format('Y-m-d H:i:s');
	}

	public function dateNowFormat($a)
	{
		return (new Date('now'))->format($a);
	}

	public function dateFormat($a, $b)
	{
		return (new Date($a))->format($b);
	}

}