<?php
include "general.php";

$error = false;
$success = false;
if (isset($_POST["username"]) && $_POST["password"] && $_POST["password_repeat"]) {
  try {
    $success = $functions->register($_POST["username"], $_POST["password"], $_POST["password_repeat"]);
  } catch(Exception $e) {
    $error = $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Websecurity shop - Register</title>
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
      	<div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="brand" href="index.php">Websecurity shop</a>
            <div class="nav-collapse collapse">
              <ul class="nav">
                <?php if ($_SESSION["auth"]) { ?>
                <li><div class="nav-acc"><?= $_SESSION["username"] ?> (<a href="logout.php">Logout</a>)</div></li>
                <?php } else { ?>
                <li><a href="login.php">Login</a></li>
                <?php } ?>
              </ul>
            </div><!--/.nav-collapse -->
      	</div><!-- /.container -->
      </div><!-- /.navbar-inner -->
    </div><!-- /.navbar -->

    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    <div class="container marketing">

      <h1>Register</h1>

      <?php if ($error) { ?>

      <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Oh snap!</strong> <?= $error; ?>
      </div>

      <?php } elseif ($success) { ?>

      <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Well done!</strong> You successfully registered your account: <?= $_POST["username"] ?>.
      </div>
      <a href="login.php"><h3>To the login page!</h3></a>

      <?php } else { ?>

      <form method="post" class="form-horizontal">
        <div class="control-group">
          <label class="control-label" for="inputUsername">Username</label>
          <div class="controls">
            <input type="text" id="inputUsername" name="username" placeholder="Username">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputPassword">Password</label>
          <div class="controls">
            <input type="password" id="inputPassword" name="password" placeholder="Password">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputPassword2">Repeat Password</label>
          <div class="controls">
            <input type="password" id="inputPassword2" name="password_repeat" placeholder="Repeat password">
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <button type="submit" class="btn">Register</button>
          </div>
        </div>
      </form>

      <?php } ?>

    </div><!-- /.container -->

    <!-- FOOTER -->
    <footer>
      <div class="container">
        <p class="pull-right"><a href="#">Tillbaka till toppen</a></p>
        <p>© 2013 Websäkerhet, LTH · <a href="about.php">Contact</a></p>
      </div>
    </footer>

  </div>

  <!-- Le javascript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script>
		var current;
		function showDetails(val) {
			if (current != val) {
				$("#details-wrap div").hide("fast");
				$("#"+val+"-d").show("fast");
				current = val;
			}
		}
	</script>
</body>
</html>