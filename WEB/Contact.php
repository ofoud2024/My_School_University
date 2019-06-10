<!DOCTYPE html>
<html>
<head>
	
	<title>Contact</title>
	<meta charset="utf-8">
    <link href="css/Common/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="css/main.css" rel="stylesheet" type="text/css"> 
	<link href="css/page-externe.css" rel="stylesheet" type="text/css"> 
	<link rel="stylesheet" href="css/menu-externe.css" type="text/css">
    <link href="frameworks/font-awesome/css/all.css" rel="stylesheet" type="text/css">


</head>
<body>

	<?php
        define("CONST_INCLUDE", true);

        include_once "php/composants/menu_externe/cont_menu_externe.php" ;

        $cont = new ContMenuExterne();

        $cont->afficherMenu("Contact");
    ?>


	<main class="contact">

		<div class="scene scene--card">
			<div class="card">
				<div class="card__face card__face--front">
					<img src="images/contact/omar.jpg" class="image-contact"/>
				</div>
				<div class="card__face card__face--back">
					<table class="mx-auto ">
						<tr>
							<td class="text-secondary">Nom: </td>
							<td class="text-center">FOUDANE</td>
						</tr>

						<tr>
							<td class="text-secondary">Prénom: </td>
							<td class="text-center">Omar</td>
						</tr>


						<tr>
							<td class="text-secondary">Age: </td>
							<td class="text-center">19 ans</td>
						</tr>


						<tr>
							<td class="text-secondary">Spécialité: </td>
							<td class="text-center">Full stack</td>
						</tr>


						<tr>
							<td class="text-secondary">Tél: </td>
							<td class="text-center">06 50 48 50 30</td>
						</tr>

						<tr>
							<td class="text-secondary">Mail: </td>
							<td class="text-center">omar.gta.99@gmail.com</td>
						</tr>


					</table>

				</div>
			</div>
		</div>

		<div class="scene scene--card">
			<div class="card">
				<div class="card__face card__face--front">
					<img src="images/contact/amel.jpg" class="image-contact"/>
				</div>
				<div class="card__face card__face--back">
				<table class="mx-auto ">
						<tr>
							<td class="text-secondary">Nom: </td>
							<td class="text-center">TRAORE</td>
						</tr>

						<tr>
							<td class="text-secondary">Prénom: </td>
							<td class="text-center">Amel</td>
						</tr>


						<tr>
							<td class="text-secondary">Age: </td>
							<td class="text-center">19 ans</td>
						</tr>


						<tr>
							<td class="text-secondary">Spécialité: </td>
							<td class="text-center">Front end</td>
						</tr>


						<tr>
							<td class="text-secondary">Tél: </td>
							<td class="text-center">06 70 48 60 30</td>
						</tr>

						<tr>
							<td class="text-secondary">Mail: </td>
							<td class="text-center">atraore@gmail.com</td>
						</tr>


					</table>

				</div>
			</div>
		</div>


		<div class="scene scene--card">
			<div class="card">
				<div class="card__face card__face--front">
					<img src="images/contact/yilmaz.jpg" class="image-contact"/>
				</div>
				<div class="card__face card__face--back">
				
				<table class="mx-auto ">
						<tr>
							<td class="text-secondary">Nom: </td>
							<td class="text-center">YILMAZ</td>
						</tr>

						<tr>
							<td class="text-secondary">Prénom: </td>
							<td class="text-center">TEOMAN</td>
						</tr>


						<tr>
							<td class="text-secondary">Age: </td>
							<td class="text-center">19 ans</td>
						</tr>


						<tr>
							<td class="text-secondary">Spécialité: </td>
							<td class="text-center">Back end</td>
						</tr>


						<tr>
							<td class="text-secondary">Tél: </td>
							<td class="text-center">06 09 16 62 99</td>
						</tr>

						<tr>
							<td class="text-secondary">Mail: </td>
							<td class="text-center">tyilmaz@gmail.com</td>
						</tr>


					</table>

				</div>
			</div>
		</div>


</main>

	<script src="scripts/Common/jquery-3.3.1.min.js" ></script>
	<script src="scripts/Common/popper.min.js" ></script>
	<script src="scripts/Common/bootstrap.min.js" ></script>

	<script>

		$(".card").click(function(e){
			$(this).toggleClass("is-flipped");
		})
	</script>
</body>
</html>