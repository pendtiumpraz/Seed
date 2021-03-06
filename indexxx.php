<?php

include('core/init.inc.php');

if (isset($_SESSION['email'])){
	if(isset($_SESSION['attempts'])){
		unset($_SESSION['attempts']);	
	}
  header('Location: /home');
  die();
}

if(isset($_SESSION['attempts']) === false){
  $_SESSION['attempts'] = 0;
}

if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
   die();
}

$errors = array();
$info = array();

if(isset($_GET['success3'])){
  $info[] = 'Your account has been successfully upgraded.';
}
if(isset($_GET['success2']) || isset($_GET['active'])){
  $info[] = 'Your account has been successfully registered.';
}

if (isset($_POST['email'], $_POST['password'])){
  if (valid_credentials($_POST['email'], $_POST['password']) === false){
	$errors[] = 'Email / Password incorrect.';
	$_SESSION['attempts']++;
  }

  if(isset($_POST['captcha']) && $_SESSION['attempts'] > 10){
	if(sha1('zgX"4C^S;^A|5U3Xa_>{"V:8t,So97TO^Gb|}vB^Vd;Yuob9b-5|$<:DHlDA[le'.strtolower($_POST['captcha'])) != $_SESSION['word']){
	  $errors[] = 'The CAPTCHA is incorrect.';
	}
  }
  
  if (empty($errors) && is_active($_POST['email']) === false){
	$errors[] = 'This account is not yet activated. Please check your email for the activation email.';
  }
	
  if (empty($errors)){
  	$user_email = html_escape($_POST['email']);
  	$user_id = get_user_id($user_email);
  	$frozen = is_frozen($user_id);
  	if($frozen != false){
	  	$errors[] = "Your account has been temporarily frozen. <b>Reason:</b> {$frozen}";
  	}
  	if (empty($errors)){
		$_SESSION['email'] = $user_email;
		$_SESSION['id'] = $user_id;
		unset($_SESSION['attempts']);
    if(isset($_GET['r'])){
      $r = preg_replace("(https?://)", "", $_GET['r']);
      header("Location: {$r}");
      die();
    }
		header("Location: http://marketdream.org/home");
		die();
	}
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $siteinfo['title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $siteinfo['description']; ?>">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="assets/css/elusive-webfont.min.css">
    <style type="text/css">body {
    background:url("assets/img/index-1.jpg") #544145;
    background-repeat:no-repeat;
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    padding-top:50px;
}
.stease {
    margin-top:35px;
    color:white;
    text-align:center;
    font-size:10em;
    position:relative;
}
@media (max-width: 400px) {
    .stease {
        font-size:7.5em;
        margin-bottom:-10px;
    }
}
@media (max-width: 240px) {
    .stease {
        font-size:5em;
        margin-bottom:-20px;
    }
}
.alert {
    max-width:512px;
    margin:0 auto;
}
.form-signin {
  color:black;
    max-width:500px;
    padding:19px 29px 29px;
    margin:20px auto 20px;
    background:rgba(255,255,255,0.8);
    -webkit-box-shadow:0 1px 2px #aaa;
    -moz-box-shadow:0 1px 2px #aaa;
    box-shadow:0 1px 2px #aaa;
    border-radius:  1px;
}
.form-signin .form-signin-heading, .form-signin .checkbox {
    margin-bottom:10px
}
.form-signin input[type="email"], .form-signin input[type="password"], .form-signin input[type="text"] {
    font-size:16px;
    height:auto;
    margin-bottom:15px;
    padding:7px 9px
}
.form-signin>img {
    max-width:100%
}
.alert {
    position:relative;
    z-index:5;
    margin-bottom:5px;
}</style>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/assets/ico/apple-touch-icon-57-precomposed.png">
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43675987-1', 'auto');
  ga('send', 'pageview');

</script>
  </head>

  <body>
    <noscript><div style="padding-top:15%;width:100%;height:100%;top:0;left:0;right:0;bottom:0;z-index:1000;position:fixed;background:black;text-align:center;color:white;"><h1>Abort, Abort!</h1><h4>Your browser does not have Javascript enabled or does not support it.</h4><h6>Please check if there is a native application available for your system.</h6></div></noscript>

    <div class="container">
    <?php
	if (empty($info) === false){

        foreach ($info as $item){
          echo "<div class='alert alert-success'>{$item}</div>";
        }
    }
    if (empty($errors) === false){
        foreach ($errors as $error){
          echo "<div class='alert alert-danger'>{$error}</div>";
        }
    }

    ?>
    <div class="body">
    <h1 class='stease'>CAIC</h1>
      <form class="form-signin" style="text-align:center;" action='#' method='post'>
        <h1>Login</h1>
        <br />
        <p><input type="text" class="form-control" placeholder="Username" name='email' value='<?php if(isset($_POST['email'])){ echo html_escape($_POST['email']); } ?>' required></p>
		<p><input type="password" class="form-control" placeholder="Password" name='password' required></p>
		<p><small><a href="forgot_password">Forgot your password?</a></small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small><a href="help.html">Need help logging in?</a></small></p>
		<?php if($_SESSION['attempts'] > 10){ ?>
		<p><small>Please enter the text below.</small></p>
		<img src="captcha/image">
		<br /><br />
		<input type="text" class="form-control" placeholder="Answer" name="captcha">
		<?php } ?>
        <button class="btn btn-default btn-lg btn-success" type="submit">Sign in</button>
      </form>
      <div class="footer" style="text-align:center;margin-bottom:35px;">
        <p>Copyright &copy; <?php echo $siteinfo['title']?> 2014. All rights reserved.</p>
      </div>
    </div>
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script src="//marketdream.org/assets/js/prefixfree.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script> 
  </body>
</html>