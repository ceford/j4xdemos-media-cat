<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>

<joomla-toolbar-button>
	<button id="mediacatIndexAll" class="button-mediacat dropdown-item" type="button">
		<span class="icon-archive icon-fw" aria-hidden="true"></span>
		<?php echo Text::_('COM_MEDIACAT_TOOLBAR_BUTTON_INDEX_ALL'); ?>
	</button>
</joomla-toolbar-button>
