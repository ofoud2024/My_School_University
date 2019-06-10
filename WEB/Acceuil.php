<!DOCTYPE html>
<html>
<head>
	
	<title>Accueil</title>
	<meta charset="utf-8">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link href="css/main.css" rel="stylesheet" type="text/css"> 
	<link href="css/page-externe.css" rel="stylesheet" type="text/css"> 
	<link href="css/menu-externe.css" rel="stylesheet"  type="text/css">


</head>
<body>

	<?php
        define("CONST_INCLUDE", true);

        include_once "php/composants/menu_externe/cont_menu_externe.php" ;

        $cont = new ContMenuExterne();

        $cont->afficherMenu("Accueil");
    ?>


	<main class="pt-3 container-fluid row justify-content-around accueil" >
		<div class="col-md-7 news-image ">

			<img src="images/news/dut_3ans_news.jpg" class="active px-0 col-12" alt="" />
			<img src="images/news/programmation_primaire.jpg" class="px-0 col-12" alt="" />
			<img src="images/news/reforme_bac.jpg" class="px-0 col-12" alt="" />
			<img src="images/news/violences_sur_twitter.jpg" class="px-0 col-12" alt="" />
			<img src="images/news/inegalites_scolaires.jpg" class="px-0 col-12" alt="" />

			<div class="direction-btn col-12 row  justify-content-center">
				<div class="col-auto justify-content-around">
					<i class="fas fa-angle-double-left col-5" ></i>
					<i class="fas fa-angle-double-right col-5 "></i>
				</div>

			</div>

		</div>
		
		<div class="col-md-5  news-titles">
			<div class="container-fluid justify-content-center ">
				<h3>Vers un DUT en 3 ans ?</h3>
			</div>

			<div class="container-fluid justify-content-center">
				<h3>
					La programmation a-t-elle sa place dans les écoles primaires ?
				</h3>
			</div>


			<div class="container-fluid justify-content-center">
				<h3>
					Réforme du bac : les lycéens devront choisir 7 spécialités
				</h3>
			</div>


			<div class="container-fluid justify-content-center">
				<h3>
					#PasDeVague : les enseignants dénoncent les violences qu'ils subissent sur Twitter
				</h3>
			</div>


			<div class="container-fluid justify-content-center">
				<h3>
					Les inégalités scolaires en Île-de-France
				</h3>
			</div>



		</div>
		
	</main>

	<script src="scripts/Common/jquery-3.3.1.min.js" ></script>
	<script src="scripts/Common/popper.min.js" ></script>
	<script src="scripts/Common/bootstrap.min.js" ></script>
	
	<script src="scripts/custom-types.js"></script>
	<script src="scripts/accueil.js"></script>

</body>
</html>