# Joomla 4x Component - Media Cat

A component to manage media: Image files and Document files.

### Prerequisites

Joomla 4. It should not install or work on earlier Joomla versions.

### Installing

Download the zip file and install it in Joomla 4.

### Documentation

After installation, the Administrator menu has a Media Cat item with three sub-items: Images, Files and Folders. Images is the obvious choice - but there are no images to display. Go to the Folders page and select Index from the Actions drop-down. If you have a small number of images, say less than 1000, it should index all of them in a few seconds. Larger numbers may time out. In that case use the Index Selected Folder option to get a feel for how it works.

The indexer makes database entries for each image. Go back to the Images page to see the result. There is some documentation in the Help pages for each screen.

This is essentially Alpha 1. The Trash functions have not been implemented so the only items that can be deleted are empty folders. The Search functions have not been implemented either.

Two database tables are created on install, #__mediacat_images and #__mediacat_files. They are not removed on uninstallation. Two site root folders are also created: files and trash. They are not remove on uninstallation either.

## Author

* **Clifford E Ford**

## License

This project is licensed under the [GPL3 License](http://www.gnu.org/licenses/gpl-3.0.html)

## Acknowledgments

This component includes code from the following sources:

* SVG Sanitizer: https://github.com/darylldoyle/svg-sanitizer

* File Icon Vectors: https://github.com/dmhendricks/file-icon-vectors

* Mime Types List: https://gist.github.com/raphael-riel/1253986

* And of course - the Joomla Project.
