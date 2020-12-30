<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;

/**
 * Controller for a single mywalk
 *
 * @since  1.6
 */
class ImageController extends FormController
{
	protected $text_prefix = 'COM_MEDIACAT_IMAGE';

	public function delete()
	{
		$this->checkToken();
		$app = Factory::getApplication();

		$jform = $this->input->get('jform', '', 'array');
		$id = $jform['action_id'];

		// get the record for this item
		$item = $this->getRecord($id);

		// remove the item from the trash folder
		$params = ComponentHelper::getParams('com_mediacat');

		$trash_path = JPATH_SITE . '/' . $params->get('trash_path') . $item->folder_path;
		$target = $trash_path . '/' . $id . '-' . $item->file_name;

		$removed = File::delete($target);

		if (!empty($removed))
		{
			// if it worked, update the record
			$date = date('Y-m-d H:i:s');
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__mediacat')
			->set('state = -3')
			->set('date_deleted = ' . $db->quote($date))
			->where('id = ' . $id);
			$db->setQuery($query);
			$db->execute();
			$app->enqueueMessage(Text::_('COM_MEDIACAT_DELETE_SUCCESS') . ' ' . $id . ' ' . $item->file_name, 'success');
		}
		else {
			// otherwise an error message
			$app->enqueueMessage(Text::_('COM_MEDIACAT_WARNING_ITEM_NOT_DELETED') . ' ' . $id . ' ' . $item->file_name, 'warning');
		}
		$this->setRedirect('index.php?option=com_mediacat&view=images');
	}


	public function trash()
	{
		$this->checkToken();
		$app = Factory::getApplication();

		$jform = $this->input->get('jform', '', 'array');
		$id = $jform['action_id'];

		// get the record for this item
		$item = $this->getRecord($id);

		// move the item to the trash folder
		$params = ComponentHelper::getParams('com_mediacat');

		$trash_path = JPATH_SITE . '/' . $params->get('trash_path') . $item->folder_path;
		$source = JPATH_SITE . $item->folder_path . '/' . $item->file_name;
		$destination = $trash_path . '/' . $id . '-' . $item->file_name;

		// create the destination folder if necessary
		Folder::create($trash_path);

		// move the file
		$moved = File::move($source, $destination);

		if (!empty($moved))
		{
			// if it worked, update the record
			$date = date('Y-m-d H:i:s');
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__mediacat')
			->set('state = -2')
			->set('date_trashed = ' . $db->quote($date))
			->where('id = ' . $id);
			$db->setQuery($query);
			$db->execute();
			$app->enqueueMessage(Text::_('COM_MEDIACAT_WARNING_ITEM_TRASHED') . ' ' . $id . ' ' . $item->file_name, 'success');
		}
		else
		{
			// otherwise an error message
			$app->enqueueMessage(Text::_('COM_MEDIACAT_WARNING_ITEM_NOT_TRASHED') . ' ' . $id, 'warning');
		}

		$this->setRedirect('index.php?option=com_mediacat&view=images');
	}

	public function restore()
	{
		$this->checkToken();
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_mediacat');

		$jform = $this->input->get('jform', '', 'array');
		$id = $jform['action_id'];

		// get the record for this item
		$item = $this->getRecord($id);

		$trash_path = JPATH_SITE . '/' . $params->get('trash_path') . $item->folder_path;
		$source  = $trash_path . '/' . $id . '-' . $item->file_name;
		$destination = JPATH_SITE . $item->folder_path . '/' . $item->file_name;

		if (File::exists($destination))
		{
			$app->enqueueMessage(Text::_('COM_MEDIACAT_FILE_EXISTS_NOT_RESTORED') . $id . ' ' . $item->file_name, 'warning');
		}
		else
		{
			File::move($source, $destination);
			// if it worked, update the record
			$db = Factory::getDbo();
			$date = date('Y-m-d H:i:s');
			$query = $db->getQuery(true);
			$query->update('#__mediacat')
			->set('state = 1')
			->where('id = ' . $id);
			$db->setQuery($query);
			$db->execute();
			$app->enqueueMessage(Text::_('COM_MEDIACAT_FILE_RESTORED') . $id . ' ' . $item->file_name, 'success');
		}

		$this->setRedirect('index.php?option=com_mediacat&view=images');
	}

	protected function getRecord($id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from('#__mediacat')
		->where('id = ' . $id);
		$db->setQuery($query);
		return $db->loadObject();
	}

}
