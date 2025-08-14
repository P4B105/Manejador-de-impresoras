<?php
session_start();

// Verifica si el usuario NO ha iniciado sesión
if (!isset($_SESSION['username'])){ 
    // Redirige al usuario a la página de inicio de sesión
    header("Location: login.php");
    exit; // Termina el script para asegurar la redirección
}

// **IMPORTANTE**: Encabezados para evitar el caché en todas las páginas protegidas
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado

require 'db.php';
session_start();

$marca=$_POST['brand'];
$serial=$_POST['serialNumber'];
$modelo=$_POST['model'];
$tipo=$_POST['printerType'];
$fechaIn=$_POST['serviceDate'];
$paginas=$_POST['pageCounter'];

$tonerBlack=$_POST['tonerBlack'];
$tonerCyan=$_POST['tonerCyan'];
$tonerAmar=$_POST['tonerYellow'];
$tonerRojo=$_POST['tonerMagenta'];

$ubicacion=$_POST['location'];
$estado=$_POST['estado'];

if($tonerAmar>100 or $tonerBlack>100 or $tonerCyan>100 or $tonerRojo>100
	or $tonerAmar<0 or $tonerBlack<0 or $tonerCyan<0 or $tonerRojo<0){
	header("Location:formulario_impresora.php");
}

require 'db.php';

$tipoInt=0;

//formatear fecha al formato de MySQL (dia-mes-año -> año-mes-dia)
$diaTemporal=date_create($fechaIn);
$fechaIn=date_format($diaTemporal,"Y-m-s");
//no c pq pero aun asi no la agarra xd

switch($tipo){ //este switch lo hice pq estaba recolectando el dato de otra manera, pero ahora quedó feo
	case "1":
		$tipoInt=1;
	break;
	case "2":
		$tipoInt=2;
	break;
	case "3":
		$tipoInt=3;
	break;
	case "4":
		$tipoInt=4;
	break;
	case "5":
		$tipoInt=5;
	break;
	case "6":
		$tipoInt=6;
	break;
	case "7":
		$tipoInt=7;
	break;
	case "8":
		$tipoInt=8;
	break;
	case "9":
		$tipoInt=9;
	break;
	default:
		header("Location:formulario_impresora.php");
	break;
}

//obtener id de usuario desde la sesion
$quer1 = $conexion->query("SELECT id_usuario FROM `usuario` where usuario.alias like '".$_SESSION['username']."'");
$idUsr = $quer1->fetch_assoc()['id_usuario'] ?? null;

//diomio
$stmt=$conexion->prepare("INSERT INTO `impresora` (
		`id_impresora`,
		`id_usuario`,
		`numero_serie`,
		`marca`, 
		`modelo`,
		`id_tipo_impresora`,
		`fecha_puesta_en_servicio`,
		`contador_paginas_actual`,
		`toner_negro`,
		`toner_cian`,
		`toner_magenta`,
		`toner_amarillo`,
		`estado_actual`,
		`ubicacion`)

	VALUES ( NULL,?,?,?,?,?,?,?,?,?,?,?,?,?);");

$stmt->bind_param("sssssssssssss", //se cree serpiente
	$idUsr,
	$serial,
	$marca,
	$modelo,
	$tipoInt,
	$fechaIn,
	$paginas,
	$tonerBlack,
	$tonerCyan,
	$tonerRojo,
	$tonerAmar,
	$estado,
	$ubicacion);

if($stmt->execute()){
	header("Location: dashboard.php");
}else{
	echo "Error al insertar: " . $stmt->error;
}

$conexion->close();


?>