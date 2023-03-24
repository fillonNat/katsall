<?php
	
	if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {
		// variable
		$secret = htmlspecialchars($_COOKIE['auth']);

		// vérification
		require('src/connection.php');
		$req = $db->prepare('SELECT count(*) as x FROM user WHERE secret = ?');
		$req->execute(array($secret));

		while($user = $req->fetch()) {

			if($user['x'] == 1) {

				$reqUser = $db->prepare("SELECT * FROM user WHERE secret = ?");
				$reqUser->execute(array($secret));

				while($userAccount = $reqUser->fetch()) {
					$_SESSION["connect"] =1;
					$_SESSION["email"] = $userAccount["email"];

				}
			}
		}
	}
?>