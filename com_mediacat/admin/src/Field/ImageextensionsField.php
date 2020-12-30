<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

/**
 * Clicks field.
 *
 * @since  1.6
 */
class ImageextensionsField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'Imageextensions';

	/**
	 * Method to get the field input markup for image extensions.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		// get the list of allowed image extensions
		$params = ComponentHelper::getParams('com_mediacat');
		$extensions = $params->get('image_upload_extensions');
		$html = '
			<select id="filter_extension" name="filter[extension]" class="custom-select" onchange="this.form.submit();">
			<option value="" selected="selected">' . Text::_('COM_MEDIACAT_SELECT_EXTENSION') . '</option>';
		$items = explode(',', $extensions);
		asort($items);
		foreach ($items as $item)
		{
			if ($item == $this->value)
			{
				$selected = '" selected="selected"';
			}
			else
			{
				$selected = '"';
			}
			$html .= '<option value="' . $item . $selected. '>' . $item  . '</option>' . "\n";
		}
		$html .= "</select>\n";
		return $html;
	}
}
