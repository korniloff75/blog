<?php

require('../../../../../system/global.dat');
if ($status != 'admin'){ die ('Доступ только для администратора.'); }

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"  "http://www.w3.org/TR/html4/frameset.dtd">
<html>
	<head>
		<title>Файловый менеджер</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="browser.css" type="text/css" rel="stylesheet">
		<script type="text/javascript" src="js/fckxml.js"></script>
		<script type="text/javascript" src="js/cookie.js"></script>
		<script type="text/javascript" src="../../connectors/php/config.php?js_conf"></script>
		<script type="text/javascript">
		
// Automatically detect the correct document.domain (#1919).
(function()
{
	var d = document.domain ;

	while ( true )
	{
		// Test if we can access a parent property.
		try
		{
			var test = window.opener.document.domain ;
			break ;
		}
		catch( e )
		{}

		// Remove a domain part: www.mytest.example.com => mytest.example.com => example.com ...
		d = d.replace( /.*?(?:\.|$)/, '' ) ;

		if ( d.length == 0 )
			break ;		// It was not able to detect the domain.

		try
		{
			document.domain = d ;
		}
		catch (e)
		{
			break ;
		}
	}
})() ;

function GetUrlParam( paramName )
{
	var oRegex = new RegExp( '[\?&]' + paramName + '=([^&]+)', 'i' ) ;
	var oMatch = oRegex.exec( window.top.location.search ) ;

	if ( oMatch && oMatch.length > 1 )
		return decodeURIComponent( oMatch[1] ) ;
	else
		return '' ;
}

var oStore = {
	firstOpen: true,
	getFolderPath: function(resourceType, folderPath, dropFirstOpen ) {
		if( this.firstOpen ) {
			if ( getCookie(resourceType+'-fPath') ) {
				folderPath = getCookie(resourceType+'-fPath');
			}
			if ( dropFirstOpen !== undefined ) {
				this.firstOpen = false;
			}
		}
		return folderPath;
	},
	setFolderPath: function(resourceType, folderPath) {
		setCookie(resourceType+'-fPath', folderPath, '');
	}
}

var oConnector = {} ;
oConnector.CurrentFolder	= '/' ;

var sConnUrl = GetUrlParam( 'Connector' ) ;

// Gecko has some problems when using relative URLs (not starting with slash).
if ( sConnUrl.substr(0,1) != '/' && sConnUrl.indexOf( '://' ) < 0 )
	sConnUrl = window.location.href.replace( /browser.php.*$/, '' ) + sConnUrl ;

oConnector.ConnectorUrl = sConnUrl + ( sConnUrl.indexOf('?') != -1 ? '&' : '?' ) ;

var sServerPath = GetUrlParam( 'ServerPath' ) ;
if ( sServerPath.length > 0 )
	oConnector.ConnectorUrl += 'ServerPath=' + encodeURIComponent( sServerPath ) + '&' ;

oConnector.ResourceType		= GetUrlParam( 'Type' ) ;
oConnector.ShowAllTypes		= ( oConnector.ResourceType.length == 0 ) ;

if ( oConnector.ShowAllTypes )
	oConnector.ResourceType = 'File' ;

oConnector.SendCommand = function( command, params, callBackFunction )
{
	var sUrl = this.ConnectorUrl + 'Command=' + command ;
	sUrl += '&Type=' + this.ResourceType ;
	sUrl += '&CurrentFolder=' + encodeURIComponent( this.CurrentFolder ) ;

	if ( params ) sUrl += '&' + params ;

	// Add a random salt to avoid getting a cached version of the command execution
	sUrl += '&uuid=' + new Date().getTime() ;

	var oXML = new FCKXml() ;

	if ( callBackFunction )
		oXML.LoadUrl( sUrl, callBackFunction ) ;	// Asynchronous load.
	else
		return oXML.LoadUrl( sUrl ) ;

	return null ;
}

oConnector.CheckError = function( responseXml )
{
	var iErrorNumber = 0 ;
	var oErrorNode = responseXml.SelectSingleNode( 'Connector/Error' ) ;

	if ( oErrorNode )
	{
		iErrorNumber = parseInt( oErrorNode.attributes.getNamedItem('number').value, 10 ) ;

		switch ( iErrorNumber )
		{
			case 0 :
				break ;
			case 1 :	// Custom error. Message placed in the "text" attribute.
				alert( oErrorNode.attributes.getNamedItem('text').value ) ;
				break ;
			case 101 :
				alert( 'Папка уже существует' ) ;
				break ;
			case 102 :
				alert( 'Неверное имя папки' ) ;
				break ;
			case 103 :
				alert( 'У Вас нет прав на создание папки' ) ;
				break ;
			case 110 :
				alert( 'Неизвесная ошибка при создании папки' ) ;
				break ;
			default :
				alert( 'Ошибка в вашем запросе. Номер ошибки: ' + iErrorNumber ) ;
				break ;
		}
	}
	return iErrorNumber ;
}

var oIcons = new Object() ;

oIcons.AvailableIconsArray = [
	'ai','avi','bmp','cs','dll','doc','exe','fla','gif','htm','html','jpg','js',
	'mdb','mp3','pdf','png','ppt','rdp','swf','swt','txt','vsd','xls','xml','zip' ] ;

oIcons.AvailableIcons = new Object() ;

for ( var i = 0 ; i < oIcons.AvailableIconsArray.length ; i++ )
	oIcons.AvailableIcons[ oIcons.AvailableIconsArray[i] ] = true ;

oIcons.GetIcon = function( fileName )
{
	var sExtension = fileName.substr( fileName.lastIndexOf('.') + 1 ).toLowerCase() ;

	if ( this.AvailableIcons[ sExtension ] == true )
		return sExtension ;
	else
		return 'default.icon' ;
}

function OnUploadCompleted( errorNumber, fileUrl, fileName, customMsg )
{
	if (errorNumber == "1")
		window.frames['frmUpload'].OnUploadCompleted( errorNumber, customMsg ) ;
	else
		window.frames['frmUpload'].OnUploadCompleted( errorNumber, fileName ) ;
}
//window.onload = function() {
//		document.getElementById('frmUpload').src = "frmupload"+(Config.SWFUpload?'_swf':'')+".html";
//}
		</script>
	</head>
	<frameset cols="150,*" framespacing="0" frameborder="0">
		<frameset rows="55,*,55" framespacing="0">
			<frame src="frmresourcetype.html" scrolling="no" frameborder="0">
			<frame name="frmFolders" src="frmfolders.html" scrolling="auto" frameborder="0">
			<frame name="frmCreateFolder" src="frmcreatefolder.html" scrolling="no" frameborder="0">
		</frameset>
		<frameset rows="55,*,55" framespacing="0">
			<frame name="frmActualFolder" src="frmactualfolder.html" scrolling="no" frameborder="0">
			<frame name="frmResourcesList" src="frmresourceslist.html" scrolling="auto" frameborder="0">
			<frameset cols="*,0" framespacing="0" frameborder="0">
				<frame id="frmUpload" name="frmUpload" src="frmupload.html" scrolling="no" frameborder="0">
				<frame name="frmUploadWorker" src="javascript:void(0)" scrolling="no" frameborder="0">
			</frameset>
		</frameset>
	</frameset>
</html>
