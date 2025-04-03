<?php
// Inicia la sesión solo si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "controller/utilities.php";

// Inicializa la clave 'cart' si no está definida
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>:: Crowd Interactive ::</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ChatWorld">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
        body {
            padding-top: 60px; /* 60px para alinear el contenido debajo de la barra superior */
        }
    </style>
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
</head>

<body>
<?php require "navbar.php"; ?>

<div class="container">
    <div class="span3">
        <?php notice(); ?>
        <!-- <img src="assets/cart.png" width="128" height="128"> -->
        <?php
        $cart = new cart();
        $cartSize = $cart->size($_SESSION['cart']);
        ?>
        <h3><a href="estatus.php">Mi canasta(<?= $cartSize; ?>)</a></h3>
        <p></p>
        <?php $cart->cart_show($_SESSION['cart']); ?>
    </div>
    <div class="span8">
        <table class="table table-hover">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            $ms = new mysql();

            $SQL = "SELECT A.producto_id, A.nombre, A.precio, A.descripcion, A.url, B.nombre AS tipo ";
            $SQL .= "FROM productos A ";
            $SQL .= "INNER JOIN productos_tipos_cantidades B ";
            $SQL .= "ON A.tipo_cantidades_id = B.tipo_cantidades_id";

            $row = $ms->query($SQL);

            foreach ($row as $key => $value) {
                $img = empty($value['url']) ? "assets/no_product_img.jpg" : $value['url'];
        ?>  
            <tr>
                <td style="width:120px">
                    <img src="<?php echo $img ?>" width="128px" height="128px">
                </td>
                <td>
                    <div class="row-fluid">
                        <div class="span10"> 
                            <h4><?php echo $value['nombre'] ?></h4>
                            <p><?php echo $value['descripcion'] ?></p>
                        </div>
                        <div class="span2">
                            <h4>$<?php echo $value['precio'] ?> MXN</h4>
                            <a href="controller/additemtocart.php?producto_id=<?php echo $value['producto_id']; ?>" class="btn btn-info">Agregar</a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</div>

<script src="bootstrap/js/jquery-1.9.1.js" type="text/javascript"></script>
<script src="bootstrap/js/bootstrap.js" type="text/javascript"></script>
<script src="bootstrap/js/boot.js" type="text/javascript"></script>

</body>
</html>
