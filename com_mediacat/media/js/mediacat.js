/**
* PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
* OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.
**/

/**
 * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
(function (document) {
  'use strict';

  var onClick = function onClick() {
    var form = document.getElementById('adminForm');
    document.getElementById('filter-search').value = '';
    form.submit();
  };

  var onBoot = function onBoot() {
    var form = document.getElementById('adminForm');
    var element = form.querySelector('button[type="reset"]');

    if (element) {
      element.addEventListener('click', onClick);
    }

    document.removeEventListener('DOMContentLoaded', onBoot);
  };

  document.addEventListener('DOMContentLoaded', onBoot);


})(document);
	
function setFolder(newPath) {
	var folderPath = document.getElementById('filter_activepath');
	folderPath.value = newPath;
	var form = document.getElementById('adminForm');
	form.submit();
};

function mediacatAction(element, url) {
	var selected = element.value;
	var modal = document.getElementById('collapseModal'); 
	var title = document.getElementsByClassName('modal-title')[0];
	var body = document.getElementsByClassName('modal-body')[0];
	var id = element.id.split('_')[1];
	var paths = Joomla.getOptions(["system.paths"], 'No good');
	var root = paths.root;
	var rootFull = paths.rootFull;
	switch (selected) {
		case 'zoom':
			var tag = '<img src="'+root+'/'+url+'" class="cover">';
			title.innerText = 'Image Zoom';
			body.classList.add("text-center");
			body.innerHTML = tag;
			modal.open();
		break;
		case 'edit':
			location.replace('index.php?option=com_mediacat&view=edit');
		break;
		case 'download':
			location.assign(url);
		break;
		case 'share':
			// get a link to share
			var share = '<input class="form-control" type="text" value="' + rootFull + url + '" onclick="this.select();document.execCommand(\'copy\');" /> Click to Copy';
			body.innerHTML = share;
			modal.open();
		break;
		case 'image':
			// get an image tag
			var width = document.getElementById('width-' + id).innerText;
			var height = document.getElementById('height-' + id).innerText;
			var alt = document.getElementById('alt-' + id).innerText;
			var value = '<img src="'+root+'/'+url+'" width="'+width+'" height="'+height+'" alt="'+alt+'" class="cover">';
			var share = '<input class="form-control" type="text" value=\'' + value + '\' onclick="this.select();document.execCommand(\'copy\');" /> Click to Copy';
			body.innerHTML = share;
			modal.open();
		break;
		case 'figure':
			// get an image with caption tag
			var width = document.getElementById('width-' + id).innerText;
			var height = document.getElementById('height-' + id).innerText;
			var alt = document.getElementById('alt-' + id).innerText;
			var figure = document.getElementById('caption-' + id).innerText;
			var value = '<figure>';
			value += '<img src="'+root+'/'+url+'" width="'+width+'" height="'+height+'" alt="'+alt+'" class="cover">';
			value += '<figcaption>' + figure + '</figcaption>';
			value += '</figure>';
			var share = '<input class="form-control" type="text" value=\'' + value + '\' onclick="this.select();document.execCommand(\'copy\');" /> Click to Copy';
			body.innerHTML = share;
			modal.open();
		break;
		case 'picture':
			// get a picture tag
			var width = document.getElementById('width-' + id).innerText;
			var height = document.getElementById('height-' + id).innerText;
			var alt = document.getElementById('alt-' + id).innerText;
			var value = '<picture>';
			value += '<source srcset="'+root+'/'+url+'" media="(min-width: 800px)">';
			value += '<img src="' + url + '">';
			value += '</picture>';
			var share = '<input class="form-control" type="text" value=\'' + value + '\' onclick="this.select();document.execCommand(\'copy\');" /> Click to Copy';
			body.innerHTML = share;
			modal.open();
		break;
		case 'delete':
			alert('This feature is not implemented');
		break;
	}
	element.value ='';
	return true;
}
