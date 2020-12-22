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

?>

<joomla-toolbar-button id="status-group-children-hash-on" task="">
	<button onclick="mediacatHashAll();" class="button-mediacat dropdown-item">
		<span class="fa-hashtag icon-fw" aria-hidden="true"></span>
		<?php echo Text::_('COM_MEDIACAT_TOOLBAR_BUTTON_HASH_ALL'); ?>
	</button>
</joomla-toolbar-button>
