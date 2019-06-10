<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../../common/classes/mail.php";
    require_once __DIR__ . "/../../common/cont_generique.php";

    class ModeleMail 
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }

        public function liste_mails_recus()
        {
            try {
                return Mail::liste_mails_recus();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }catch(NonConnecterException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE
                );
            }
        }

        public function liste_mails_envoyes()
        {
            try {
                return Mail::liste_mails_envoyes();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }catch(NonConnecterException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE
                );
            }
        }


        public function envoyer_mail( $sujet_mail, $message_mail, $utilisateurs, $groupes, $piece_jointe)
        {
            try {
                $liste_utilisateurs = explode(',', $utilisateurs);
                $liste_groupes = explode(',', $groupes);
                $lien_piece_jointe = "";
                $nom_piece_jointe = "";

                if($piece_jointe !== null){
                    if($piece_jointe->copyFile()){
                        $lien_piece_jointe = $piece_jointe->getFullPath();
                        $nom_piece_jointe = $piece_jointe->getClientFileName();
                    }
                }

                if(empty($liste_utilisateurs) && empty($liste_groupes)){
                    $this->cont->pasAssezDeParametres('destinataire');
                }
            
                Mail::envoyer_mail($sujet_mail, $message_mail, $lien_piece_jointe, $nom_piece_jointe, $liste_utilisateurs, $liste_groupes);

            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    MAIL_SEND_ERROR_TITLE,
                    MAIL_SEND_ERROR_MESSAGE
                );
            }catch(NonConnecterException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE
                );
            }
        }   



        public function supprimer_mail($id)
        {
            try {
                $Mail = new Mail($id);
                $Mail->supprimerMail();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    MAIL_DELETE_ERROR_TITLE,
                    MAIL_DELETE_ERROR_MESSAGE
                );
            }catch(NonConnecterException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"supprimer ce mail")
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le mail', 'id'=>$id)
                );
            }
        }

        public function cacher_mail($id_mail){
            try {
                $mail = new Mail($id_mail);
                $mail->cacherMail();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    MAIL_DELETE_ERROR_TITLE,
                    MAIL_DELETE_ERROR_MESSAGE
                );
            }catch(NonConnecterException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le mail', 'id'=>$id_mail)
                );
            }
        }
    }
