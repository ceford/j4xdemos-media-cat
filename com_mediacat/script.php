<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

class com_mediacatInstallerScript
{

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		// check that the default file and trash folders exist
		$folder = JPATH_ROOT . '/files';
		if (!file_exists($folder))
		{
			try
			{
				// create with current umask - so should be same as images
				mkdir($folder);
			}
			catch (\Exception $e)
			{
				echo "<p>Error installing Media Cat: Could not create 'files' folder. Do it manually!</p>";
			}
		}

		$folder = JPATH_ROOT . '/trash';
		if (!file_exists($folder))
		{
			try
			{
				mkdir($folder);
			}
			catch (\Exception $e)
			{
				echo "<p>Error installing Media Cat: Could not create 'trash' folder. Do it manually!</p>";
			}
		}

		$params = ComponentHelper::getParams('com_mediacat');
		if ($params == '{}')
		{
			$this->setParams();
		}

		return true;
	}
	/**
	 * Sets parameter values in the extensions row of the extension table.
	 */
	protected function setParams()
	{
		$params = '{"image_upload_extensions":"bmp,gif,ico,jpg,jpeg,png","image_upload_maxsize":"10","image_path":"images","file_upload_extensions":"csv,doc,odp,ods,odt,pdf,ppt,txt,xls","file_upload_maxsize":"10","file_path":"files","trash_path":"trash"}';

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
		->update($db->quoteName('#__extensions'))
		->set('params = ' . $db->quote($params))
		->where("element = 'com_mediacat'");
		$db->setQuery($query)->execute();
	}
}