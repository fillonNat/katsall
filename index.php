<?php 
session_start();

require('src/log.php');

if(!empty($_POST['email']) && !empty($_POST['password'])) {
	
	require('src/connection.php');
	// variables
	$email 			= htmlspecialchars($_POST['email']);
	$password 		= htmlspecialchars($_POST['password']);

	// ADRESSE courriel valide
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location:index.php?error=1&message=Votre adresse email est invalide.');
		exit();
	}

	// Cryptage du password
	$password = "aq1".sha1($password."1254")."521";

	// vérifier le email si il est dans la base de donnée et on le compare avec le password
	$req = $db->prepare('SELECT count(*) as x FROM user WHERE email = ?');
	$req->execute(array($email));

	while($email_verif = $req->fetch()) {
		if($email_verif['x'] != 1) { 
			header('location:../?error=1&message=Impossible de vous authentifier correctement.');
			exit();
		}
	} 

		// Connection
		$req = $db->prepare('SELECT * FROM user WHERE email = ?');
		$req->execute(array($email));

		while($user = $req->fetch()) {
			if($password == $user['password']) {
				$_SESSION["connect"] =1;
				$_SESSION["email"] = $user["email"];

				if(isset($_POST['auto'])) {
				setcookie('auth', $user['secret'], time() + 365*24*3600, '/', null, false, true);
				}

				header('location:../index.php?succes=1');
				exit();
			}
			else {
				header('location:../?error=1&message=Impossible de vous authentifier correctement.');
				exit();
			}
		}
}
	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Katsall</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<?php if(isset($_SESSION['connect'])) { ?>
				<h1>Bienvenue</h1>
				<p>Qu'allez-vous regarder aujourd'hui ?</p>
				<small><a href="src/logout.php">Déconnexion</a></small>

			<?php } else { ?>
		
				<h1>S'identifier</h1>
				<?php
				if(isset($_GET['error'])) {
					if (isset($_GET['message'])) {
					echo '<p class="alert error">'.htmlspecialchars($_GET["message"]).'</p>';
					}
				}
				else if(isset($_GET['succes'])) {
					echo '<p class="alert success">Bienvenue ! Vous êtes connecté.</p>';
				}
				?>

				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>
			

				<p class="grey">Première visite sur Katsall ? <a href="inscription.php">Inscrivez-vous</a>.</p>
			  <?php } ?>
			</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>