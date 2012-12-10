/**
 * Ajax Object
 * 
 * This is a ajax object file. You can use ajax easyly with this file.
 * 
 * Usage
 * ***********************************************************************
 * <script src="./js/ajax.js"><\/script>
 * 
 * <!-- body codes -->
 * 
 * <\script>
 * var ajax = Ajax();
 * ajax.get('ajax.php?k=v&k2=v2', function(data) {
 *     alert(data);
 * }) 
 * 
 * ajax.post('ajax.php', 'k=v&k2=v2', function(data) {
 *     alert(data);
 * })
 * 
 * ajax.post('ajax.php', {k:v, method:json}, function(data) {
 *     alert(data);
 * })
 * <\/script>
 * 
 * ***********************************************************************
 * @author IT不倒翁 <itbudaoweng@gmail.com>
 * @copyright (C) 2012 Just Use It!
 * @link http://yungbo.com IT不倒翁
 * @param  {string} returnType HTML|XML
 * @return {unknown}            data back from server
 */
function Ajax(returnType) {
	var ajax = new Object();
	ajax.returnType = returnType ? returnType.toUpperCase() : 'HTML'; //return type, HTML|XML
	ajax.targetUrl = '';		//ajax request url
	ajax.sendData = '';			//query string or json object that send if method is post
	ajax.callbackData = '';		//data that callback function return
	ajax.xmlHttpRequest = null;		//xmlHttpRequest object

	ajax.createXMLHttpRequest = null;	//Create a XmlHttpRequestRequest object or ActiveX object
	ajax.process = null;				//Handle the data back from server

	/**
	 * [createXMLHttpRequest description]
	 * @return {[type]} [description]
	 */
	ajax.createXMLHttpRequest = function () {
		var xmlHttpRequest = null;
		if (window.XMLHttpRequest) {
			xmlHttpRequest = new XMLHttpRequest();
		} else {
			xmlHttpRequest = new ActiveXObject();
		}
		return xmlHttpRequest;
	}

	/**
	 * [process description]
	 * @return {[type]} [description]
	 */
	ajax.process = function () {
		if (ajax.xmlHttpRequest.readyState == 4 && ajax.xmlHttpRequest.status == 200) {
			if (ajax.returnType == 'XML') {
				ajax.callbackData(ajax.xmlHttpRequest.responseXML);
			} else {
				ajax.callbackData(ajax.xmlHttpRequest.responseText);
			}
		}
	}

	ajax.xmlHttpRequest = ajax.createXMLHttpRequest();

	/**
	 * [get description]
	 * @param  {[type]} targetUrl    [description]
	 * @param  {[type]} callbackData [description]
	 * @return {[type]}              [description]
	 */
	ajax.get = function (targetUrl, callbackData) {
		ajax.targetUrl = targetUrl;

		if (callbackData != null) {
			ajax.xmlHttpRequest.onreadystatechange = ajax.process;
			ajax.callbackData = callbackData;
		}
		ajax.xmlHttpRequest.open('get', ajax.targetUrl, true);
		ajax.xmlHttpRequest.send();
	}

	/**
	 * [post description]
	 * @param  {[type]} targetUrl    [description]
	 * @param  {[type]} sendData     [description]
	 * @param  {[type]} callbackData [description]
	 * @return {[type]}              [description]
	 */
	ajax.post = function (targetUrl, sendData, callbackData) {
		ajax.targetUrl = targetUrl;
		ajax.sendData = sendData;

		if (callbackData != null) {
			ajax.xmlHttpRequest.onreadystatechange = ajax.process;
			ajax.callbackData = callbackData;
		}

		if (typeof(ajax.sendData) == 'object') {
			var postData = '';
			for (var key in ajax.sendData) {
				postData += key + '=' + ajax.sendData[key] + '&';
			}
			ajax.sendData = postData.substr(0, postData.length - 1);
		}

		ajax.xmlHttpRequest.open('post', ajax.targetUrl, true);
		ajax.xmlHttpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		ajax.xmlHttpRequest.send(ajax.sendData);
	}
	return ajax;
}

/**
 * Sample HTML 
 */
// <html>
// <head>
// 	<title></title>
// 	<script type="text/javascript" src="./js/ajax.js"></script>
// </head>
// <body>
	
// <input type="button" value="Load Ajax" onclick="ajaxSample();">
// <input type="button" value="Clear" onclick="clearSample();">
// <div id="text"></div>

// <script type="text/javascript">
// var textObj = document.getElementById('text');

// function ajaxSample() {
// 	var ajax = Ajax();
// 	ajax.post('ajax.php', {name:'China'}, function(data) {
// 		textObj.innerHTML = data;
// 	});
// }

// function clearSample() {
// 	textObj.innerHTML = '';
// }

// </script>
// </body>
// </html>

//sample php
// <?php 
// header("Content-Type:text/html; charset='utf-8'");

// echo '<pre>';
// echo __FILE__, '<br />';
// echo date("Y-m-d H:i:s"), '<br />';
// print_r(getallheaders());
// echo '<br />';

// if ($_REQUEST) {
// 	echo 'Request Method is: ',
// 		$_SERVER['REQUEST_METHOD'],
// 		'<br />';

// 	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// 		print_r($_POST);
// 	} else {
// 		print_r($_GET);
// 	}
// }

// echo '</pre>';