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
class TrashController extends FormController
{
	protected $text_prefix = 'COM_MEDIACAT_TRASH';

	public function delete()
	{
		$this->checkToken();
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_mediacat');

		$jform = $this->input->get('jform', '', 'array');
		$id = $jform['action_id'];
		$media_type = $jform['media_type'];
		if ($media_type == 'file')
		{
			$view = 'files';
		}
		else
		{
			$view = 'images';
		}

		$item_record = $this->getRecord($id, $view);

		// get the record from the trash table
		$trash_record = $this->getTrashRecord($id, $media_type);

		$trash_path = JPATH_SITE . '/' . $params->get('trash_path') . $item_record->folder_path;
		$source  = $trash_path . '/' . $id . '-' . $item_record->file_name;

		// update the records - even if the files do not exist
		$this->updateRecord($id, $view, -3);
		$this->updateTrashAfterDelete($trash_record->trash_id);

		if (File::exists($source))
		{
			File::delete($source);
			$app->enqueueMessage(Text::_('COM_MEDIACAT_FILE_DELETED') . $id, 'success');
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_MEDIACAT_FILE_DID_NOT_EXIST') . $id, 'warning');
		}

		$this->setRedirect('index.php?option=com_mediacat&view=' . $view);
	}

	public function restore()
	{
		$this->checkToken();
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_mediacat');

		$jform = $this->input->get('jform', '', 'array');
		$id = $jform['action_id'];
		$media_type = $jform['media_type'];
		if ($media_type == 'file')
		{
			$view = 'files';
		}
		else
		{
			$view = 'images';
		}

		// get the record from the trash table
		$trash_record = $this->getRecord($id, $view);

		$trash_path = JPATH_SITE . '/' . $params->get('trash_path') . $trash_record->folder_path;

		$source  = $trash_path . '/' . $id . '-' . $trash_record->file_name;

		$destination = JPATH_SITE . $trash_record->folder_path . '/' . $trash_record->file_name;

		if (File::exists($destination))
		{
			$app->enqueueMessage(Text::_('COM_MEDIACAT_FILE_EXISTS_NOT_RESTORED') . $id, 'warning');
		}
		else
		{
			File::move($source, $destination);
			$this->updateRecord($id, $view, 1);
			$app->enqueueMessage(Text::_('COM_MEDIACAT_FILE_RESTORED') . $id, 'success');
		}

		$this->setRedirect('index.php?option=com_mediacat&view=' . $view);
	}

	public function trash()
	{
		$this->checkToken();
		$app = Factory::getApplication();

		$jform = $this->input->get('jform', '', 'array');
		$id = $jform['action_id'];
		$media_type = $jform['media_type'];
		if ($media_type == 'file')
		{
			$view = 'files';
		}
		else
		{
			$view = 'images';
		}

		$result = false;

		// get data from the files or images folder
		$original_record = $this->getRecord($id, $view);

		// make an entry in the trash table
		$trash_record = $this->setRecord($original_record->id, $view, $media_type);

		// move the item to the trash folder
		// insert the trash record in the path before the file name
		$params = ComponentHelper::getParams('com_mediacat');
		$trash_path = JPATH_SITE . '/' . $params->get('trash_path') . $original_record->folder_path;

		$destination = $trash_path . '/' . $id . '-' . $original_record->file_name;
		$source = JPATH_SITE . $original_record->folder_path . '/' . $original_record->file_name;

		// created the destination folder if necessary
		Folder::create($trash_path);

		// move the file
		$moved = File::move($source, $destination);

		// update the database record
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__mediacat_' . $view))
		->set('state = -2')
		->set('date_updated = Now()')
		->where('id = ' . $id);
		$db->setQuery($query);
		$result = $db->execute();

		if (empty($moved))
		{
			$app->enqueueMessage(Text::_('COM_MEDIACAT_WARNING_ITEM_NOT_TRASHED') . ' ' . $id, 'warning');
		}
		else
		{
			//Folder::delete(JPATH_SITE . $folder);
			$app->enqueueMessage(Text::_('COM_MEDIACAT_WARNING_ITEM_TRASHED') . ' ' . $id, 'success');
		}

		$this->setRedirect('index.php?option=com_mediacat&view=' . $view);
	}

	protected function getRecord($id, $view)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__mediacat_' . $view))
		->where('id = ' . $id);
		$db->setQuery($query);
		return $db->loadObject();
	}

	protected function getTrashRecord($id, $media_type)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__mediacat_trash'))
		->where('id = ' . $id)
		->where('media_type = ' . $db->quote($media_type));
		$db->setQuery($query);
		return $db->loadObject();
	}

	protected function updateRecord($id, $view, $value)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__mediacat_' . $view))
		->set('state = ' . $value)
		->where('id = ' . $id);
		$db->setQuery($query);
		$db->execute();
	}

	protected function updateTrashAfterDelete($trash_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__mediacat_trash'))
		->set('state = -3')
		->where('trash_id = ' . $trash_id);
		$db->setQuery($query);
		$db->execute();
	}

	protected function setRecord($id, $view, $media_type)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->
			from($db->quoteName('#__mediacat_' . $view))->
			where('id = ' . $id);
		$db->setQuery($query);
		$record = $db->loadObject();

		$record->state = '-2';
		$query = $db->getQuery(true);
		$query->insert($db->quoteName('#__mediacat_trash'));
		foreach ($record as $key => $value)
		{
			$query->set($key . ' = ' . $db->quote($value));
		}
		$query->set('media_type = '. $db->quote($media_type));
		$db->setQuery($query);
		$db->execute();

		$result =  $db->insertid();
		return $result;
	}
}
