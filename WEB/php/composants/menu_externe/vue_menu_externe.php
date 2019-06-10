<?php

	if(!defined('CONST_INCLUDE')){
		die("Accès interdit");
	}

	class VueMenuExterne{

		public function afficherMenu($liste, $active) {

			$acceuil_active 	= $active === "Accueil" ? "active" : "";
			$formation_active 	= $active === "Formation" ? "active" : "";
			$etudiants_active 	= $active === "Etudiants" ? "active" : "";
			$inscription_active = $active === "Inscription" ? "active" : "";
			$contact_active		= $active === "Contact" ? "active" : "";

			echo '
			<nav id="navigation-externe" class="navbar navbar-expand-lg navbar-light bg-light">
			<a class="navbar-brand" href="Accueil.php">
				<img id="logo-img" style="
			height: 50px;
			width: 60px;
		" src="images/logo_etablissement.png" alt="my-school university" />
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
			 aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNavDropdown">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item '.$acceuil_active.'">
						<a class="nav-link" href="Accueil.php">Accueil <span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item '.$formation_active.'">
						<a class="nav-link" href="Formation.php">Formation</a>
					</li>
					<li class="nav-item '.$etudiants_active.'">
						<a class="nav-link" href="Etudiants.php">Etudiants</a>
					</li>
					<li class="nav-item '.$inscription_active.'">
						<a class="nav-link" href="Inscription.php">Inscription</a>
					</li>
					<li class="nav-item '.$contact_active.'">
						<a class="nav-link" href="Contact.php">Contact</a>
					</li>
				</ul>
		
				<a href="index.php?module=connexion&action=afficherConnexion">
					<button class="btn btn-outline-primary">
						Se connecter
					</button>
				</a>
		
		
			</div>
		</nav>
			';

		}
	}
?>
