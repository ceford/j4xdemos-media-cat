<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
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
	/*
	 *  Delete a folder if it is empty - called from Controllers
	 *
	 *  Queues a succes or fail message
	 *
	 * @return void
	 */
	public static function deleteifempty()
	{
		$app = Factory::getApplication();
		// get the path where the new folder is required
		$filters = $app->input->get("filter", '', 'array');
		$folder = $filters['activepath'];
		$nfiles = count(scandir(JPATH_SITE . $folder)) - 2;

		if (!empty($nfiles))
		{
			$app->enqueueMessage(Text::sprintf('COM_MEDIACAT_WARNING_FOLDER_NOT_DELETED', $folder, $nfiles), 'warning');
		}
		else
		{
			Folder::delete(JPATH_SITE . $folder);
			$app->enqueueMessage(Text::_('COM_MEDIACAT_WARNING_FOLDER_DELETED') . ' ' . $folder, 'success');
		}
	}

	/*
	 * Create a folder - called from Controllers
	 *
	 * Queues a succes or fail message
	 *
	 * @return void
	 *
	 * @since   4.0.0
	 */
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
			$full_path = JPATH_SITE . $newfoldername;
			if (Folder::exists($full_path))
			{
				$app->enqueueMessage(Text::_('COM_MEDIACAT_WARNING_FOLDER_EXISTS') . ' ' . $newfoldername, 'warning');
			}
			else
			{
				$result = Folder::create($full_path);
				if ($result)
				{
					$app->enqueueMessage(Text::_('COM_MEDIACAT_SUCCESS_FOLDER_CREATED') . ' ' . $newfoldername, 'success');
				}
				else
				{
					$app->enqueueMessage(Text::_('COM_MEDIACAT_ERROR_FOLDER_NOT_CREATED') . ' ' . $newfoldername, 'danger');
				}
			}
		}
	}

	/*
	 * Creates the tree used in the images and files view
	 *
	 * @param  path $activepath the folder to be shown expanded
	 *
	 * @return html markup
	 *
	 * @since  4.0.0
	 */
	public static function getTree($activepath)
	{
		$root = JPATH_SITE;
		$path = '';

		$dirs = explode('/', $activepath);
		array_shift($dirs);
		$subs[] = '/' . $dirs[0];

		foreach ($dirs as $dir)
		{
			if (empty($dir))
			{
				continue;
			}
			// skip if dir begins with .
			if (strpos($dir, '.') === 0)
			{
				continue;
			}
			$path .= '/' . $dir;

			foreach (new \DirectoryIterator($root . $path) as $fileInfo)
			{
				if($fileInfo->isDot())
				{
					continue;
				}
				// skip if dir begins with .
				if (strpos($fileInfo->getFilename(), '.') === 0)
				{
					continue;
				}
				if ($fileInfo->isDir())
				{
					$subs[] = $path . '/' . $fileInfo->getFilename();
				}
			}
		}

		asort($subs);
		/* example:
		 * /files
		 * /files/odt
		 * /files/pdf
		 * /files/png
		 * /files/webp
		 */
		return $subs;
	}
}