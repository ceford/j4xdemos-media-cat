<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

Factory::getDocument()->getWebAssetManager()
	->useScript('webcomponent.toolbar-button');

$title = Text::_('COM_MEDIACAT_CREATE_NEW_FOLDER');
?>
<joomla-toolbar-button>
	<button id="mediacatCreateFolder" class="btn btn-info"
	onclick="mediacatCreateFolder();"
	disabled="true">
		<span class="icon-folder icon-fw" aria-hidden="true"></span>
		<?php echo $title; ?>
	</button>
</joomla-toolbar-button>
