<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/vue_generique.php";

    class VueUtilisateur extends VueGenerique
    {
        public function __construct()
        {
        }


        /****************************************************************************************************/
        /*******************************************UTILISATEURS*********************************************/
        /****************************************************************************************************/



        public function afficherInsertionUtilisateurs($liste_droits, $token)
        {
            require_once __DIR__ . "/html/utilisateur/ajouter_utilisateur_1.php";

            $this->afficherListeDroits($liste_droits);

            echo $this->inputToken($token);

            require_once __DIR__ . "/html/utilisateur/ajouter_utilisateur_2.php";
        }


        public function afficherListeUtilisateurs($liste_utilisateurs)
        {

            try{
              Utilisateur::possedeDroit('droit_creation_utilisateurs');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"afficher la liste des utilisateurs")
                );                        
            } 

            echo '
            <h2 class="text-center text-dark underline mb-4 pt-2 underline">
              Liste des utilisateurs
            </h2>
          ';
          
            $this->afficherTableau(
              $liste_utilisateurs,
              array('id', 'nom', 'prenom', 'tel', 'mail', 'date_naissance'),
              'index.php?module=administration&action=modification&type=utilisateur&id=',
              'id',
              array('#', 'nom', 'prénom', 'n° tel', 'mail', 'date de naissance')
            );
            
            echo '
            <div class="container-fluid row justify-content-center mx-0 mb-2">
              <div class="col-md-8 row justify-content-center">
                  <a href="index.php?module=administration&type=utilisateur&action=afficherCreationUtilisateur">
                    <button class="btn btn-outline-success">Ajouter</button>
                  </a>
              </div>
            </div>
            ';
        }


        public function afficherUtilisateur($utilisateur, $liste_droits, $token)
        {
            try{
              Utilisateur::possedeDroit('droit_creation_utilisateurs');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"afficher les détails d'un utilisateur")
                );                        
            } 

            if ($utilisateur) {

                $utilisateur['genre'] = $utilisateur['genre'] ? 1 : 0;
                $inputToken = $this->inputToken($token);

                
                echo "
                <h2
                class='text-center text-dark underline mb-4 pt-2 '
                style='text-decoration:underline'
              >
                Profil de ${utilisateur['pseudo_utilisateur']}
              </h2>
              
              <form class='pb-2' autocomplete='off' method='post' action='index.php?module=administration&type=utilisateur&action=modification_utilisateur&id=${utilisateur['id_utilisateur']}'>
                <div class='form-row'>
                  <div class='form-group col-md-4'>
                    <label for='nom'>Nom</label>
                    <input
                      type='text'
                      class='form-control'
                      id='nom'
                      name='nom'
                      placeholder='Toto'
                      value='${utilisateur['nom_utilisateur']}'
                      required
                    />
                    ${inputToken}
                  </div>
                  <div class='form-group col-md-4'>
                    <label for='prenom'>Prenom</label>
                    <input
                      type='text'
                      class='form-control'
                      id='prenom'
                      name='prenom'
                      placeholder='Titi'
                      value='${utilisateur['prenom_utilisateur']}'
                      required
                    />
                  </div>
                  <div class='form-group col-md-4 '>
                    <label for='date_naissance'>Date de naissance</label>
                    <input
                      type='date'
                      class='form-control'
                      id='date_naissance'
                      name='date_naissance'
                      placeholder='31/01/2018'
                      value='${utilisateur['date_naissance_utilisateur']}'
                      required
                    />
                  </div>
                </div>
              
                <div class='form-row'>
                  <div class='form-group col-md-6'>
                    <label for='email'>Email</label>
                    <input
                      type='email'
                      class='form-control'
                      id='email'
                      name='email'
                      placeholder='email@domain.com'
                      value='${utilisateur['mail_utilisateur']}'
                      required
                    />
                  </div>
                  <div class='form-group col-md-6'>
                    <label for='mot_de_passe'>Mot de Passe</label>
                    <input
                      type='password'
                      class='form-control'
                      id='mot_de_passe'
                      name='mot_de_passe'
                      placeholder='*********'
                    />
                  </div>
                </div>
              
                <div class='form-row'>
                  <div class='form-group col-md-4'>
                    <label for='civilite'>civilite</label>
                    <select 
                        id='civilite' 
                        name='est_homme' 
                        class='form-control' 
                        value='${utilisateur['genre']}'
                        required>
                      <option value='true' ".($utilisateur['genre'] ? "selected" : '').">Monsieur</option>
                      <option value='false' ".(!$utilisateur['genre'] ? "selected" : '').">Madame</option>
                    </select>
                  </div>
                  <div class='form-group col-md-8'>
                    <label for='addresse'>Address</label>
                    <input
                      type='text'
                      class='form-control'
                      id='addresse'
                      name='addresse'
                      value='${utilisateur['adresse_utilisateur']}'
                      placeholder='120 Rue de la nouvelle France'
                      required
                    />
                  </div>
                </div>
              
                <div class='form-row'>
                  <div class='form-group col-md-4'>
                    <label for='ville'>Ville</label>
                    <input 
                        placeholder='nom ville'
                        type='text' 
                        class='form-control' 
                        id='ville' 
                        value='${utilisateur['nom_ville']}'
                    />
                  </div>
                  <div class='form-group col-md-4'>
                    <label for='code_postal'>Code postal</label>
                    <input
                      type='text'
                      placeholder='code postal'
                      class='form-control'
                      id='code_postal'
                      name='code_postal'
                      value='${utilisateur['code_postal_ville']}'
                      required
                    />
                  </div>
                  <div class='form-group col-md-4'>
                    <label for='pays_naissance' >Pays naissance</label>
                    <input  id='pays_naissance' 
                            placeholder='pays de naissance'
                            name='pays_naissance'
                            class='form-control' 
                            value='${utilisateur['nom_pays']}'
                            required
                    >
                  </div>
                </div>
              
                <div class='form-row'>
                  <div class='form-group col-md-6'>
                    <label for='tel'>Numéro de telephone</label>
                    <input
                      type='tel'
                      maxlength='10'
                      class='form-control'
                      id='tel'
                      name='tel'
                      placeholder='0610203040'
                      value='${utilisateur['tel_utilisateur']}'
                      required
                    />
                  </div>
              
                  <div class='form-group col-md-6 '>
                    <label for='droits'>Droits</label>
                ";

                $this->afficherListeDroits($liste_droits, $utilisateur['nom_droits']);

                echo '
                </div>
                  </div>
                
                  <div class="container-fluid row justify-content-around">
                    <button type="submit" name="modifier" class="btn btn-outline-primary ">Modifier</button>
                    <button type="submit" name="supprimer" class="btn btn-outline-danger ">Supprimer</button>
                  </div>
                
                </form>';
            }
        }



        /****************************************************************************************************/
        /*******************************************PERSONNELS*********************************************/
        /****************************************************************************************************/
   
   
        public function afficherListePersonnels($liste_personnels, $token)
        {
            try{
              Utilisateur::possedeDroit('droit_creation_utilisateurs');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"afficher la liste du personnels")
                );                        
            } 

            $inputToken = $this->inputToken($token);

            $this->afficherTableau(
              $liste_personnels,
              array('id', 'nom', 'prenom', 'num_enseignant', 'heures_travail'),
              'index.php?module=administration&action=afficher_modification_personnel&type=personnel&id=',
              'id',
              array('#', 'nom', 'prenom', 'est enseignant', 'heures travail')
            );

            echo '
              <form action="index.php?module=administration&type=utilisateur&action=ajouter_personnel" method="post">
                <fieldset class="container-fluid mx-0 row justify-content-center  mb-1">

                  <legend class="col-auto px-0" align="center">Ajouter un personnel</legend>

                  <div class=" container-fluid row justify-content-center mx-0 px-0 mb-2">
                    <div class="form-inline container-fluid px-0">

                      <div class="form-group col-md-4 col-6">
                        <label for="pseudo" class="sr-only">Pseudo</label>
                        <input type="text" class="form-control"  id="pseudo" name="pseudo" placeholder="Pseudo utilisateur"
                          required />
                        '.$inputToken.'
                      </div>

                      <div class="form-check col-md-6 col-6 justify-content-center">
                        <input class="form-check-input" type="checkbox" name="estEnseignant" id="estEnseignant" />
                        <label class="form-check-label" for="estEnseignant">
                          Enseignant
                        </label>
                      </div>

                      <div class="col-md-2 col-12 row justify-content-md-end justify-content-center mt-2 mt-md-0">
                        <button class="btn btn-outline-success" type="submit">Ajouter</button>
                      </div>

                    </div>
                  </div>
                </fieldset>

              </form>
            ';
          }

        public function modifierPersonnel($personnel, $token)
        { 
            try{
              Utilisateur::possedeDroit('droit_creation_utilisateurs');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"afficher les détails du personnel")
                );                        
            } 

            $inputToken = $this->inputToken($token);

            $heures_travail_courants = includesAt($personnel['heures_travail'], 'annee', 'heures_travail', $personnel['annee_courante'], 0);
            $est_enseignant = $personnel['id_enseignant'] === null ? '' : 'checked' ;
            
            echo '
            <h2 class="text-center text-dark underline mb-4 pt-2 " style="text-decoration:underline">
                Modifier '.$personnel["pseudo_utilisateur"] . '
            </h2>
    
            <form class="pb-2" method="post" action="index.php?module=administration&type=utilisateur&action=modifier_personnel&id='.$personnel['id_personnel'].'">
            
                <div class="justify-content-center small-table row container-fluid">
                    <table class="table table-striped text-center table-hover table-bordered col-md-6 col-lg-5">
                        <thead class="thead-dark ">
                            <tr>
                                <th>Année</th>
                                <th>Heures travaillée</th>
                            </tr>
                        </thead>
            
                        <tbody>
                            <tr>
                                <td class="align-middle">'.$personnel['annee_courante'].'</td>
                                <td>
                                    <input type="number" min="0" value="' .$heures_travail_courants . '" class="form-control table-input" name="heures_travail" />  
                                    '.$inputToken.'
                                </td>
                            </tr>';



            foreach ($personnel['heures_travail'] as $heures_travail) {
                if ($heures_travail['annee'] != $personnel['annee_courante']) {
                    echo "<tr>
                            <td>${heures_travail['annee']}</td>
                            <td>${heures_travail['heures_travail']}</td>
                          </tr>";
                }
            }


                          
            echo
            '</tbody>
                  </table>
              </div>
          
              <div class="container-fluid row justify-content-center">
                  <div class="pt-2 col-auto">
                      <input type="checkbox" name="estEnseignant" '. $est_enseignant .' id="estEnseignant" />
                      <label for="estEnseignant">Est Enseignant</label>
                  </div>
              </div>
          
          
              <div class="container-fluid row justify-content-sm-center justify-content-around">
                  <button name="modification" class="btn btn-outline-success mr-sm-5">Valider</button>
                  <button name="suppression" class="btn btn-outline-danger ml-sm-5 ">Supprimer</button>
              </div>
          
          </form>';
        }
    }
