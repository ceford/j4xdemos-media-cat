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
	var paths = Joomla.getOptions(["system.paths"], 'No good');
	var root = paths.root;
	var rootFull = paths.rootFull;		
	var modal = document.getElementById('collapseModal'); 
	var title = document.getElementsByClassName('modal-title')[0];
	var body = document.getElementsByClassName('modal-body')[0];
	if (element === 'zoom') {
		var selected = element
	} else {
		var selected = element.value;
		var id = element.id.split('_')[1];
	}
	switch (selected) {
		case 'zoom':
			var tag = '<img src="'+root+'/'+url+'" class="cover">';
			title.innerText = 'Image Zoom';
			body.classList.add("text-center");
			body.innerHTML = tag;
			modal.open();
		break;
		case 'edit':
			location.replace('index.php?option=com_mediacat&view=image&layout=edit&id=' + id);
		break;
		case 'download':
			location.assign(root + '/' + url);
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
		case 'trash':
			alert('This feature is not implemented');
		break;
	}
	element.value ='';
	return true;
}

function updateFilename(element) {
	var id = document.getElementById('jform_id').value;
	var uploadfile = document.getElementById('jform_uploadfile').value;
	var filename = document.getElementById('jform_file_name');
	// if id is empty we need a file
	if (!id) {
		if (!uploadfile) {
			alert('Please select a file to upload');
			return;
		} else if (!filename.value) {
			var parts = uploadfile.split('.');
			var ext = parts.pop();
			filename.value = ext;
		}
	}
	// only allow alphanumeric characters in filename
	var dirty = element.value;
	var clean = dirty.replace(/[^0-9a-zA-Z\ ]/g, '');
	element.value = clean;
	// if there is an id - don't change the filename
	if (id) {
		return;
	}
	// keep the extension
	var parts = filename.value.split('.');
	var ext = parts.pop();
	var sink = clean.replace(/ /g, '-');
	filename.value = sink.toLowerCase() + '.' + ext;
}

function mediacatSelectFolder(element) {
	var parts = element.id.split('-');
	var id = parts[1];
	var folder = document.getElementById('folder-' + id);
	var value = folder.innerText;
	var activepath = document.getElementById('jform_activepath');
	activepath.value = value;
	var mediaHasher = document.getElementById('mediacatHasher');
	var mediaIndexer = document.getElementById('mediacatIndexer');
	var mediaCreateFolder = document.getElementById('mediacatCreateFolder');
	var mediaTrash = document.getElementById('mediacatTrash');
	mediaHasher.removeAttribute('disabled');
	mediaIndexer.removeAttribute('disabled');
	mediaCreateFolder.removeAttribute('disabled');
	mediaTrash.removeAttribute('disabled');
}

function mediacatUnselectFolder(element) {
	var mediaHasher = document.getElementById('mediacatHasher');
	var mediaIndexer = document.getElementById('mediacatIndexer');
	var mediaCreateFolder = document.getElementById('mediacatCreateFolder');
	var mediaTrash = document.getElementById('mediacatTrash');
	mediaHasher.setAttribute('disabled', true);
	mediaIndexer.setAttribute('disabled', true);
	mediaCreateFolder.setAttribute('disabled', true);
	mediaTrash.setAttribute('disabled', true);
}

async function doHash(folder) {
	var form = document.getElementById('adminForm');
	var task = document.getElementById('task');
	var activepath = document.getElementById('jform_activepath');
	
	task.value = 'folders.hasher';
	activepath.value = folder;
	
	var results = document.getElementById('results');
	let response = await fetch(form.action, {
			method: form.method,
			body: new FormData(form)
	});
	if (!response.ok) {
		throw new Error(`HTTP error! status: ${response.status}`);
	} else {
		let result = await response.json();
		// show the results?
		var br = document.createElement("br");
		var text = document.createTextNode(result);
		results.appendChild(br);
		results.appendChild(text);
	}
	task.value = '';
}

async function doIndex(folder) {
	var form = document.getElementById('adminForm');
	var task = document.getElementById('task');
	var activepath = document.getElementById('jform_activepath');
	
	task.value = 'folders.indexer';
	activepath.value = folder;
	
	var results = document.getElementById('results');
	let response = await fetch(form.action, {
			method: form.method,
			body: new FormData(form)
	});
	if (!response.ok) {
		throw new Error(`HTTP error! status: ${response.status}`);
	} else {
		let result = await response.json();
		// show the results?
		var br = document.createElement("br");
		var text = document.createTextNode(result);
		results.appendChild(br);
		results.appendChild(text);
	}
	task.value = '';
}

async function myFetch() {
	var form = document.getElementById('adminForm');
	var task = document.getElementById('task');
	task.value = 'folders.getTree';
	var results = document.getElementById('results');
	let response = await fetch(form.action, {
			method: form.method,
			body: new FormData(form)
	});
	if (!response.ok) {
		throw new Error(`HTTP error! status: ${response.status}`);
	} else {
		let folders = await response.json();
		// show the tree?
		results.innerHTML = folders.join("<br />\n");
		//document.form.appendChild(what);
		folders.forEach (element => doIndex(element));
	}
}

function mediacatHasher() {
	var activepath = document.getElementById('jform_activepath');
	if (confirm('Hash items in Folder in ' + activepath.value)) {
		doHash(activepath.value);
	}
}

function mediacatIndexer() {
	var activepath = document.getElementById('jform_activepath');
	if (confirm('Index Folder in ' + activepath.value)) {
		doIndex(activepath.value);
	} else {
		alert('Test = ' + activepath.value);
	}
}

function mediacatCreateFolder() {
	var activepath = document.getElementById('jform_activepath');
	var task = document.getElementById('task');
	var form = document.getElementById('adminForm');
	var newfoldername = prompt('Create Folder in ' + activepath.value);
	if (newfoldername != null) {
		// make sure the foldername is acceptable
		newfoldername = newfoldername.trim();
		if (!newfoldername) {
			alert('Folder name is empty!')
			return;
		}
		// without full stops
		if (newfoldername.indexOf('.') >= 0) {
			alert('Folder name may not contain a full stop!');
			return;
		}
		// and replace spaces and underlines with -
		let regexp = /_| /gi;
		newfoldername = newfoldername.replace(regexp, '-');
		// now ask if this is ok
		ok = confirm('Is this OK: ' + newfoldername);
		if (ok) {
			task.value = 'folders.newfolder';
			document.getElementById('newfoldername').value = newfoldername;
			document.getElementById('adminForm')
			form.submit();
		}
	}
}

function mediacatTrash() {
	var activepath = document.getElementById('jform_activepath');
	if (confirm('Trash Folder in ' + activepath.value)) {
		var task = document.getElementById('task');
		var form = document.getElementById('adminForm');
		task.value = 'folder.trash';
		//form.submit();
		alert('This feature is not implemented');
	} else {
		
	}
	task.value = '';
}