<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/../../common/classes/mail.php";

    class ModeleMailAPI 
    {   
        public function detailsMail($id_mail)
        {

            try {
                $mail = new Mail($id_mail);
                
                $resultat = array(
                    "mail"=>$mail->detailsMail(),
                    "utilisateurs"=>$mail->getUtilisateursDestinataire(),
                    "groupes"=>$mail->getGroupesDestinataires()
                );
                
                Response::sendHttpBodyAndExit($resultat);
            } catch (PDOException $e) {
                ErrorHandlerAPI::afficherErreur($e);
            }catch(NonConnecterException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE,
                    array(),
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le mail', 'id'=>$id_mail),
                    HTTP_NOT_FOUND
                );
            }
        }

        public function reponses_mail($id_mail){
            try {
                $mail = new Mail($id_mail);
                                
                Response::sendHttpBodyAndExit($mail->getReponsesMail());

            } catch (PDOException $e) {
                ErrorHandlerAPI::afficherErreur($e);
            }catch(NonConnecterException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE,
                    array(),
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le mail', 'id'=>$id_mail),
                    HTTP_NOT_FOUND
                );
            }
        }

        public function ajouterReponse($id_mail, $message_reponse){
            try {
                $mail = new Mail($id_mail);
                $mail->ajouterReponse($message_reponse);
                Response::sendHttpBodyAndExit();
            } catch (PDOException $e) {
                ErrorHandlerAPI::afficherErreur($e);
            }catch(NonConnecterException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE,
                    array(),
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le mail', 'id'=>$id_mail),
                    HTTP_NOT_FOUND
                );
            }
        }

        public function telecharger_mail($id_mail){
            try {
                $mail = new Mail($id_mail);
                
                $lien_piece_jointe = $mail->detailsMail()["pieces_jointe_mail"];
                $nom_piece_jointe  = $mail->detailsMail()["nom_piece_jointe"];

                $fichier = new Fichier($lien_piece_jointe);

                $fichier->telecharger($nom_piece_jointe);
            } catch (PDOException $e) {
                ErrorHandlerAPI::afficherErreur($e);
            }catch(NonConnecterException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE,
                    array(),
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le mail', 'id'=>$id_mail),
                    HTTP_NOT_FOUND
                );
            }
        }
    }
?>