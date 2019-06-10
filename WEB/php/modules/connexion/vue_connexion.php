<?php
    require_once __DIR__ . "/../../verify.php";
    
    class VueConnexion
    {
        public function __construct()
        {
        }

        public function afficherConnexion($error)
        {
            
            echo '
        <div class="container-fluid row justify-content-center connexion ">
            <div class="connexion-form-container col-auto container align-middle row justify-content-center">
              <div class="container px-0 connexion-form col-auto ">
          
                <div class="col-12 row mx-0 justify-content-center mb-4">
                  <img src="images/connexion/logo.png" class=" logo-form" alt="logo site web" />
                </div>';

            if($error){
                echo '
                <div class="alert alert-danger mx-3" role="alert">
                    '. $error . '
                </div>';
            }
            echo '      
                <form action="index.php?module=connexion&action=seConnecter" class="container-fluid px-0" method="post">
                  <div class="row justify-content-center mb-4 mx-0 container-fluid">
                    <div class="input-group col-auto row justify-content-center">
                      <div class="input-group-prepend">
                        <img src="images/connexion/username.png" class="input-group-text input-image-prepend" />
                      </div>
                      <input type="text" name="username" class="form-control data-input" />
                    </div>
                  </div>
          
                  <div class="row justify-content-center mb-4 mx-0 container-fluid">
                    <div class="input-group col-auto row justify-content-center">
                      <div class="input-group-prepend">
                        <img src="images/connexion/password.png" class="input-group-text input-image-prepend" />
                      </div>
                      <input type="password" name="password" class="form-control data-input" />
                    </div>
                  </div>
          
                  <div class="row justify-content-center text-white mb-4 mx-0 container-fluid">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="resterConnecter" id="resterConnecter" />
                      <label class="form-check-label" for="resterConnecter">
                        Rester connecté
                      </label>
                    </div>
                  </div>
          
                  <div class="row justify-content-center text-white mb-4 mx-0 container-fluid">
                    <button type="submit" class="btn btn-success">Se connecter</button>
                  </div>
                </form>
              </div>
            </div>
          </div>';
        }
    }
