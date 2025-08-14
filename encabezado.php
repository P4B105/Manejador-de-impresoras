<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <header class="encabezado-header">
            <?php
                if(isset($_SESSION['username'])){
                    echo '
                        <div class="encabezado-div-logo">
                            <a href="dashboard.php"><img src="Imagenes/logo2.png" alt="Logo de empresa" class="encabezado-img-logo"></a>
                        </div>
                        <nav>
                            <ul class="encabezado-ul">
                                <li class="encabezado-li" style= "padding:0"><a class="encabezado-a"><button class="encabezado-button">'.$_SESSION['username'].'</button></a></li>
                            </ul>
                        </nav>
                        <a href="logout.php"><button class="encabezado-button">Cerrar Sesion</button></a>
                        <a href="formulario_impresora.php"><button class="encabezado-button">Agregar Impresora</button></a>
                    ';
                }elseif(!isset($_SESSION['ussername'])){
                    echo '
                        <div class="encabezado-div-logo">
                            <a href="#"><img src="Imagenes/logo2.png" alt="Logo de empresa" class="encabezado-img-logo"></a>
                        </div>
                        <nav>
                            <ul class="encabezado-ul">
                                <li class="encabezado-li"><a href="registro.php" class="encabezado-a">Registrarse</a></li>
                                <li class="encabezado-li"><a href="index.php" class="encabezado-a">Iniciar Sesion</a></li>
                            </ul>
                        </nav>
                    ';
                }
            ?>
    </header>


</body>
</html>


