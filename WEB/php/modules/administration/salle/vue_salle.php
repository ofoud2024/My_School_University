<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/vue_generique.php";

    class VueSalle extends VueGenerique
    {
        public function __construct()
        {
        }

        public function afficher_salles($liste_salles, $token)
        {
            try{
                Utilisateur::possedeDroit('droit_creation_cours');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajouter une salle")
                );                        
            } 
            $input_token = $this->inputToken($token);
            
            echo '<h2 class="text-center text-dark underline mb-4 pt-2 underline">
                     Gestion des salles
                </h2>';
            
            $this->afficherTableau(
                $liste_salles,
                array('nom_salle', 'nombre_ordinateurs_salle', 'nombre_places_salle', 'contient_projecteur_salle'),
                'index.php?module=administration&type=salle&action=afficher_salle&nom_salle=',
                'nom_salle',
                array('Nom de la salle', 'Nombre de pc', 'Nombre de places', 'Contient un projecteur')
            );

            echo '
              <form autocomplete="off" method="post" action="index.php?module=administration&type=salle&action=ajouter_salle">
                '.$input_token.'
                <h4 class=" text-center mt-3 mb-1 underline">Ajout d\'une salle</h4>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="nom_salle">Nom de la salle</label>
                    <input type="text" class="form-control" id="nom_salle" name="nom_salle" placeholder="B0-01" required />
                  </div>
                  <div class="form-group col-md-4">
                    <label for="nombre_places_salle">Nombre de places</label>
                    <input type="number" class="form-control" id="nombre_places_salle" name="nombre_places_salle" placeholder="93"
                      required />
                  </div>
                  <div class="form-group col-md-4">
                    <label for="nombre_ordinateurs_salle">Nombre d\'ordinateurs</label>
                    <input type="number" class="form-control" id="nombre_ordinateurs_salle" name="nombre_ordinateurs_salle"
                      placeholder="93" required />
                  </div>

                  <div class="justify-content-md-center row  container-fluid form-group  ">
                    <div class="form-check col-auto justify-content-md-center">
                      <input class="form-check-input" type="checkbox" value="" id="contient_projecteur" name="contient_projecteur">
                      <label class="form-check-label" for="contient_projecteur">
                        Contient un projecteur
                      </label>
                    </div>
                  </div>


                </div>

                <div class="container-fluid row justify-content-center">
                  <button type="submit" class="btn btn-success mb-2">Ajouter</button>
                </div>
              </form>
            ';
        }

        public function afficher_salle($detailsSalle,$nom_salle, $token)
        {
            try{
              Utilisateur::possedeDroit('droit_creation_cours');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"afficher les détails d'une salle")
                );                        
            } 

            echo "<h2 class='text-center underline'> Modification du semestre $nom_salle</h2>";
            
            $nombre_places_salle = $detailsSalle['nombre_places_salle'];
            $nombre_ordinateurs_salle = $detailsSalle['nombre_ordinateurs_salle'];
            $input_token = $this->inputToken($token);


            echo "
                <form
                autocomplete='off'
                method='post'
                action='index.php?module=administration&type=salle&action=modifier_salle&nom_salle=$nom_salle'
                >
                ${input_token}
                <div class='form-row'>
                <div class='form-group col-md-4'>
                  <label for='nom_salle'>Nom de la salle</label>
                  <input
                    type='text'
                    class='form-control'
                    id='nom_salle'
                    name='nom_salle'
                    value='$nom_salle'
                    required
                  />
                </div>
                <div class='form-group col-md-4'>
                  <label for='nombre_places_salle'>Nombre de places</label>
                  <input
                    type='number'
                    class='form-control'
                    id='nombre_places_salle'
                    name='nombre_places_salle'
                    value='$nombre_places_salle'
                    required
                  />
                </div>
                <div class='form-group col-md-4'>
                  <label for='nombre_ordinateurs_salle'>Nombre d'ordinateurs</label>
                  <input
                    type='number'
                    class='form-control'
                    id='nombre_ordinateurs_salle'
                    name='nombre_ordinateurs_salle'
                    value='$nombre_ordinateurs_salle'
                    required
                  />
                </div>";
            if($detailsSalle['contient_projecteur_salle']){
                echo "
                <div class='justify-content-md-center row  container-fluid form-group'>
                  <div class='form-check col-auto justify-content-md-center'>
                    <input class='form-check-input' type='checkbox' checked id='contient_projecteur' name='contient_projecteur'>
                    <label class='form-check-label' for='contient_projecteur'>
                      Contient un projecteur
                    </label>
                  </div>
                </div>
              </div>";
            }
            else{
                echo "
                <div class='justify-content-md-center row  container-fluid form-group'>
                  <div class='form-check col-auto justify-content-md-center'>
                    <input class='form-check-input' type='checkbox' id='contient_projecteur' name='contient_projecteur'>
                    <label class='form-check-label' for='contient_projecteur'>
                      Contient un projecteur
                    </label>
                  </div>
                </div>
              </div>";
            }
            echo"
              <div class='container-fluid row justify-content-around'>
                <button type='submit' class='btn btn-success mb-2' name='modifier'>Modifier</button>
                <button type='submit' class='btn btn-danger mb-2' name='supprimer'>Supprimer</button>

              </div>
              
              </form>";
        }
    }
