<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CmsApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;
use J4xdemos\Component\Mediacat\Administrator\Helper\FolderHelper;

/**
 * Images list controller class.
 *
 * @since  1.6
 */
class ImagesController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_MEDIACAT_IMAGES';

	/*
	 * Create a new folder from data in the adminForm
	 *
	 *  redirect to the folders view
	 */
	public function deleteifempty()
	{
		// Check for request forgeries.
		$this->checkToken();
		FolderHelper::deleteifempty();

		// just deletedd the active branch so move up one
		$app = Factory::getApplication();
		$activepath = $app->getUserState('com_mediacat.images.filter.activepath');
		$parts = explode('/', $activepath);
		array_pop($parts);
		$newactivepath = implode('/', $parts);
		$app->setUserState('com_mediacat.images.filter.activepath', $newactivepath);

		$this->setRedirect('index.php?option=com_mediacat&view=images');
	}

		/*
	 * Create a new folder from data in the adminForm
	 *
	 *  redirect to the folders view
	 */
	public function newfolder()
	{
		// Check for request forgeries.
		$this->checkToken();
		FolderHelper::make();
		$this->setRedirect('index.php?option=com_mediacat&view=images');
	}
}
