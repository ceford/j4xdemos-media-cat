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

$title = Text::_('COM_MEDIACAT_TOOLBAR_BUTTON_HASHER');

//	value="http://localhost/j4xdemos/administrator/index.php?option=com_mediacat&amp;view=indexer&amp;tmpl=component"

?>

<joomla-toolbar-button>
	<button id="mediacatHasher" onclick="mediacatHasher();" class="btn btn-primary"
		disabled="true">
		<span class="fa-hashtag icon-fw" aria-hidden="true"></span>
		<?php echo $title; ?>
	</button>
</joomla-toolbar-button>
