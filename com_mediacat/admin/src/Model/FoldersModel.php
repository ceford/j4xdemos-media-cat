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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

/**
 * Media View Model
 *
 * @since  4.0.0
 */
class FoldersModel extends ListModel
{
	public function getFolders($media_type_selected)
	{
		$path = JPATH_SITE . '/' . $media_type_selected;
		$root = JPATH_SITE;
		$rootlen = strlen($root);

		$folders[] = '/' . $media_type_selected;

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
		return $folders;
	}
}
