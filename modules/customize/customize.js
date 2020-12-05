function customizeSave(ticket, uri) {
	console.log('customize: save');
	console.log('customize: ' + ticket);
	console.log('customize: ' + uri);
	//cmstatus.innerHTML = 'Сохранение';
	var cmFormData = new FormData();
	cmFormData.append('customize', 'save');
	cmFormData.append('ticket', ticket);
	if (document.getElementsByClassName) {
		var elem = document.getElementsByClassName('editable');
		for (var i = 0; i < elem.length; i++) {
			cmFormData.append(elem[i].id, elem[i].innerHTML);
		}
	}
	console.log('customize: request("' + uri + '")');
	JLoader.request(uri, {
		data: cmFormData,// Данные
		success: function (response) {
			console.log('customize: ' + response);
			customizeStop();
		},
		error: function (response) {
			console.log('customize: error');
		}
	})
			
	
	return false;
}

function customizeStop() {
	location.reload(true);
}

function customizeStart(ticket, uri) {
	if (document.getElementsByClassName) {
		var elem = document.getElementsByClassName('editable');
		for (var i = 0; i < elem.length; i++) {
			elem[i].contentEditable = 'true';
		}
	}
	CKEDITOR.inlineAll();
	CKEDITOR.config.filebrowserImageBrowseUrl = '/?act=customizeUpload';
	// CKEDITOR.config.image2_captionedClass = 'image-captioned';
	// //

	var customizeHtml = 
		'<div id="grup2">' +
		'<a href="javascript:void(0);" title="Сохранить правки" onclick="customizeSave(\'' + ticket + '\', \'' + uri + '\');"><img src="/modules/customize/save.svg"></a>' +
		'<a href="javascript:void(0);" title="Выключить редактирование" onclick="customizeStop()"><img src="/modules/customize/close.svg"></a>' +
		'</div>';
	customize.innerHTML = customizeHtml;
	return false;
}

function customizeInit(ticket, uri) {
	var customize = document.createElement('div');
	document.body.appendChild(customize);
	customize.id = 'customize';
	//CKEDITOR.disableAutoInline = true;
	var customizeHtml = 
		'<div id="grup1">' +
		'<a href="javascript:void(0);" title="Включить редактирование" onclick="customizeStart(\'' + ticket +'\', \''+ uri +'\')"><img src="/modules/customize/edit.svg"></a>' +
		'</div>';
	customize.innerHTML = customizeHtml;

}
