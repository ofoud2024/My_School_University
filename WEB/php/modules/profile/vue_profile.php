<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../../common/vue_generique.php";

    class VueProfile extends VueGenerique
    {
        public function __construct()
        {
        }


        public function afficherProfile($profile, $token){
          $input_token = $this->inputToken($token);

          echo '
          <div class="container-fluid">
                <h3 class="text-center text-primary underline">Profil de l\'utilisateur : <span class="font-weight-bold">'.$profile["pseudo_utilisateur"].'</span></h3> 
                <form method="post" action="index.php?module=profile&action=changer_mot_de_passe">
                    '.$input_token.'
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="surname">Prénom</label>
                            <input type="text" class="form-control cursor-pointer" id="surname" value="'.$profile["prenom_utilisateur"].'" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="familyname">Nom</label>
                            <input type="text" class="form-control cursor-pointer" value="'.$profile["nom_utilisateur"].'" id="familyname" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mailing">Email</label>
                        <input type="email" class="form-control cursor-pointer" id="mailing" value="'.$profile["mail_utilisateur"].'" placeholder="toto@teamphp.fr" readonly>
                    </div>

                    <div class="form-group">
                        <label for="address">Addresse</label> 
                        <input type="text" class="form-control cursor-pointer" id="address" value="'.$profile["adresse_utilisateur"].'"  readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                          <label for="city">Ville</label>
                          <input type="text" class="form-control cursor-pointer" id="city" value="'.$profile["nom_ville"].'" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="zip">Code postal</label>
                            <input type="text" class="form-control cursor-pointer" id="zip" value="'.$profile["code_postal_ville"].'" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="country">Pays</label>
                            <input type="text" class="form-control cursor-pointer" id="country" value="'.$profile["nom_pays"].'" readonly/>
                        </div>             
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-6">
                          <label for="password">Mot de passe</label>
                          <input type="password" class="form-control" id="password" name="mot_de_passe" placeholder="Mot de passe">
                      </div>
                      <div class="form-group col-md-6">
                          <label for="password2">Confirmation</label>
                          <input type="password" class="form-control" id="password2" name="confirmation_mot_de_passe" placeholder="Confirmation de votre mot de passe">
                      </div>
                    </div>
                    <div class="row justify-content-around">
                        <button type="submit" class="mt-2 btn btn-success">Modifier</button>
                        <button type="reset" class="mt-2 btn btn-danger">Annuler</button>   
                    </div>   
                  </form>
            </div>

          ';
        }
    }
