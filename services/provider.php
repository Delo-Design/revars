<?php \defined('_JEXEC') or die;

/**
 * @package    Revars
 *
 * @author     Cymbal <cymbal@delo-design.ru> and Progreccor
 * @copyright  Copyright © 2022 Delo Design. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://hika.su
 */

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\System\Revars\Extension\Revars;

return new class implements ServiceProviderInterface {

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function register(Container $container)
	{
		$container->set(PluginInterface::class,
			function (Container $container) {
				$plugin  = PluginHelper::getPlugin('system', 'revars');
				$subject = $container->get(DispatcherInterface::class);

				$plugin = new Revars($subject, (array) $plugin);
				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			}
		);
	}
};
