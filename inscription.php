<?php 
session_start();

require('src/log.php');

if(isset($_SESSION['connect']))  {
	header('location: ../');
	exit();
}

if(!empty($_POST['email']) && !empty($_POST['password'])) {

	require('src/connection.php');
	// variables
	$email 			= htmlspecialchars($_POST['email']);
	$password 		= htmlspecialchars($_POST['password']);
	$password_two 	= htmlspecialchars($_POST['password_two']);
	// tester si les 2 password sont identiques
	if($password != $password_two) {
		header('location:inscription.php?error=1&message=Les mots de passe ne sont pas identiques.');
		exit();
	}
	// ADRESSE courriel valide
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location: inscription.php?error=1&message=Votre adresse email est invalide.');
		exit();
	}
	$req = $db->prepare('SELECT COUNT(*) AS x FROM user WHERE email = ?');
	$req->execute(array($email));
	while($result = $req->fetch()) {
		if($result['x'] != 0) {
		header('location:inscription.php?error=1&message=Cette adresse courriel existe déjà.');
		exit();
		}
	}

	// HASH (secret)
	$secret = sha1($email).time();
	$secret = sha1($secret).time().time();

	// Cryptage du password
	$password = "aq1".sha1($password."1254")."521";

 	// Envoi de la requête dans la base de données
	$req = $db->prepare('INSERT INTO user(email, password, secret) VALUES (?,?,?)');
	$req->execute(array($email, $password, $secret));

	header('location:inscription.php?succes=1');
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Katsall</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>
		<?php
			if(isset($_GET['error'])) { 

			  	if (isset($_GET['message'])) {
					echo '<p class="alert error">'.htmlspecialchars($_GET["message"]).'</p>';
				}
				
			} else if(isset($_GET['succes'])) {
					echo '<p class="alert success">Votre compte a été créé. <a href="index.php">Veuillez vous connecter.</a></p>';
			}
		?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur ChatFlix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>