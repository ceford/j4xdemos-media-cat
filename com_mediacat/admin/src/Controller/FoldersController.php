<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2007 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;

/**
 * Media Manager Component Controller
 *
 * @since  4.0.0
 */
class FoldersController extends BaseController
{
	/*
	 * Create a new folder from data in the adminForm
	 *
	 *  redirect to the folders view
	 */
	public function newfolder()
	{
		// Check for request forgeries.
		$this->checkToken();
		$app = Factory::getApplication();
		// get the path where the new folder is required
		$activepath = $this->input->get('activepath', '', 'path');
		$newfoldername = $this->input->get('newfoldername');
		// if there is a full stop
		if (strpos($newfoldername, '.') !== false)
		{
			$app->enqueueMessage(Text::_('COM_MEDIACAT_ERROR_STOP_IN_FOLDER_NAME'), 'danger');
		}
		else
		{
			$full_path = JPATH_SITE . $activepath . '/' . $newfoldername;
			if (Folder::exists($full_path))
			{
				$app->enqueueMessage(Text::_('COM_MEDIACAT_WARNING_FOLDER_EXISTS') . ' ' . $full_path, 'warning');
			}
			else
			{
				$result = Folder::create($full_path);
				if ($result)
				{
					$app->enqueueMessage(Text::_('COM_MEDIACAT_SUCCESS_FOLDER_CREATED') . ' ' . $full_path, 'success');
				}
				else
				{
					$app->enqueueMessage(Text::_('COM_MEDIACAT_ERROR_FOLDER_NOT_CREATED') . ' ' . $full_path, 'danger');
				}
			}
		}
		$this->setRedirect('index.php?option=com_mediacat&view=folders');
	}
}
