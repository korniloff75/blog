<?php

require('../../../../../system/global.dat');
if ($status != 'admin'){ die ('Доступ только для администратора.'); }

//require('../../../../../modules/ckeditor_plus/cfg.dat');

global $MConfig;
error_reporting(5);
//session_start();
mb_internal_encoding("UTF-8");

// SECURITY: You must explicitly enable this "connector". (Set it to "true").
// WARNING: don't just set "$MConfig['Enabled'] = true ;", you must be sure that only
//		authenticated users can access this file or use some kind of session checking.
//$MConfig['Enabled'] = $_SESSION['administartor']?true:false;

$MConfig['Enabled'] = true;

// Разрешить удаление файлов и папок true/false
$MConfig['Delete'] = true;

// Делать превьюшки и разрешать изменять размеры при закачке
// требует утилиту imagemagick или библиотеку gd2
// если их нет, то чтобы не выводить в интерфейсе лишние элементы установите в false
$MConfig['ThumbCreate'] = true;  // При закачке можно изменять размеры
$MConfig['ThumbList'] = false;    // Показывать превьюшки // НЕ МЕНЯТЬ - false
$MConfig['ThumbListSize'] = 100; // Размер превьюшки, вписывается в квадрат
$MConfig['ThumbMaxGenerate'] = 5; // Максимальное количество превьюшек, генерируемое за раз, если вдруг их нет

// Path to user files relative to the document root.
$MConfig['UserFilesPath'] = '/files/' ;

// Fill the following value it you prefer to specify the absolute path for the
// user files directory. Useful if you are using a virtual directory, symbolic
// link or alias. Examples: 'C:\\MySite\\userfiles\\' or '/root/mysite/userfiles/'.
// Attention: The above 'UserFilesPath' must point to the same directory.
// $MConfig['UserFilesAbsolutePath'] = $_SERVER["DOCUMENT_ROOT"].$MConfig['UserFilesPath'] ;
$MConfig['UserFilesAbsolutePath'] = DR.$MConfig['UserFilesPath'] ;  

// Due to security issues with Apache modules, it is recommended to leave the
// following setting enabled.
$MConfig['ForceSingleExtension'] = true ;

// Perform additional checks for image files.
// If set to true, validate image size (using getimagesize).
$MConfig['SecureImageUploads'] = true;

// What the user can do with this connector.
$MConfig['ConfigAllowedCommands'] = array('QuickUpload', 'FileUpload', 'GetFolders', 'GetFoldersAndFiles', 'CreateFolder') ;
if ($MConfig['Delete']) {
	$MConfig['ConfigAllowedCommands'][] = 'FileDelete';
	$MConfig['ConfigAllowedCommands'][] = 'FolderDelete';
}

// Allowed Resource Types.
$MConfig['ConfigAllowedTypes'] = array('File', 'Image', 'Flash', 'Media') ;

// For security, HTML is allowed in the first Kb of data for files having the
// following extensions only.
$MConfig['HtmlExtensions'] = array("html", "htm", "xml", "xsd", "txt", "js") ;

// After file is uploaded, sometimes it is required to change its permissions
// so that it was possible to access it at the later time.
// If possible, it is recommended to set more restrictive permissions, like 0755.
// Set to 0 to disable this feature.
// Note: not needed on Windows-based servers.
$MConfig['ChmodOnUpload'] = 0777 ;

// See comments above.
// Used when creating folders that does not exist.
$MConfig['ChmodOnFolderCreate'] = 0777 ;

/*
	Configuration settings for each Resource Type

	- AllowedExtensions: the possible extensions that can be allowed.
		If it is empty then any file type can be uploaded.
	- DeniedExtensions: The extensions that won't be allowed.
		If it is empty then no restrictions are done here.

	For a file to be uploaded it has to fulfill both the AllowedExtensions
	and DeniedExtensions (that's it: not being denied) conditions.

	- FileTypesPath: the virtual folder relative to the document root where
		these resources will be located.
		Attention: It must start and end with a slash: '/'

	- FileTypesAbsolutePath: the physical path to the above folder. It must be
		an absolute path.
		If it's an empty string then it will be autocalculated.
		Useful if you are using a virtual directory, symbolic link or alias.
		Examples: 'C:\\MySite\\userfiles\\' or '/root/mysite/userfiles/'.
		Attention: The above 'FileTypesPath' must point to the same directory.
		Attention: It must end with a slash: '/'

	 - QuickUploadPath: the virtual folder relative to the document root where
		these resources will be uploaded using the Upload tab in the resources
		dialogs.
		Attention: It must start and end with a slash: '/'

	 - QuickUploadAbsolutePath: the physical path to the above folder. It must be
		an absolute path.
		If it's an empty string then it will be autocalculated.
		Useful if you are using a virtual directory, symbolic link or alias.
		Examples: 'C:\\MySite\\userfiles\\' or '/root/mysite/userfiles/'.
		Attention: The above 'QuickUploadPath' must point to the same directory.
		Attention: It must end with a slash: '/'

	 	NOTE: by default, QuickUploadPath and QuickUploadAbsolutePath point to
	 	"userfiles" directory to maintain backwards compatibility with older versions of FCKeditor.
	 	This is fine, but you in some cases you will be not able to browse uploaded files using file browser.
	 	Example: if you click on "image button", select "Upload" tab and send image
	 	to the server, image will appear in FCKeditor correctly, but because it is placed
	 	directly in /userfiles/ directory, you'll be not able to see it in built-in file browser.
	 	The more expected behaviour would be to send images directly to "image" subfolder.
	 	To achieve that, simply change
			$MConfig['QuickUploadPath']['Image']			= $MConfig['UserFilesPath'] ;
			$MConfig['QuickUploadAbsolutePath']['Image']	= $MConfig['UserFilesAbsolutePath'] ;
		into:
			$MConfig['QuickUploadPath']['Image']			= $MConfig['FileTypesPath']['Image'] ;
			$MConfig['QuickUploadAbsolutePath']['Image'] 	= $MConfig['FileTypesAbsolutePath']['Image'] ;

*/

$MConfig['AllowedExtensions']['File']	= array('7z', 'aiff', 'asf', 'avi', 'bmp', 'csv', 'doc', 'docx', 'fla', 'flv', 'gif', 'gz', 'gzip', 'jpeg', 'jpg', 'mid', 'mov', 'mp3', 'mp4', 'mpc', 'mpeg', 'mpg', 'ods', 'odt', 'pdf', 'png', 'ppt', 'pptx', 'pxd', 'qt', 'ram', 'rar', 'rm', 'rmi', 'rmvb', 'rtf', 'sdc', 'sitd', 'swf', 'sxc', 'sxw', 'tar', 'tgz', 'tif', 'tiff', 'txt', 'vsd', 'wav', 'wma', 'wmv', 'xls', 'xlsx', 'xml', 'xps', 'zip','svg');
$MConfig['DeniedExtensions']['File']		= array() ;
$MConfig['FileTypesPath']['File']		= $MConfig['UserFilesPath'];
$MConfig['FileTypesAbsolutePath']['File']= ($MConfig['UserFilesAbsolutePath'] == '') ? '' : $MConfig['UserFilesAbsolutePath'];
$MConfig['QuickUploadPath']['File']		= $MConfig['UserFilesPath'];
$MConfig['QuickUploadAbsolutePath']['File']= $MConfig['UserFilesAbsolutePath'];

$MConfig['AllowedExtensions']['Image']	= array('bmp','gif','jpeg','jpg','png','tif','tiff','svg','pdf') ;
$MConfig['DeniedExtensions']['Image']	= array() ;
$MConfig['FileTypesPath']['Image']		= $MConfig['UserFilesPath'];
$MConfig['FileTypesAbsolutePath']['Image']= ($MConfig['UserFilesAbsolutePath'] == '') ? '' : $MConfig['UserFilesAbsolutePath'];
$MConfig['QuickUploadPath']['Image']		= $MConfig['UserFilesPath'];
$MConfig['QuickUploadAbsolutePath']['Image']= $MConfig['UserFilesAbsolutePath'];

$MConfig['AllowedExtensions']['ImageThumb']	= $MConfig['AllowedExtensions']['Image'] ;
$MConfig['DeniedExtensions']['ImageThumb']	= $MConfig['DeniedExtensions']['Image'] ;
$MConfig['FileTypesPath']['ImageThumb']		= $MConfig['UserFilesPath'].'_thumbs/' ;
$MConfig['FileTypesAbsolutePath']['ImageThumb']= ($MConfig['UserFilesAbsolutePath'] == '') ? '' : $MConfig['UserFilesAbsolutePath'].'_thumbs/';

$MConfig['AllowedExtensions']['Flash']	= array('swf','flv') ;
$MConfig['DeniedExtensions']['Flash']	= array() ;
$MConfig['FileTypesPath']['Flash']		= $MConfig['UserFilesPath'];
$MConfig['FileTypesAbsolutePath']['Flash']= ($MConfig['UserFilesAbsolutePath'] == '') ? '' : $MConfig['UserFilesAbsolutePath'];
$MConfig['QuickUploadPath']['Flash']		= $MConfig['UserFilesPath'];
$MConfig['QuickUploadAbsolutePath']['Flash']= $MConfig['UserFilesAbsolutePath'];

$MConfig['AllowedExtensions']['Media']	= array('aiff', 'asf', 'avi', 'bmp', 'fla', 'flv', 'gif', 'jpeg', 'jpg', 'mid', 'mov', 'mp3', 'mp4', 'mpc', 'mpeg', 'mpg', 'png', 'qt', 'ram', 'rm', 'rmi', 'rmvb', 'swf', 'tif', 'tiff', 'wav', 'wma', 'wmv') ;
$MConfig['DeniedExtensions']['Media']	= array() ;
$MConfig['FileTypesPath']['Media']		= $MConfig['UserFilesPath'];
$MConfig['FileTypesAbsolutePath']['Media']= ($MConfig['UserFilesAbsolutePath'] == '') ? '' : $MConfig['UserFilesAbsolutePath'];
$MConfig['QuickUploadPath']['Media']		= $MConfig['UserFilesPath'];
$MConfig['QuickUploadAbsolutePath']['Media']= $MConfig['UserFilesAbsolutePath'];

// Отдает конфиг для javascript (чтоб в нем не дублировать)
if (isset($_GET['js_conf'])) {
	$MConfig_js = array('Delete', 'ThumbList', 'ThumbListSize', 'ThumbCreate',
		'AllowedExtensions.File', 'AllowedExtensions.Image', 'AllowedExtensions.Flash', 'AllowedExtensions.Media');
	header('Content-type: text/javascript');
	$added = array();
	echo "Config = {};\n";
	foreach($MConfig_js as $key) {
		if (strpos($key, '.')!==false) {
			$key_arr = explode(".", $key);
			if (!in_array($key_arr[0], $added)) {
				$added[] = $key_arr[0];
				echo "Config['".$key_arr[0]."'] = [];\n";
			}
			$line = $MConfig[$key_arr[0]][$key_arr[1]];
			$key = $key_arr[0]."']['".$key_arr[1];
		} else {
			$line = $MConfig[$key];
		}
		echo "Config['".$key."'] = ";
		if (is_bool($line)) {
			echo $line?'true':'false';
		} elseif (is_array($line)) {
			echo '[';
			foreach($line as $k=>$v)
				echo ($k?',':'')."'".$v."'";
			echo ']';
		} else {
			echo "'".$line."'";
		}
		echo ";\n";
	}
	die();
}
?>
