<?php
include "functions.php";
include "general.php";

/* Force login before checkout */
if (!$_SESSION["auth"]) {
  header("Location: https://".$_SERVER["HTTP_HOST"]."/login?return=checkout");
  exit();
}

$error = false;
if (isset($_POST["checkoutSubmit"])) {
  try {
    $functions->checkout();
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
      <h1>Checkout</h1>
      <div class="row-fluid">
        <div class="span9">
          <div class="row">
            <table class="table">
              <thead>
                <th>Item</th>
                <th>Price</th>
              </thead>
              <?php
              $total = 0;
              foreach ($functions->fetchSelectedItems() as $key => $item):
              $total += $item->price;
              ?>
              <tr>
                <td><?= $item->name ?></td>
                <td class="price"><?= $item->price ?></td>
              </tr>
              <?php endforeach; ?>
            </table>
            <div class="pull-right">Total price: <strong><?= $total ?>$</strong></div>
          </div>
          <hr>

          <?php if ($error) { ?>

          <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Oh snap!</strong> <?= $error; ?>
          </div>

          <?php } ?>

          <form method="post" class="span9">
            <h5>Your details</h5>
            <div class="control-group">
              <label class="control-label" for="inputEmail">Email</label>
              <div class="controls">
                <input type="email" id="inputEmail" name="inputEmail" placeholder="Email" value="<?= getPostIfIsset("inputEmail") ?>" class="span10">
              </div>
            </div>
            <div class="controls controls-row">
              <div class="control-group span5">
                <label class="control-label" for="inputFirstName">First Name</label>
                <div class="controls">
                  <input type="text" id="inputFirstName" name="inputFirstName" placeholder="First Name" value="<?= getPostIfIsset("inputFirstName") ?>" class="span12">
                </div>
              </div>
              <div class="control-group span5">
                <label class="control-label" for="inputLastName">Last Name</label>
                <div class="controls"> 
                  <input type="text" id="inputLastName" name="inputLastName" placeholder="Last Name" value="<?= getPostIfIsset("inputLastName") ?>" class="span12">
                </div>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="inputAddress">Address</label>
              <div class="controls">
                <input type="text" id="inputAddress" name="inputAddress" placeholder="Address" value="<?= getPostIfIsset("inputAddress") ?>" class="span10">
              </div>
            </div>
            <div class="controls controls-row">
              <div class="control-group span3">
                <label class="control-label" for="inputPostcode">Postcode</label>
                <div class="controls">
                  <input type="text" id="inputPostcode" name="inputPostcode" placeholder="Postcode" value="<?= getPostIfIsset("inputPostcode") ?>" class="span12">
                </div>
              </div>
              <div class="control-group span7">
                <label class="control-label" for="inputCity">City</label>
                <div class="controls"> 
                  <input type="text" id="inputCity" name="inputCity" placeholder="City" value="<?= getPostIfIsset("inputCity") ?>" class="span12">
                </div>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="inputPhone">Phone-number</label>
              <div class="controls">
                <input type="tel" id="inputPhone" name="inputPhone" placeholder="Phone-number" value="<?= getPostIfIsset("inputPhone") ?>" class="span5">
              </div>
            </div>
            <hr><h5>Payment details <small>You don't need to fill this, since this is just a test site...</small></h5>
            <div class="control-group">
              <label class="control-label" for="inputCardOwner">Credit-card owners full name</label>
              <div class="controls">
                <input type="text" id="inputCardOwner" name="inputCardOwner" placeholder="Full name" value="<?= getPostIfIsset("inputCardOwner") ?>" class="span10">
              </div>
            </div>
            <div class="controls controls-row">
              <div class="control-group span7">
                <label class="control-label" for="inputCardNr">Credit card number</label>
                <div class="controls"> 
                  <input type="text" id="inputCardNr" name="inputCardNr" placeholder="Credit card number" value="<?= getPostIfIsset("inputCardNr") ?>" class="span12">
                </div>
              </div>
              <div class="control-group span3">
                <label class="control-label" for="inputCVC">CVC</label>
                <div class="controls">
                  <input type="text" id="inputCVC" name="inputCVC" placeholder="CVC" value="<?= getPostIfIsset("inputCVC") ?>" class="span12">
                </div>
              </div>
            </div>
            <div class="controls controls-row">
              <div class="control-group span5">
                <label class="control-label" for="inputExpirationMonth">Expiration Month</label>
                <div class="controls">
                  <select id="inputExpirationMonth" name="inputExpirationMonth">
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                  </select>
                </div>
              </div>
              <div class="control-group span5">
                <label class="control-label" for="inputExpirationYear">Expiration Year</label>
                <div class="controls">
                  <select id="inputExpirationYear" name="inputExpirationMonth">
                    <option>2013</option>
                    <option>2015</option>
                    <option>2016</option>
                    <option>2017</option>
                    <option>2018</option>
                  </select>
                </div>
              </div>
            </div>
            <<hr><h5>Confirm by entering your password</h5>
            <div class="control-group">
              <label class="control-label" for="inputPassword">Password</label>
              <div class="controls">
                <input type="password" id="inputPhone" name="inputPassword" placeholder="Password" value="<?= getPostIfIsset("inputPassword") ?>" class="span5">
              </div>
            </div>
            <button name="checkoutSubmit" class="btn btn-large btn-success">Procceed to payment</button>
          </form>
        </div>
        <div class="span3">
          <a href="index" class="btn btn-large btn-block btn-danger">Back to item-list</a>
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
</body>
</html>