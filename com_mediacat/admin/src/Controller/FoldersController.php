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
use J4xdemos\Component\Mediacat\Administrator\Helper\FolderHelper;

/**
 * Media Manager Component Controller
 *
 * @since  4.0.0
 */
class FoldersController extends BaseController
{
	/*
	 * get the folder tree to be indexed separately
	 *
	 *  return a jason encoded array
	 */
	public function getTree()
	{
		$this->checkToken();
		$jform = $this->input->get('jform', '', 'array');
		$folder = $jform['activepath'];
		$model = $this->getModel();
		$result = $model->getFolders($folder);
		echo json_encode($result);
		jexit();
	}

	/*
	 * delete a folder if it is empty
	 *
	 *  return a json encoded message
	 */
	public function deleteifempty()
	{
		// Check for request forgeries.
		$this->checkToken();
		$app = Factory::getApplication();
		$jform = $this->input->get('jform', '', 'array');
		$folder = $jform['activepath'];

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
		$this->setRedirect('index.php?option=com_mediacat&view=folders');
	}

	/*
	 * index all of the files in a given folder
	 *
	 *  return a json encoded message
	 */
	public function hasher()
	{
		// Check for request forgeries.
		$this->checkToken();
		$jform = $this->input->get('jform', '', 'array');
		$folder = $jform['activepath'];
		$media_type = $jform['media_type'];
		$model = $this->getModel();
		$result = $model->getHashes($media_type, $folder);
		echo json_encode($result);//$folder . ' = ' . $result);
		// not finished
		jexit();
	}

	/*
	 * index all of the files in a given folder
	 *
	 *  return a json encoded message
	 */
	public function indexer()
	{
		// Check for request forgeries.
		$this->checkToken();
		$jform = $this->input->get('jform', '', 'array');
		$folder = $jform['activepath'];
		$media_type = $jform['media_type'];
		$model = $this->getModel();
		$result = $model->getFiles($media_type, $folder);
		echo json_encode($result);//$folder . ' = ' . $result);
		// not finished
		jexit();
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
		$this->setRedirect('index.php?option=com_mediacat&view=folders');
	}
}
