<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	<title></title>
	<link rel="stylesheet" type="text/css" href="style.css" />
		
	<style type="text/css">
	<!--
	-->
	</style>
</head>	
</head>

<body onLoad="document.login.username.focus();">

	  <div id="stripe">
	  &nbsp;
	  </div>	
	  
	  <div id="container">
	  
		<div id="logoArea">
           <img src="yubicoLogo.gif" alt="yubicoLogo" width="150" height="75"/>
		</div>
		
		<div id="greenBarContent">
			<div id="greenBarImage">
				<img src="yubikey.jpg" alt="yubikey" width="150" height="89"/>
			</div>
			<div id="greenBarText">
				<h3>
				Legacy login: Username/password + YubiKey
				</h3>
			</div>
		</div>
		<div id="bottomContent">		
<?php include 'authenticate.php';
if ($authenticated == 0) { ?>
	<h1 class="ok">Congratulations <?php if ($realname) { print "$realname!"; }?></h1>
	<p>You have been successfully authenticated with the YubiKey.
<?php } else { ?>
	<ol>
	<li>Place your YubiKey in the USB-port.</li>
	<li>Enter Username in the username field.</li>
	<li>Enter password.</li>
	<li>Put your finger on the YubiKey button and hold it
	  steady until the YubiKey field is filled.</li>
	</ol>
	<p>No password? You can <a href="admin.php">set password</a> directly. </p>
	<br />

<?php if ($authenticated > 0) { ?>
		<h1 class="fail">Login failure. Please try again. </h1>
<?php } ?>

	<form name="login" method="post" style="border: 1px solid #e5e5e5; background-color: #f1f1f1; padding: 10px; margin: 0px;">
	<input type="hidden" name="mode" value="legacy">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="150">
					<b>Username</b>
			</td>
			<td width="470">
				  <input autocomplete="off" type="text" name="username">
			</td>
		</tr>
		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
		<tr>
			<td  width="150">
				<b>Password</b>
			</td>
			<td width="470">
			  <input autocomplete="off" type="text" name="password">
			</td>
		</tr>
		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
		<tr>
			<td width="150">
				<b>YubiKey</b>
			</td>
			<td width="470">
			  <input autocomplete="off" type="text" name="key" class="yubiKeyInput"><input type="submit" value="Go" style="border: 0px; font-size: 0px; background: none; padding: 0px; margin: 0px; width: 0px; height: 0px;" />
			</td>
		</tr>
	</table>
	</form>

<?php } ?>

	<br /><br />
	<p>&raquo; <a href="two_factor_legacy.php">Try again</a>
	<p>&raquo; <a href="./">Back to main page</a><br /><br />
	<br /><br /><br /><br />

<?php if ($authenticated >= 0) { ?>
	<h3>Technical details</h3>
	More information about the performed transcaction:
	<br /><br />
	<?php include 'debug.php';
} ?>

</div>
</body>
</html>
