<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$view = $this->options->get('view');

?>
<joomla-toolbar-button id="status-group-children-create-folder" task="">
	<button id="mediacatCreateFolder" class="button-mediacat dropdown-item"
	onclick="mediacatCreateFolder('<?php echo $view; ?>');">
		<span class="fa-folder-plus icon-fw" aria-hidden="true"></span>
		<?php echo Text::_('COM_MEDIACAT_CREATE_NEW_FOLDER'); ?>
	</button>
</joomla-toolbar-button>
