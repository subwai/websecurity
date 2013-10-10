<?php
include "functions.php";
include "general.php";

$error = false;
$order = false;
if (isset($_GET["id"])) {
  try {
    $order = $functions->fetchOrder($_GET["id"]);
  } catch(Exception $e) {
    $error = $e->getMessage();
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Websecurity shop - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="main.css" rel="stylesheet">
</head>

<body screen_capture_injected="true">
  <div id="site-wrapper">
    <!-- NAVBAR
    ================================================== -->

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <nav class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="nav-link brand" href="index">Websecurity shop</a>
            <?php if ($_SESSION["auth"]) { ?>
            <div class="nav-block">Account: <?= $_SESSION["username"] ?></div>
            <a class="nav-link" href="logout">Logout</a>
            <?php } else { ?>
            <a class="nav-link" href="login">Login</a>
            <?php } ?>
        </nav><!-- /.container -->
      </div><!-- /.navbar-inner -->
    </div><!-- /.navbar -->

    <!--MAIN CONTAINER
    ================================================== -->

    <div class="container main">

      <?php if ($error) { ?>

      <div class="alert alert-error">
        <strong>Oh snap!</strong> <?= $error; ?>
      </div>

      <?php } elseif($order) { ?>

      <h1>Receipt</h1>
      <h4>Order Id: #<?= $order->id ?></h4>
      <div class="row-fluid">
        <div class="row">
          <table class="table">
            <thead>
              <th>Item</th>
              <th>Price</th>
            </thead>
            <?php foreach ($functions->fetchOrderItems($order) as $key => $item): ?>
            <tr>
              <td><?= $item->name ?></td>
              <td class="price"><?= $item->price ?></td>
            </tr>
            <?php endforeach; ?>
          </table>
          <div class="pull-right">Total price: <strong><?= $order->totalPrice ?>$</strong></div>
        </div>
        <hr>
        <div class="alert alert-info">
          Status: <strong><?= $order->status ?></strong>
        </div>
      </div>

      <?php } else { ?>

        <div class="alert alert-warning">
          <strong>Sorry!</strong> You need a receipt id to see this page.
        </div>

      <?php } ?>

      <!-- /END THE FEATURETTES -->

    </div><!-- /.container -->

    <!-- FOOTER -->
    <footer>
      <div class="container">
        <p class="pull-right"><a href="#">Tillbaka till toppen</a></p>
        <p>© 2013 Websäkerhet, LTH · <a href="about">Contact</a></p>
      </div>
    </footer>

  </div>

  <!-- Le javascript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script src="jquery.cookie.js"></script>
  <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>