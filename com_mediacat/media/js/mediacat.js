var test4jformalt = document.getElementById("jform_alt");
if (test4jformalt) {
	document.getElementById("jform_alt").onkeyup = function() {
		updateFilename();
	}
	document.getElementById("jform_alt").onchange = function() {
		updateFilename();
	}
}

function setFolder(newPath) {
	var folderPath = document.getElementById('filter_activepath');
	folderPath.value = newPath;
	var form = document.getElementById('adminForm');
	form.submit();
}

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
			title.innerText = Joomla.Text._('COM_MEDIACAT_JS_IMAGE_ZOOM');
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
			var share = '<input class="form-control" type="text" value="' + rootFull + url + '" onclick="this.select();document.execCommand(\'copy\');" />' + Joomla.Text._('COM_MEDIACAT_JS_CLICK_TO_COPY');
			title.innerText = Joomla.Text._('COM_MEDIACAT_JS_SHARE_LINK');
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
			title.innerText = Joomla.Text._('COM_MEDIACAT_JS_IMAGE_TAG');
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
			title.innerText = Joomla.Text._('COM_MEDIACAT_JS_FIGURE_TAG');
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
			title.innerText = Joomla.Text._('COM_MEDIACAT_JS_PICTURE_TAG');
			body.innerHTML = share;
			modal.open();
		break;
		case 'trashimage':
			if (confirm(Joomla.Text._('COM_MEDIACAT_JS_TRASH_ITEM') + id)) {
				var action_id = document.getElementById('jform_action_id');
				var task = document.getElementById('task');
				var form = document.getElementById('adminForm');
				action_id.value = id;
				task.value = 'image.trash';
				form.submit();
			}
		break;
		case 'trashfile':
			if (confirm(Joomla.Text._('COM_MEDIACAT_JS_TRASH_ITEM') + id)) {
				var action_id = document.getElementById('jform_action_id');
				var task = document.getElementById('task');
				var form = document.getElementById('adminForm');
				action_id.value = id;
				task.value = 'file.trash';
				form.submit();
			}
		break;
		case 'restoreimage':
			if (confirm(Joomla.Text._('COM_MEDIACAT_JS_RESTORE_ITEM'))) {
				var action_id = document.getElementById('jform_action_id');
				var task = document.getElementById('task');
				var form = document.getElementById('adminForm');
				action_id.value = id;
				task.value = 'image.restore';
				form.submit();
			}
		break;
		case 'restorefile':
			if (confirm(Joomla.Text._('COM_MEDIACAT_JS_RESTORE_ITEM'))) {
				var action_id = document.getElementById('jform_action_id');
				var task = document.getElementById('task');
				var form = document.getElementById('adminForm');
				action_id.value = id;
				task.value = 'file.restore';
				form.submit();
			}
		break;
		case 'deleteimage':
			if (confirm(Joomla.Text._('COM_MEDIACAT_JS_DELETE_ITEM'))) {
				var action_id = document.getElementById('jform_action_id');
				var task = document.getElementById('task');
				var form = document.getElementById('adminForm');
				action_id.value = id;
				task.value = 'image.delete';
				form.submit();
			}
		break;
		case 'deletefile':
			if (confirm(Joomla.Text._('COM_MEDIACAT_JS_DELETE_ITEM'))) {
				var action_id = document.getElementById('jform_action_id');
				var task = document.getElementById('task');
				var form = document.getElementById('adminForm');
				action_id.value = id;
				task.value = 'file.delete';
				form.submit();
			}
		break;
	}
	element.value ='';
	return true;
}

function updateFilename() {
	var id = document.getElementById('jform_id').value;
	var uploadfile = document.getElementById('jform_uploadfile').value;
	var filename = document.getElementById('jform_file_name');
	// if id is empty we need a file
	if (!id) {
		if (!uploadfile) {
			alert(Joomla.Text._('COM_MEDIACAT_JS_PLEASE_SELECT_FILE'));
			return;
		} else if (!filename.value) {
			var parts = uploadfile.split('.');
			var ext = parts.pop();
			filename.value = ext;
		}
	}
	// if there is an id - don't change the filename
	if (id) {
		return;
	}
	// only allow alphanumeric characters in filename
	var dirty = document.getElementById('jform_alt').value;
	var clean = dirty.replace(/[^0-9a-zA-Z\ ]/g, '-');
	jform_alt.value = clean;
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
}

async function doHash(folder) {
	var form = document.getElementById('adminForm');
	var task = document.getElementById('task');
	var activepath = document.getElementById('jform_activepath');
	var br = document.createElement("br");
	var hr = document.createElement("hr");
	var results = document.getElementById('results');

	task.value = 'folders.hasher';
	activepath.value = folder;
	
	let response = await fetch(form.action, {
			method: form.method,
			body: new FormData(form)
	});
	if (!response.ok) {
		throw new Error (Joomla.Text._('COM_MEDIACAT_JS_ERROR_STATUS') + `${response.status}`);
	} else {
		let result = await response.json();
		results.appendChild(hr);
		var path = result.shift();
		var updates = result.join(',');
		results.appendChild(document.createTextNode(path));
		results.appendChild(br);
		results.appendChild(document.createTextNode(updates));
		results.scrollIntoView(false);
	}
	task.value = '';
}

async function doIndex(folder) {
	var form = document.getElementById('adminForm');
	var task = document.getElementById('task');
	var activepath = document.getElementById('jform_activepath');
	var results = document.getElementById('results');
	var hr = document.createElement("hr");
	var br = document.createElement("br");

	task.value = 'folders.indexer';
	activepath.value = folder;
	
	let response = await fetch(form.action, {
			method: form.method,
			body: new FormData(form)
	});
	if (!response.ok) {
		throw new Error (Joomla.Text._('COM_MEDIACAT_JS_ERROR_STATUS') + `${response.status}`);
	} else {
		let result = await response.json();
		// show the results?
		results.appendChild(hr);
		var path = result.shift();
		var updates = result.join(',');
		results.appendChild(document.createTextNode(path));
		results.appendChild(br);
		results.appendChild(document.createTextNode(updates));
		results.scrollIntoView(false);
	}
	task.value = '';
}

async function indexAll() {
	var form = document.getElementById('adminForm');
	var task = document.getElementById('task');
	var activepath = document.getElementById('jform_activepath');
	var results = document.getElementById('results');
	var hr = document.createElement("hr");

	task.value = 'folders.getTree';

	let response = await fetch(form.action, {
			method: form.method,
			body: new FormData(form)
	});
	if (!response.ok) {
		throw new Error(Joomla.Text._('COM_MEDIACAT_JS_ERROR_STATUS') + `${response.status}`);
	} else {
		let folders = await response.json();
		var nFolders = folders.length;
		var text = Joomla.Text._('COM_MEDIACAT_JS_NFOLDERS_TO_PROCESS') + nFolders + '\n';
		var message = document.createTextNode(text);
		results.appendChild(hr);
		results.appendChild(message);
		folders.forEach (element => doIndex(element));
	}
}

function mediacatDeleteIfEmpty() {
	var activepath = document.getElementById('jform_activepath');
	if (confirm(Joomla.Text._('COM_MEDIACAT_JS_DELETE_IF_EMPTY') + activepath.value)) {
		var task = document.getElementById('task');
		var form = document.getElementById('adminForm');
		task.value = 'folders.deleteifempty';
		form.submit();
	}
	task.value = '';
}

function mediacatHashOne() {
	var activepath = document.getElementById('jform_activepath');
	if (confirm(Joomla.Text._('COM_MEDIACAT_JS_HASH_FOLDER') + activepath.value)) {
		doHash(activepath.value);
	}
}

function mediacatHashAll() {
	if (!confirm(Joomla.Text._('COM_MEDIACAT_JS_HASH_ALL'))) {
		return;
	}
	var activepath = document.getElementById('jform_activepath');
	var current = activepath.value;
	document.getElementById('rb-0').checked = true;
	activepath.value = current.split('/', 2).join('/');
	doHash(activepath.value);
}

function mediacatIndexOne() {
	var activepath = document.getElementById('jform_activepath');
	if (confirm(Joomla.Text._('COM_MEDIACAT_JS_INDEX_FOLDER') + activepath.value)) {
		doIndex(activepath.value);
	}
}

function mediacatIndexAll() {
	if (!confirm(Joomla.Text._('COM_MEDIACAT_JS_INDEX_ALL'))) {
		return;
	}
	var activepath = document.getElementById('jform_activepath');
	var current = activepath.value;
	document.getElementById('rb-0').checked = true;
	activepath.value = current.split('/', 2).join('/');
	indexAll();
}

function mediacatCreateFolder(view) {
	var activepath = document.getElementById('jform_activepath');
	var task = document.getElementById('task');
	var form = document.getElementById('adminForm');
	var newfoldername = prompt('Create Folder in ' + activepath.value);
	if (newfoldername != null) {
		// make sure the foldername is acceptable
		newfoldername = newfoldername.trim();
		if (!newfoldername) {
			alert(Joomla.Text._('COM_MEDIACAT_JS_FOLDER_NAME_EMPTY'))
			return;
		}
		// without full stops
		if (newfoldername.indexOf('.') >= 0) {
			alert(Joomla.Text._('COM_MEDIACAT_JS_FOLDER_NAME_NO_STOP'));
			return;
		}
		// and replace spaces and underlines with -
		let regexp = /_| /gi;
		newfoldername = newfoldername.replace(regexp, '-');
		// now ask if this is ok
		ok = confirm(Joomla.Text._('COM_MEDIACAT_JS_FOLDER_NAME_IS_OK') + newfoldername);
		if (ok) {
			task.value = view + '.newfolder';
			document.getElementById('jform_newfoldername').value = newfoldername;
			document.getElementById('adminForm')
			form.submit();
		}
	}
	task.value = '';
}

function mediacatTrash() {
	var activepath = document.getElementById('jform_activepath');
	if (confirm(Joomla.Text._('COM_MEDIACAT_JS_FOLDER_TRASH_ITEMS') + activepath.value)) {
		var task = document.getElementById('task');
		var form = document.getElementById('adminForm');
		task.value = 'folder.trash';
		//form.submit();
		alert('This feature is not implemented');
	} else {
		
	}
	task.value = '';
}