<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2020 Clifford E Ford
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use J4xdemos\Component\Mediacat\Administrator\Helper\MimetypesHelper;
use J4xdemos\Component\Mediacat\Administrator\Sanitizer\Sanitizer;

/**
 * Item Model for a single walk.
 *
 * @since  1.6
 */

class ImageModel extends AdminModel
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_MEDIACAT';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			return Factory::getUser()->authorise('core.delete', 'com_mediacat.mediacat.' . (int) $record->id);
		}

		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = Factory::getUser();

		// Check for existing article.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_mediacat.mediacat.' . (int) $record->id);
		}

		// Default to component settings if neither article nor category known.
		return parent::canEditState($record);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A Form object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_mediacat.image', 'image', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		return parent::getItem($pk);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = Factory::getApplication();
		$data = $app->getUserState('com_mediacat.edit.mediacat.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
		}

		$this->preprocessData('com_mediacat.mediacat', $data);

		return $data;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   4.0.0
	 */
	public function publish(&$pks, $value = 1) {
		/* this is a very simple method to change the state of each item selected */
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		$query->update('`#__mediacat`');
		$query->set('state = ' . $value);
		$query->where('id IN (' . implode(',', $pks). ')');
		$db->setQuery($query);
		$db->execute();
	}
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$savedFile = $this->saveImage($data);
		if (empty($savedFile))
		{
			return false;
		}

		return parent::save($data);
	}

	protected function saveImage(&$data)
	{
		$app = Factory::getApplication();
		$file = $app->input->files->get('jform', '', 'array');
		// array (size=1)
		// 'uploadfile' =>
		// array (size=5)
		// 'name' => string 'joomla-topmenu-test.png' (length=23)
		// 'type' => string 'image/png' (length=9)
		// 'tmp_name' => string '/private/var/tmp/phpVr8sUp' (length=26)
		// 'error' => int 0
		// 'size' => int 70637
		if (isset($data['id']) && empty($file['uploadfile']['name']))
		{
			// a file is not required if a record exists
			return true;
		}

		if (!isset($data['id']) && empty($file['uploadfile']['name']))
		{
			// a file is required but a file has not been selected
			$app->enqueueMessage(Text::_('COM_MEDIACAT_ERROR_FILE_NOT_SELECTED'), 'error');
			return false;
		}

		$params = ComponentHelper::getParams('com_mediacat');

		// check size
		if ($file['uploadfile']['size'] > ($params->get('image_upload_maxsize')*1024*1024))
		{
			$app->enqueueMessage(Text::_('COM_MEDIACAT_ERROR_WARNFILETOOLARGE'), 'error');
			unlink($file['uploadfile']['tmp_name']);
			return false;
		}

		$mime = $file['uploadfile']['type'];

		// check that mimtype has an extension in the allowed list
		$allowed = $this->checkInAllowedImageExtensions($mime, $params);

		if (empty($allowed))
		{
			$app->enqueueMessage(Text::_('COM_MEDIACAT_ERROR_NOT_AN_ALLOWED_TYPE'), 'error');
			unlink($file['uploadfile']['tmp_name']);
			return false;
		}

		//ToDo check that the uploaded file has an extension good for the mimetype

		$tmp_name = $file['uploadfile']['tmp_name'];

		// if this is an svg - sanitize it
		if ($mime == 'image/svg+xml') {
			// Create a new sanitizer instance
			$sanitizer = new Sanitizer();

			// Load the dirty svg
			$dirtySVG = file_get_contents($tmp_name);

			// Pass it to the sanitizer and get it back clean
			file_put_contents($tmp_name, $sanitizer->sanitize($dirtySVG));
		}

		$activePath = $app->getUserState('com_mediacat.images.activepath');
		$new_path = JPATH_SITE . $activePath . '/' . $data['file_name'];

		if (!File::upload($tmp_name, $new_path))
		{
			$app->enqueueMessage(Text::_('COM_MEDIACAT_ERROR_NOT_UPLOADED'), 'error');
			File::delete($tmp_name);
			return false;
		}
		File::delete($tmp_name);

		// add the file information to the data
		$activePath = $app->getUserState('com_mediacat.images.activepath');
		$data['file_path'] = $activePath . '/' . $data['file_name'];
		$file_path = JPATH_SITE . $data['file_path'];

		list ($width, $height, $type, $wandhstring) = getimagesize($file_path);
		$size = filesize($file_path);
		$hash = hash('md5', $file_path);
		$data['extension'] = substr($data['file_name'], strrpos($data['file_name'], '.') + 1);
		$data['width'] = $width;
		$data['height'] = $height;
		$data['size'] = $size;
		$data['hash'] = $hash;
		return true;
	}

	/**
	 * Check the Image Mime type is in the allowed extensions list
	 *
	 * @param   string   $mimetype     The mimetype of the uploaded image
	 *
	 * @return  bool    true if the mime type is in the allowed list, otherwise false
	 *
	 * @since   4.0
	 */
	protected function checkInAllowedImageExtensions($mime, $params)
	{
		$mthelper = new MimetypesHelper;
		$extensions = $mthelper->getExtensions($mime);

		$allowed_extensions = explode(',',$params->get('image_upload_extensions'));
		// check whether $extensions is in allowed extensions
		$result = array_intersect($allowed_extensions, $extensions);
		// return extensions that match the mimetype
		return count($result);
	}
	/**
	 * Get the Image Mime type
	 *
	 * @param   string   $file     The link to the file to be checked
	 * @param   boolean  $isImage  True if the passed file is an image else false
	 *
	 * @return  mixed    the mime type detected false on error
	 *
	 * @since   3.7.2
	 */
	protected function getImageMimeType($file)
	{
		// If we can't detect anything mime is false
		$mime = false;

		try
		{
			if (\function_exists('exif_imagetype'))
			{
				$mime = image_type_to_mime_type(exif_imagetype($file));
			}
			elseif (\function_exists('getimagesize'))
			{
				$imagesize = getimagesize($file);
				$mime      = $imagesize['mime'] ?? false;
			}
			elseif (\function_exists('mime_content_type'))
			{
				// We have mime magic.
				$mime = mime_content_type($file);
			}
			elseif (\function_exists('finfo_open'))
			{
				// We have fileinfo
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime  = finfo_file($finfo, $file);
				finfo_close($finfo);
			}
		}
		catch (\Exception $e)
		{
			// If we have any kind of error here => false;
			return false;
		}

		// We have a mime here
		return $mime;
	}

	/**
	 * Get the Image Mime type
	 *
	 * @param   string   $file     The link to the file to be checked
	 * @param   boolean  $isImage  True if the passed file is an image else false
	 *
	 * @return  mixed    the mime type detected false on error
	 *
	 * @since   3.7.2
	 */
	protected function getFileMimeType($file)
	{
		// If we can't detect anything mime is false
		$mime = false;

		try
		{
			if (\function_exists('mime_content_type'))
			{
				// We have mime magic.
				$mime = mime_content_type($file);
			}
			elseif (\function_exists('finfo_open'))
			{
				// We have fileinfo
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime  = finfo_file($finfo, $file);
				finfo_close($finfo);
			}
		}
		catch (\Exception $e)
		{
			// If we have any kind of error here => false;
			return false;
		}

		// We have a mime here
		return $mime;
	}
}