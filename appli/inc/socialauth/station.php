<?php ob_start(); session_start();

$_SESSION['authstation'] = (isset($_SESSION['authstation']) ? $_SESSION['authstation'] : null);
if ( !isset($_SESSION['authstation']) ) {
  $_SESSION['authstation'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

 ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SONHLAB Social Auth PHP Plugin Demo v2 - Login</title>
<meta name="description" content="This is a demo of SONHLAB Social Auth PHP Plugin. This Plugin is free and easy to use" />
<link rel="stylesheet" href="css/style.css" type="text/css" charset="utf-8"/>
<link href='http://fonts.googleapis.com/css?family=Wire+One|Cuprum' rel='stylesheet' type='text/css'>
</head>
<body>

<?php
if ( !isset($_SESSION["userprofile"]) ) {
?>

<div id="header">
  <h1><a href="http://docs.sonhlab.com/social-auth-for-php-app/" style="color:#1e1e1e;">LOGIN</a></h1>
</div>

<!-- Start Main Container -->
<div class="maincontainer">
  <!-- Start Logo -->
  <div class="logo">
    <img src="images/sonhlab-logo.png" alt="SONHLAB Logo" />
  </div>
  <!-- End Logo -->

  <!-- Start Facebook -->
  <a href="auth/login.php?app=facebook"class="button fb">
    <span>Facebook</span>
  </a>
  <!-- End Facebook -->

  <!-- Start Google -->
  <a href="auth/login.php?app=google" class="button gg">
    <span>Google</span>
  </a>
  <!-- End Google -->

  <!-- Start Twitter -->
  <a href="auth/login.php?app=twitter" class="button tt">
    <span>Twitter</span>
  </a>
  <!-- End Twitter -->

  <div style="clear:both;"></div>

  <div id="credits">
    <div>
      <div class="tile-bt-long solid-green hovershadow-green">
        <a href="http://docs.sonhlab.com/social-auth-for-php-app/" target="_blank" title="SSA Documentation">
          <img src="images/documents.png" alt="SONHLAB Social Auth Docs" />
          <span>SONHLAB Social Auth Docs</span>
        </a>
      </div>
    </div>
  </div>

</div>
<!-- End Main Container -->


<?php } else { ?>

<!-- Start Main Container -->
<div class="maincontainer">

  <!-- Start Welcome -->
  <div class="welcome">
    <h2>Hi, <?php echo $_SESSION["userprofile"]['name']; ?></h2>
    <h2>Hi, <?php echo $_SESSION["userprofile"]['email']; ?></h2>
    <p>You are logged in successfully. See your details below.</p>
  </div>
  <!-- End Welcome -->

  <!-- Start Details -->
  <div class="details">

    <pre>
    <?php
        print_r($_SESSION["userprofile"]);
    ?>
    </pre>

  </div>
  <!-- End Details -->

  <h2 class="logout">
    <a href="auth/logout.php">Logout</a>
  </h2>



  </div>
<!-- End Main Container -->
<?php } ?>

</body>
</html>



<?php ob_end_flush(); ?>
