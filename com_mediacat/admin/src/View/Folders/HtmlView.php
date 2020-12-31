<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_mediacat
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
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
		$params = ComponentHelper::getParams('com_mediacat');

		$jform = $app->input->get('jform', '', 'array');

		if (empty($jform))
		{
			$media_type = $app->getUserState('com_mediacat.folders.media_type', 'image');
		}
		else
		{
			$media_type = $jform['media_type'];
		}

		$app->setUserState('com_mediacat.folders.media_type', $media_type);

		if ($media_type == 'image')
		{
			$this->activepath = '/' . $params->get('image_path');
			$this->media_type = 'image';
			$folder = '/' . $params->get('image_path');
		}
		else
		{
			$this->activepath = '/' . $params->get('file_path');
			$this->media_type = 'file';
			$folder = '/' . $params->get('file_path');
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


		$dropdown = $toolbar->dropdownButton('status-group')
		->text('JTOOLBAR_CHANGE_STATUS')
		->toggleSplit(false)
		->icon('icon-ellipsis-h')
		->buttonClass('btn btn-action');

		$childBar = $dropdown->getChildToolbar();

		$layout = new FileLayout('toolbar.index-one', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$childBar->appendButton('Custom', $layout->render([]), 'archive');

		$layout = new FileLayout('toolbar.index-all', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$childBar->appendButton('Custom', $layout->render([]), 'archive');

		$layout = new FileLayout('toolbar.hash-one', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$childBar->appendButton('Custom', $layout->render([]), 'hashtag');

		$layout = new FileLayout('toolbar.hash-all', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
		$childBar->appendButton('Custom', $layout->render([]), 'hashtag');

		if ($user->authorise('core.create', 'com_mediacat'))
		{
			$dropdown = $toolbar->dropdownButton('folders')
			->text('COM_MEDIACAT_FOLDERS')
			->toggleSplit(false)
			->icon('icon-folder')
			->buttonClass('btn btn-action');

			$childBar = $dropdown->getChildToolbar();

			// Add the create folder button
			$layout = new FileLayout('toolbar.create-folder', JPATH_COMPONENT_ADMINISTRATOR . '/layouts', array('view' => 'folders'));
			$childBar->appendButton('Custom', $layout->render([]), 'icon-folder-plus');

			$layout = new FileLayout('toolbar.delete-if-empty', JPATH_COMPONENT_ADMINISTRATOR . '/layouts', array('view' => 'folders'));
			$childBar->appendButton('Custom', $layout->render([]), 'icon-folder-minus');
		}

		// Add the preferences button
		if (($user->authorise('core.admin', 'com_mediacat') || $user->authorise('core.options', 'com_mediacat')) && $tmpl !== 'component')
		{
			ToolbarHelper::preferences('com_mediacat');
			ToolbarHelper::divider();
		}

		if ($tmpl !== 'component')
		{
			ToolbarHelper::help('folders', true);
		}
	}
}
