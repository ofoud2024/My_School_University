<?php
	class VueBreadcrumb{

		/*
			Fonction permettant d'afficher un breadcrumb
			@param $active_element : Le nom de l'element active ("Page active")
			@param $hierachical Array(name=>link): Liste des elements de la hiérarchie, Il doivent être ordonnée par ordre croissant du profondeur

		*/
		public static function showBreadcrumb($active_element, $hierarchical = array()){
				echo '<nav aria-label="breadcrumb">';
				echo '<ol class="breadcrumb">';

				foreach($hierarchical as $name=>$link){
					echo "<li class='breadcrumb-item'><a href='${link}'>${name}</a></li>";
				}

	 			echo "<li class='breadcrumb-item active' aria-current='page'>${active_element}</li>";

	 			echo "</ol></nav>";


		}

	}
?>