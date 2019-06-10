<!DOCTYPE html>
<html>
<head>
	
	<title>Accueil</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link href="css/main.css" rel="stylesheet" type="text/css"> 
	<link href="css/page-externe.css" rel="stylesheet" type="text/css"> 
	<link rel="stylesheet" href="css/menu-externe.css" type="text/css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">


</head>
<body>

	<?php
        define("CONST_INCLUDE", true);

        include_once "php/composants/menu_externe/cont_menu_externe.php" ;

        $cont = new ContMenuExterne();

        $cont->afficherMenu("Etudiants");
    ?>


	<main>
		<div class="container-fluid px-0 mt-3 row">
			<div class="container col-md-11">
				<h2 class="text-center text-primary">La bibliothèque</h2>
				<div id="Bibliothèque">
					<p>
						L'établissement met à la disposition des étudiants une salle qui leur est ouverte tous les jours de 8h à 22h. On peut y retrouver une vaste gamme de livres : des romans, des mangas, des manuels scolaires, ainsi que des livres éducatifs. Les étudiants ont la possibilité des les lire librement et les emprunter momentanément.
						Cette salle est calme et permet aux étudiants de l'établissement de pouvoir travailler tranquillement tout en disposant de tous les outils nécessaires pour progresser.
						</p>
				</div>
				<h2 class="text-center text-primary">La restauration</h2>
				<div id="resto">
					<p>
						L'établissement dispose d'une salle de restauration spacieuse. De nombreux plats divers et équilibrés sont ainsi proposés aux étudiants à un prix relativement abordable. De plus, nous faisons en sorte que tous les étudiants ne mangent pas en meme temps, de sorte à ce que le temps d'attente reste acceptable. Enfin, tous les régimes alimentaires y sont respectés: régime végétarien, régime cacher. Tous les étudiants en sont satisfaits.
						</p>
				</div>
				<h2 class="text-center text-primary">Le logement</h2>
				<div id="logement">
					<p>
						L'établissement propose aux étudiants une aide au logement considérable. Il leur permet de trouver le logement qui leur convient à proximité de l'établissement à un prix faible. Si toutefois l'étudiant ne peut pas se permettre de payer autant, létablissement s'engage à l'aider financièrement du mieux qu'il le peut. Chaque année, de nombreux étudiants optent pour un logement à proximité et cela leur permet de réduire le temps de trajet et ainsi de disposer de plus de temps pour travailler et réussir leur année.
						</p>
				</div>
				<h2 class="text-center text-primary">Les activités</h2>
				<div id="activites">
					<p>
						Afin de rendre la scolarité des étudiants plus vivante et plus agréable, l'établissement propose un grand nombre d'activités à pratiquer en dehors des cours. Ainsi, on peut y retrouver des salles de musique, des salles de sport, des salles de jeux et des salles de repos. L'étudiant peut y accéder gratuitement et ainsi décompresser facilement. Ces activités permettent aussi de créer des liens avec d'autres étudiants ce qui facilitera leur insertion.
					</p>
				</div>
			</div>
		</div>

	</main>

	<script src="scripts/Common/jquery-3.3.1.min.js" ></script>
	<script src="scripts/Common/popper.min.js" ></script>
	<script src="scripts/Common/bootstrap.min.js" ></script>

	
</body>
</html>