<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2007 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;

/**
 * Mediacat component helper.
 *
 * @since  4.0
 */

Class FolderHelper
{
	public static function make()
	{
		$app = Factory::getApplication();
		// get the path where the new folder is required
		$jform = $app->input->get('jform', '', 'array');
		$activepath = $jform['activepath'];
		$newfoldername = $jform['newfoldername'];
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
	}
}