<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2007 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace J4xdemos\Component\Mediacat\Administrator\View\Folders;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Media List View
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Holds a list of providers
	 *
	 * @var array|string
	 *
	 * @since   4.0.0
	 */
	protected $providers = null;

	/**
	 * The current path of the media manager
	 *
	 * @var string
	 *
	 * @since 4.0.0
	 */
	protected $currentPath;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse;
	 *                        automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   4.0.0
	 */
	public function display($tpl = null)
	{
		$model         = $this->getModel();

		$app = Factory::getApplication();

		$data = $app->input->post->get('jform', '', 'array');

		$params = ComponentHelper::getParams('com_mediacat');

		if (empty($data['media_type']) || $data['media_type'] == 'image')
		{
			$this->activepath = '/images';
			$this->media_type = 'image';
			$folder = $params->get('image_path');
		}
		else
		{
			$this->activepath = '/files';
			$this->media_type = 'file';
			$folder = $params->get('file_path');
		}

		$this->folders = $model->getFolders($folder);

		$this->prepareToolbar();

		$this->currentPath = Factory::getApplication()->input->getString('path');

		parent::display($tpl);
	}

	/**
	 * Prepare the toolbar.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function prepareToolbar()
	{
		$tmpl = Factory::getApplication()->input->getCmd('tmpl');

		// Get the toolbar object instance
		$toolbar  = Toolbar::getInstance('toolbar');
		$user = Factory::getUser();

		// Set the title
		ToolbarHelper::title(Text::_('COM_MEDIACAT_TITLE_BAR_FOLDERS'), 'folder mediacat');

		$layout = new FileLayout('toolbar.indexer', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$toolbar->appendButton('Custom', $layout->render([]), 'archive');

		// Add the upload and create folder buttons
		if ($user->authorise('core.create', 'com_mediacat'))
		{
			// Add the create folder button
			$layout = new FileLayout('toolbar.create-folder', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');

			$toolbar->appendButton('Custom', $layout->render([]), 'new');
		}

		// Add a delete button
		if ($user->authorise('core.delete', 'com_mediacat'))
		{
			// Instantiate a new FileLayout instance and render the layout
			$layout = new FileLayout('toolbar.trash');

			$toolbar->appendButton('Custom', $layout->render([]), 'trash');
		}

		// Add the preferences button
		if (($user->authorise('core.admin', 'com_mediacat') || $user->authorise('core.options', 'com_mediacat')) && $tmpl !== 'component')
		{
			ToolbarHelper::preferences('com_mediacat');
			ToolbarHelper::divider();
		}

		if ($tmpl !== 'component')
		{
			ToolbarHelper::help('JHELP_CONTENT_MEDIA_MANAGER');
		}
	}
}
