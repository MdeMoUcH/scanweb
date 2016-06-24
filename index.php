<?php
/***************************************
 * Script para visualizar webs
 * sin imágenes ni estilos.
 * 2015 - MdeMocH
 * mdemouch@gmail.com
 **************************************/

$urlbase = 'http://localhost/scanweb/';
$url_redirect = $urlbase.'index.php?web=';

if(@$_GET['web'] != ''){
	$s_url = $_GET['web'];
	$s_url = str_replace('%3F','?',$s_url);
	if(strpos($s_url,'http://') === false && strpos($s_url,'https://') === false){
		$s_url = 'http://'.$s_url;
	}
}else{
	$s_url = 'http://www.lagranm.com/';
}
$a_url = explode('/',$s_url);
$s_url_base = $url_redirect.$a_url[0].'//'.$a_url[2];

//Cogemos la web:
$s_web = @file_get_contents($s_url);

//Quitamos las imágenes
$s_web = str_replace('src=','boo=',$s_web);
$s_web = str_replace('srcset=','boo=',$s_web);
$s_web = str_replace('<img','<boo',$s_web);
$s_web = str_replace('<svg','<boo',$s_web);

//Quitamos los estilos:
$s_web = str_replace('.css','.ssc',$s_web);
$s_web = str_replace('<style','<!--<stylah',$s_web);
$s_web = str_replace('</style>','</stylah>-->',$s_web);
$s_web = str_replace('style=','stylah=',$s_web);
$s_web = str_replace('favicon','iconfav',$s_web);

//Quitamos los iframes y objetos:
$s_web = str_replace('iframe','noframe',$s_web);
$s_web = str_replace('object','nobject',$s_web);
$s_web = str_replace('embed','nobject',$s_web);

//Redirigimos los enlaces:
$a_exp = array('|href\="http|','|href\="www\.|','|href\="/|','|href\="#|');
$a_sus = array('href="'.$url_redirect.'http','href="'.$url_redirect.'www.','href="'.$s_url_base.'/','href="'.$url_redirect.$s_url.'#');
$s_web = preg_replace($a_exp,$a_sus,$s_web);

//Añadimos nuestra propia hoja de estilos con un color de fondo entre unos cuentos:
$a_colores = array('LightBlue', 'LightGrey', 'Tan', 'Moccasin');
$s_web = str_replace(array('</head>','</HEAD>'),str_replace('{{color}}',$a_colores[rand(0,(count($a_colores)-1))],file_get_contents('nostyle.html')),$s_web);

//Y nuestro propio menú:
$body_pos = strpos($s_web,'<body');
if($body_pos === false){
	$body_pos = strpos($s_web,'<BODY');
}
if($body_pos !== false){
	$body_pos = strpos($s_web,'>',$body_pos)+1;
	$s_web = substr($s_web,0,$body_pos).file_get_contents('menu.html').substr($s_web,$body_pos,strlen($s_web));
}


//Y se lo enseñamos al mundo:
die($s_web);
