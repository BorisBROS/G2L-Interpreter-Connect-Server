<!DOCTYPE HTML>
<html>
<head>
<title>Simple Login Form</title>
<meta charset="UTF-8" />
<meta name="Designer" content="PremiumPixels.com">
<meta name="Author" content="$hekh@r d-Ziner, CSSJUNTION.com">
<link rel="stylesheet" type="text/css" href="css/reset.css">
<link rel="stylesheet" type="text/css" href="css/structure.css">
</head>

<body>
<?php 
if( isset($username) or isset($password) )
{
	?>
	<div class="error">Could not authenticate</div>
	<?php
}
?>
<form class="box login" action="ManageInterpreters.php" method="post">
	<fieldset class="boxBody">
	  <label>Username</label>
	  <input name="username" type="text" tabindex="1" value="<?php echo($username) ?>" required>
	  <label><!-- <a href="#" class="rLink" tabindex="5">Forget your password?</a> -->Password</label>
	  <input name="password" type="password" tabindex="2" value="<?php echo($password) ?>" required>
	</fieldset>
	<footer>
	  <label><input type="checkbox" tabindex="3" disabled="disabled">Keep me logged in</label>
	  <input type="submit" class="btnLogin" value="Login" tabindex="4">
	</footer>
</form>
<footer id="main">
  <a href="http://wwww.cssjunction.com">Simple Login Form (HTML5/CSS3 Coded) by CSS Junction</a> | <a href="http://www.premiumpixels.com">PSD by Premium Pixels</a>
</footer>
</body>
</html>
