<?php
include "functions.php";
include "general.php";
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
      <h1>Items</h1>
      <div class="row-fluid">
        <div class="span9">
          <table class="table table-striped table-bordered" id="items">
            <thead>
              <th class="title">NAME</th>
              <th class="title">DESCRIPTION</th>
              <th></th>
              <th class="price title">PRICE</th>
              <th class="title">ITEM-LIST <i class="icon-th-list"></i></th>
            </thead>
            <tbody>
              <?php foreach ($functions->fetchItems() as $item): ?>
              <tr>
                <td><?= $item->name ?></td>
                <td><?= $item->description ?></td>
                <td class="image"><img src="<?= "images/items/".$item->img ?>" class="img-polaroid" /></td>
                <td class="price"><?= $item->price ?></td>
                <td><a href="#" class="btn btn-primary" value="<?= $item->id ?>">Buy</a></td>
              <?php endforeach; ?>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="span3">
          <div class="well">
            <div class="title">
              <i class="icon-shopping-cart"></i>
              <span>SHOPPING-CART</span>
            </div>
            <div class="cart-container">
              <div class="cart-background">
                <h5>Your cart is empty</h5>
              </div>
              <table class="table table-striped" id="cart">
                <?php
                $total = 0;
                foreach ($functions->fetchSelectedItems() as $key => $item):
                $total += $item->price;
                ?>
                <tr>
                  <td><?= $item->name ?></td>
                  <td class="price"><?= $item->price ?></td>
                  <td><a href="#" class="close" value="<?= $key ?>">&times;</a></td>
                </tr>
                <?php endforeach; ?>
              </table>
            </div>
            <div class="title white">Total price: <span id="total-price"><?= $total ?></span>$</div>
            <a href="checkout" id="checkoutbtn" class="btn btn-large btn-block btn-success">Procceed to checkout</a>
          </div>
        </div>
      </div>

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
	<script>
		$(function() {
      var items = $.cookie("items");
      if (items != undefined && items != "") {
        items = items.split("-");
      } else {
        items = new Array();
      }

      var calculateTotalPrice = function() {
        var total = 0;
        var $tr = $("#cart").find("tr");
        $tr.each(function() {
          var price = $(this).children("td:nth-child(2)").html();
          total += parseInt(price);
        });
        $("#total-price").html(total);
      };

      var deleteFromCart = function() {
        var key = $(this).parent("tr").index()-1;
        items.splice(key, 1);
        $.cookie("items", items.join("-"));
        $(this).parents("tr").remove();
        calculateTotalPrice();
      };

      $("#items td a").click(function() {
        var id = $(this).attr("value");
        items.push(id);
        $.cookie("items", items.join("-"));
        var table = document.getElementById("cart");
        var row = table.insertRow(-1);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var $tr = $(this).parents("tr");
        cell1.innerHTML = $tr.children("td:nth-child(1)").html();
        cell2.innerHTML = $tr.children("td:nth-child(4)").html();
        cell2.className = "price";
        cell3.innerHTML = "<a href=\"#\" class=\"close\">&times;</a>";
        $(cell3).children("a").click(deleteFromCart);
        calculateTotalPrice();
      });

      $("#cart td a").click(deleteFromCart);
      
    });
	</script>
</body>
</html>