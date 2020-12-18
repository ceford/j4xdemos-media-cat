<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use J4xdemos\Component\Mediacat\Administrator\Helper\MimetypesHelper;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

/**
 * Media View Model
 *
 * @since  4.0.0
 */
class FoldersModel extends ListModel
{
	/*
	 * Get a list of all files in one folder and catalogue them
	 *
	 * $folder string relative to site root
	 * $media_type string either 'images' or 'files'
	 *
	 * return a list of catalogued files
	 */
	public function getFiles($media_type, $folder)
	{
		if (empty($folder))
		{
			return array ('That', 'went', 'wrong');
		}
		$path = JPATH_SITE . $folder;

		$folders[] = '/' . $folder;
		$params = ComponentHelper::getParams('com_mediacat');
		$mh = new MimetypesHelper;

		$items = scandir($path);

		$new = 0;
		$old = 0;
		$dud = 0;

		foreach ($items as $item)
		{
			// skip directories
			if (is_dir($item))
			{
				continue;
			}
			// skip hidden files
			if (strpos($item, '.') == 0)
			{
				continue;
			}
			// get mime type
			$mime = $mh->getFileMimeType($path . '/' . $item);
			// is it an allowed type
			if (empty($mh->checkInAllowedExtensions($mime, $params, $media_type)))
			{
				continue;
			}
			// save data as image or file
			if ($media_type == 'image')
			{
				$result = $this->saveImageData($folder . '/' . $item);
			} else {
				$result = $this->saveFileData($folder . '/' . $item);
			}
			if ($result == 'new')
			{
				$new++;
			}
			else if ($result == 'old')
			{
				$old++;
			}
			else
			{
				$dud++;
			}
		}

		return array('Folder = ' . $folder, ' New = ' . $new, ' Old = ' . $old, ' Dud = ' . $dud);
	}

	protected function saveImageData($file)
	{
		$root = JPATH_SITE;

		list($width, $height, $type, $wandhstring) = getimagesize($root. $file);
		$size = filesize($root . $file);

		// get the file name and extension
		$filename = substr($file, strrpos($file, '/') + 1);
		$extension = substr($filename, strrpos($filename, '.') + 1);

		// takes 5x longer to hash - needs time saving ploy if file has not changed
		//$hash = hash('md5', $file);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// does the record exist
		$query->select('id');
		$query->from('#__mediacat_images');
		$query->where('file_path = ' . $db->quote($file));
		$db->setQuery($query);
		$id = $db->loadResult();

		// if yes then update it
		$query = $db->getQuery(true);
		if ($id)
		{
			$query->update('#__mediacat_images');
			$query->where('id = ' . $id);
			$result = 'old';
		}
		else
		{
			$query->insert('#__mediacat_images');
			$result = 'new';
		}
		$query->set('file_path = ' . $db->quote($file));
		$query->set('file_name = ' . $db->quote($filename));
		$query->set('extension = ' . $db->quote($extension));
		if ($extension != 'svg')
		{
			$query->set('width = ' . $db->quote($width));
			$query->set('height = ' . $db->quote($height));
		}
		$query->set('size = ' . $db->quote($size));
		//$query->set('hash = ' . $db->quote($hash));

		$db->setQuery($query);
		try
		{
			$db->execute();
		}
		catch (\RuntimeException $e)
		{
			// skip this one
			return 'dud';
		}
		return $result;
	}

	protected function saveFileData($file)
	{
		$root = JPATH_SITE;
		$size = filesize($root . $file);

		// get the file name and extension
		$filename = substr($file, strrpos($file, '/') + 1);
		$extension = substr($filename, strrpos($filename, '.') + 1);

		// takes 5x longer to hash - needs time saving ploy if file has not changed
		//$hash = hash('md5', $file);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// does the record exist
		$query->select('id');
		$query->from('#__mediacat_files');
		$query->where('file_path = ' . $db->quote($file));
		$db->setQuery($query);
		$id = $db->loadResult();

		// if yes then update it
		$query = $db->getQuery(true);
		if ($id)
		{
			$query->update('#__mediacat_files');
			$query->where('id = ' . $id);
			$result = 'old';
		}
		else
		{
			$query->insert('#__mediacat_files');
			$result = 'new';
		}
		$query->set('file_path = ' . $db->quote($file));
		$query->set('file_name = ' . $db->quote($filename));
		$query->set('extension = ' . $db->quote($extension));
		$query->set('size = ' . $db->quote($size));
		//$query->set('hash = ' . $db->quote($hash));

		$db->setQuery($query);
		try
		{
			$db->execute();
		}
		catch (\RuntimeException $e)
		{
			// skip this one
			return 'dud';
		}
		return $result;
	}

	public function getFolders($folder)
	{
		// if folder is empty - abort
		if (empty($folder))
		{
			return array ('That', 'went', 'wrong');
		}
		$path = JPATH_SITE . '/' . $folder;
		$root = JPATH_SITE;
		$rootlen = strlen($root);
		$folders[] = '/' . $folder;

		try
		{
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
				RecursiveIteratorIterator::SELF_FIRST);
			foreach($objects as $name => $object)
			{
				if (!is_dir($name))
				{
					continue;
				}
				$fileName = $object->getFilename();
				if ($fileName == '.' || $fileName == '..')
				{
					continue;
				}
				$folders[] = substr($name, $rootlen);
			}
			sort($folders);
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage('Bad path = ' . $path, 'warning');
		}
		return $folders;
	}
}
