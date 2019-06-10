<!DOCTYPE html>
<html>
<head>
	
	<title>Inscription</title>
	
    <link href="css/Common/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="css/main.css" rel="stylesheet" type="text/css"> 
	<link href="css/page-externe.css" rel="stylesheet" type="text/css"> 
	<link rel="stylesheet" href="css/menu-externe.css" type="text/css">
    <link href="frameworks/font-awesome/css/all.css" rel="stylesheet" type="text/css">


</head>
<body>

	<?php 
		define("CONST_INCLUDE",true);

		include_once "php/composants/menu_externe/cont_menu_externe.php" ;

		$cont = new ContMenuExterne();

		$cont->afficherMenu("Inscription");
	?>


	<main>
		<div class="row text-justify">
			<article  id="art1tiltle" class="container-fluid bg-transparent col-lg-5">
				<h2 >ADMISSION EN DUT</h2>
				<h3>Après le BAC Français ...</h3>
				<p>Vous êtes candidat au baccalauréat français ou titulaire d'un baccalauréat français antérieur. Vous souhaitez candidater à <b>MySchoolUniversity</b> ?<br/>
					Vous devez saisir votre candidature sur <a href="www.parcoursup.fr">www.parcoursup.fr <br/><br/>
				Le détail de la procédure est décrit sur le site <a href="www.parcoursup.fr">www.parcoursup.fr</a>. </p>
				<h3 style="color: rgb(45, 196, 183)">Si vous êtes étranger ...</h3>
				<p>Vous êtes étudiant étranger et vous souhaitez suivre un semestre ou une année d’études à <b>MySchoolUniversity</b> ?
				Prenez contact avec les Relations Internationales de votre université. Suivez la procédure demandée par votre université.<br/>
				Si vous avez été sélectionné, vous devez :<br/>
				- Candidatez en ligne<br/>
			- Télécharger les pièces jointes dans le formulaire en ligne<br/>
			- Soumettre la candidature avant le 5 juin (début de cours en septembre).</p>
			</article>
			<article id="art2tiltle" class="container-fluid bg-transparent col-lg-5">
				<h2 >INSCRIPTION ADMINISTRATIVE</h2>
				<p>L’inscription administrative correspond à votre enregistrement en qualité d’étudiant pour une année universitaire. Elle se traduit par le paiement des droits d’inscription, la délivrance de la carte étudiante et des certificats de scolarité.</p>
				<h3>Contribution de Vie Etudiante et de Campus (CVEC)</h3>
				<p>Vous devez vous acquitter de la Contribution Vie Etudiante et de Campus (CVEC). Cette nouvelle contribution est destinée à l’accueil et à l’accompagnement social, sanitaire, culturel et sportif des étudiant(es). Elle sert également à conforter les actions de prévention et d’éducation à la santé.<br/>
				Elle doit être acquittée chaque année aurpès du CROUS. Montant : 90 €<br/>
				Vous êtes exonéré(e) de cette contribution si vous vous inscrivez en formation continue ou si vous êtes boursier(e), réfugié(e), bénéficiaire de la protection subsidiaire ou demandeur(se) d’asile bénéficiant du droit à se maintenir sur le territoire. </p>
				<h3>Inscription définitive</h3>
				<h5>Procédure ParcoursSup</h5>
				<p>Après avoir déposé vos vœux dans ParcourSup, vous avez été autorisé à vous inscrire. Pour une inscription définitive, suivre les étapes suivantes :</p>
				<p>1/ Télécharger votre attestation d'admission<br/>
				2/ S'acquitter de la CVEC<br/>
				3/ S'incrire en ligne<br/>
				4/ Scanner vos pièces justificatives et les déposer en ligne<br/>
				5/ Prendre un rendez-vous de validation<br/>
				6/ Valider votre inscription</p>
				<h5>Réinscription</h5>
				<p>La réinscription se fait en ligne. Pour ce faire suivre les étapes suivantes :</p>
				<p>1/ Inscription en ligne<br/>
				2/ Scan de vos pièces justificatives<br/>
				3/ Validation de votre inscription</p>
			</article>
		</div>

	</main>

	<script src="scripts/Common/jquery-3.3.1.min.js" ></script>
	<script src="scripts/Common/popper.min.js" ></script>
	<script src="scripts/Common/bootstrap.min.js" ></script>

</body>
</html>