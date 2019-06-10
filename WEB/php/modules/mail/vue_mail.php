<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../../common/vue_generique.php";

    class VueMail extends VueGenerique
    {
        public function __construct()
        {
            require_once __DIR__ . "/html/nav_mail.html";
        }

        public function afficher_liste_mails($liste_mails, $sont_recu = true)
        {

            $action = $sont_recu ? "cacher_mail" : "supprimer_mail";

            $titres_mail = "";

            if(count($liste_mails) == 0){
                $text = $sont_recu ? "reçu" : "envoyé";
                $titres_mail = "
                    <div class='empty-message'>
                        <p class='text-center text-secondary'>Vous n'avez ${text} aucun mail </p> 
                    </div>
                ";
            }

            foreach($liste_mails as $mail_recu){
                setlocale(LC_TIME, "fr_FR.utf8");

                $date_envoi = ucfirst(strftime('%d %b', strtotime($mail_recu['date_envoi_mail'])));

                $titres_mail .= '
                <div>
            
                    <div class="mail-title-container" mail-id="'.$mail_recu["id_mail"].'">
                        <div class=" mail-check-container">
                            <a href="index.php?module=mail&action='.$action.'&id_mail='.$mail_recu["id_mail"].'">
                                <button class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                            </a>
                        </div>
            
                        <div class="  mail-title ">
                            <p class="mail-sender m-0"></p>
                            
                            <div class="mail-date-name-container">
                                <p class="mail-sender m-0">'.$mail_recu["prenom_utilisateur"] . " " . $mail_recu["nom_utilisateur"] .'</p>
                                <p class="text-primary text-left mail-date-value" >
                                    '.$date_envoi.'
                                </p>
                            </div>
                            <p class="mail-title-text m-1 ">'.$mail_recu["sujet_mail"].'</p>
                        </div>

                    </div>
            
                </div>
            
                ';
            }

            echo '


            <div class="show-mail-container ">

                <div class="mail_title_list">
                    '.$titres_mail.'
                </div>


            <div class="mail-details-container bg-light" id="mail-content-container">
                <div class="select-message-container">
                    <h1 class="mail-select-icon"><i class="fas fa-envelope"></i></h1>
                    <p class="text-secondary select-mail-text">Sélectionnez le mail à lire</p>
                </div>
            </div>

        </div>
            ';

        }


        public function afficher_envoyer_mail()
        {

            echo '<form enctype="multipart/form-data" class="border rounded shadow border-dark m-3 p-3" method="POST" action="index.php?module=mail&action=envoyer_mail">
            
                <div class="form-group row mt-2 ">
                    <label for="utilisateurs_destinataire" class="col-sm-2 col-form-label">Destinataires</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="utilisateurs_destinataire" id="utilisateurs_destinataire" placeholder="utilisateurs">
                    </div>
                </div>
                <div class="form-group row mt-2 ">
                  <label for="groupes_destinataire" class="col-sm-2 col-form-label">Groupes</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="groupes_destinataire" name="groupes_destinataire" placeholder="groupes">
                  </div>
                </div>


                <div class="form-group row mt-2">
                  <label for="sujet_mail" class="col-sm-2 col-form-label">Objet</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="sujet_mail" name="sujet_mail" placeholder="Objet" required>
                  </div>
                </div>

                <div class="row  mt-2 px-3 mt-2">
                    <label for="message_mail" class="font-weight-bold">Contenu de votre message</label>
                    <textarea class="form-control mail-content-area" id="message_mail" name="message_mail" ></textarea>
                </div>
                <div class="py-3 ">
                    <div class="form-group">
                        <label for="piece_jointe" class="font-weight-bold">Ajouter une pièce-jointe</label>
                        <input type="file" class="form-control-file" name="piece_jointe" id="piece_jointe">
                    </div>
                    <div class="row justify-content-center container-fluid mt-3 ">
                        <button type="submit" class="btn btn-success ">Envoyer</button>
                    </div>
                </div>
            </form>
        </section>
';

        }
    }

