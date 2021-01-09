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

$view = $this->options->get('view');

?>

<joomla-toolbar-button>
	<button id="mediacatDeleteIfEmpty" class="button-mediacat dropdown-item"
		data-view="<?php echo $view; ?>">
		<span class="fa-folder-minus icon-fw" aria-hidden="true"></span>
		<?php echo Text::_('COM_MEDIACAT_TOOLBAR_BUTTON_DELETE_IF_EMPTY'); ?>
	</button>
</joomla-toolbar-button>
