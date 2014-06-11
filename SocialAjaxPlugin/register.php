<?php

	include '../db/db.php';
	include 'User.php';

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	global $user;
	$error = 0;
	$ok = 0;
	$message = array();

	if (isset($_GET['s']))
	{
		$service = $_GET['s'];

		switch ($service) {
			case 'facebook':
				$user = new FacebookUser($_POST);
				break;
			case 'google':
				$user = new GoogleUser($_POST);
				break;
			default:
				echo json_encode(array('response' => 'error'));
				exit;
		}
	}

	//var_dump($user);
	if (isset($_GET['id']))
	{
		//$user->id = intval($_GET['id']); else $user->id = 0;
		if (isset($_GET['username']))   $user->username = mysql_real_escape_string($_GET['username']);     else $user->username = '';
		if (isset($_GET['email']))      $user->email = mysql_real_escape_string($_GET['email']);           else $user->email = '';
		if (isset($_GET['first_name'])) $user->first_name = mysql_real_escape_string($_GET['first_name']); else $user->first_name = '';
		if (isset($_GET['last_name']))  $user->last_name = mysql_real_escape_string($_GET['last_name']);   else $user->last_name = '';
		if (isset($_GET['phone']))  $user->phone = mysql_real_escape_string($_GET['phone']);   else $user->phone = '';
	}

	if (isset($_POST['email'])) {
		if (isset($_POST['id']))         $user->id = $_POST['id']; else $user->id = 0;
		if (isset($_POST['username']))   $user->username = mysql_real_escape_string($_POST['username']);     else $username = '';
		if (isset($_POST['email']))      $user->email = mysql_real_escape_string($_POST['email']);           else $user->email = '';
		if (isset($_POST['first_name'])) $user->first_name = mysql_real_escape_string($_POST['first_name']); else $user->first_name = '';
		if (isset($_POST['last_name']))  $user->last_name = mysql_real_escape_string($_POST['last_name']);   else $user->last_name = '';
		if (isset($_POST['phone']))      $user->phone = mysql_real_escape_string($_POST['phone']);   		 else $user->phone = '';

		$pass = mysql_real_escape_string($_POST['pass']);
		$repass = mysql_real_escape_string($_POST['repass']);

		//check password
		if (strlen($pass) < 6 || $pass != $repass) {
			$error = 1;
			$message[] = '<p style="color: red">Passwords do not match (min 6 characters)!</p>'.
		}
		$user->password = $pass;
		$user->country = mysql_real_escape_string($_POST['country']);

		if ($user->country == 99999) {
			$error = 1;
			$message[] = '<p style="color: red">Please select your country!</p>';
		}

		//if all ok save the user
		if ($error == 0) {
			$response = $user->saveUser();
			var_dump(json_decode($response));
			$ok = 1;
		}
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="robots" content="index, follow">
		<meta property="og:title" content="Netent Slots - Latest Net Entertainment Slot Games" />
		<meta property="og:type" content="website " />
		<meta property="og:url" content="http://www.slots4play.com/freeslots/netent.php" />
		<meta property="og:image" content="/slots4play.png" />
		<meta name="keywords" content="netent slots, latest netent slots, netent gaming, netent latest games ">
		<meta name="description" content="Play the latest netent slots and get daily updates of upcoming news from the Net Entertainment Software">
		<meta http-equiv="content-language" content="en-GB" />
		<meta property="og:site_name" content="Slots4Play" />
		<meta property="fb:admins" content="100002036197615" />
		<meta name="dcterms.rights" content="2014, Slots4play.com" />
		<meta name="dcterms.audience" content="global" />
		<link rel="author" href="https://plus.google.com/b/115873352258648051739/+Slots4play-free-slots/posts" />
		<meta name="rating" content="general">
		<link href="/images/favicon.ico" rel="icon" type="image/x-icon" />
		<meta name="msvalidate.01" content="C4E7C0A0F066F838F6BF617C6720BEB8" />
		<link rel="stylesheet" href="/css1/font-awesome.min.css">
		<link rel="stylesheet" href="/css1/style.css">
		<script type="text/javascript" src="/javascripts/jquery-1.11.0.min.js"></script>
		<script src="/javascripts/jquery.cycle2.min.js"></script>
		<script src="http://malsup.github.io/jquery.cycle2.tile.js"></script>
		<script type="text/javascript" src="/javascripts/scripts.js"></script>
		<?php //include($_SERVER["DOCUMENT_ROOT"] . '/include/social1.php'); ?>
		<title>Netent Slots - Register</title>
<?php include($_SERVER["DOCUMENT_ROOT"] . '/include/analytics-dd.php');  ?>
	</head>
	<body>
		<a href="https://www.slots4play.comhttps://www.slots4play.com/app/slots-on-facebook.php">
			<div id="sign-up"></div>
		</a>
		<img src="/images1/bgimage.jpg" style="width: 100%; z-index: 0; position: fixed;"/>
		<div style="background: url('/images1/maincontent-l.png') 100% 0 repeat-y; background-color: darkslategray !important; position: absolute;
			 height: 157%;
			 padding: 7px;
			 left: 18.8%;
			 clear: both;
			 "></div>
		<div style="background: url('/images1/maincontent-r.png') 100% 0 repeat-y; background-color: darkslategray !important; position: absolute;
			 height: 157%;
			 padding: 7px;
			 right: 18.8%;
			 clear: both;
			 "></div>
			<?php include($_SERVER["DOCUMENT_ROOT"] . '/include/parse-rss.php'); ?>
		<div class="wrapper" style="z-index:1; position:relative ;">


		<?php include($_SERVER["DOCUMENT_ROOT"] . '/include/head2.php'); ?>
			<div class="content">
				<div style="background: #fff; width: 60%; height: 400px;">
					<?php if (isset($message)) echo $message; ?>

					<?php if ($ok == 0) { ?>
					<form action="register.php?s=<?php echo $service; ?>" method="POST">
						<input type="hidden" name="id" value="<?php echo intval($_GET['id']); ?>" ?>
						<table>
							<tr>
								<td><strong>Username</strong></td>
								<td><input type="text" name="username" value="<?php echo $user->username; ?>" /></td>
							</tr>
							<tr>
								<td><strong>Email</strong></td>
								<td><input type="email" name="email" value="<?php echo $user->email; ?>" /></td>
							</tr>
							<tr>
								<td><strong>Password</strong></td>
								<td><input type="password" name="pass" /></td>
							</tr>
							<tr>
								<td><strong>Password control:</strong></td>
								<td><input type="password" name="repass" /></td>
							</tr>
							<tr>
								<td><strong>First name</strong></td>
								<td><input type="text" name="first_name" value="<?php echo $user->first_name; ?>" /></td>
							</tr>
							<tr>
								<td><strong>Last name</strong></td>
								<td><input type="text" name="last_name" value="<?php echo $user->last_name; ?>" /></td>
							</tr>
							<tr>
								<td><strong>Country</strong></td>
								<td>
									<select id="inputCountry" name="country" class="input-xlarge">
										<option value="99999">Choose your country</option>
										<option value="0" style="display: none;">0</option>
										<option value="1">United States</option>
										<option value="2">United Kingdom</option>
										<option value="3">Canada</option>
										<option value="4">Europe</option>
										<option value="5">Germany</option>
										<option value="6">Netherlands</option>
										<option value="7">Australia</option>
										<option value="8">Sweden</option>
										<option value="9">Ireland</option>
										<option value="10">Italy</option>
										<option value="11">Denmark</option>
										<option value="12">France</option>
										<option value="13">Austria</option>
										<option value="111">Other</option>
									</select>
								</td>
							</tr>
							<tr>
								<td><strong>Date of birth</strong></td>
								<td>
									<div class="input date"><label for="UserBirthDateDay">Date of birth</label>

										<select name="data[User][birth_date][day]" class="input-small" id="UserBirthDateDay">
											<?php
												for ($i = 1; $i < 32; $i++) {
													echo '<option value="'.$i.'">'.$i.'</option>';
												}
											?>
										</select>

										<select name="data[User][birth_date][month]" class="input-small" id="UserBirthDateMonth">
											<option value="01">January</option>
											<option value="02">February</option>
											<option value="03">March</option>
											<option value="04">April</option>
											<option value="05">May</option>
											<option value="06" selected="selected">June</option>
											<option value="07">July</option>
											<option value="08">August</option>
											<option value="09">September</option>
											<option value="10">October</option>
											<option value="11">November</option>
											<option value="12">December</option>
										</select>-
											<select name="data[User][birth_date][year]" class="input-small" id="UserBirthDateYear">
											<?php
												for ($i = 2014; $i > 1920; $i--) {
													echo '<option value="'.$i.'">'.$i.'</option>';
												}
											?>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td><strong>Phone</strong></td>
								<td><input type="text" name="phone" value="<?php echo $user->phone; ?>" /></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" /></td>
							</tr>
						</table>
					</form>
					<?php } else { ?>
					<p style="color: green"><strong>Your account was successfuly created!</strong></p>
					<?php } ?>
				</div>
			</div>
			<div class="sidebar">
				<?php include($_SERVER["DOCUMENT_ROOT"] . '/include/s4-350.php'); ?>

				<div class="search">
					<div class="search-wrapper">
						<div class="search-inner">
							<form class="form-wrapper" action="/search.php"  type="get">
								<input type="text" id="search" name="for">
								<button type="submit" >Search</button>
								<!-- <button type="submit">Search</button> -->
							</form>
						</div>
					</div>
				</div>
				<div class="facebook" style="background-color: white">
					<p><iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fslots4play&amp;width&amp;height=290&amp;colorscheme=light&amp;show_faces=true&amp;header=true&amp;stream=false&amp;show_border=true&amp;appId=663758380335197" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:290px;" allowTransparency="true"></iframe></p><br />
				</div>
				<div class="adv">
					<?php include($_SERVER['DOCUMENT_ROOT']."/include/net-1.php"); ?>
				</div>
				<div class="twitter">
					<a class="twitter-timeline" data-dnt="true" data-widget-id="453696677925249025" href="https://twitter.com/slots4play">Tweets by @slots4play</a>
					<script>!function(d, s, id) {
							var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
							if (!d.getElementById(id)) {
								js = d.createElement(s);
								js.id = id;
								js.src = p + "://platform.twitter.com/widgets.js";
								fjs.parentNode.insertBefore(js, fjs);
							}
						}(document, "script", "twitter-wjs");</script>
				</div>
			</div>
		</div>

		<script>
			var menu = new menu.dd("menu");
			menu.init("menu", "menuhover");
		</script>
	</body>
</html>