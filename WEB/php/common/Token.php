<?php

require_once __DIR__ . "/../verify.php";
require_once __DIR__ . "/Database.php";

class Token extends Database{
    private static $create_token_query   = "insert into token values (:id_utilisateur, :token, now() + Interval '00:20:00')";
    private static $validate_token_query = "select * from token 
                                            where id_utilisateur = :id_utilisateur
                                            and token = :token and expiration > now()";
    private static $delete_token_query   = "delete from token 
                                            where 
                                            (id_utilisateur = :id_utilisateur
                                            and token = :token)
                                            or expiration < now()
                                            ";


    /*
        - Crée un token
        - @return String token : Le token crée
    */
    public static function createToken(){
        $token = false;

        if(Utilisateur::estConnecte()){
            $token = uniqid('etablissement_');
            $stmt = self::$db->prepare(self::$create_token_query);
            $stmt->bindValue(':id_utilisateur', Utilisateur::getUtilisateurCourant()->getIdUtilisateur());
            $stmt->bindValue(':token', $token);
            
            try{
                $stmt->execute();
            }catch(PDOException $e){
                $token = false;
            }

        }

        return $token;

    } 


    /*
        - Valide un token 
        - @param token : Le token à valider
        - @return boolean : Vrai si le token est valider, faux sinon.
    */
    public static function validateToken($token){
        $est_valide = false;

        if(Utilisateur::estConnecte()){
            $stmt = self::$db->prepare(self::$validate_token_query);
            $stmtDel = self::$db->prepare(self::$delete_token_query);

            $stmt->bindValue(':id_utilisateur', Utilisateur::getUtilisateurCourant()->getIdUtilisateur());
            $stmt->bindValue(':token', $token);

            $stmtDel->bindValue(':id_utilisateur', Utilisateur::getUtilisateurCourant()->getIdUtilisateur());
            $stmtDel->bindValue(':token', $token);

            try{
                $stmt->execute();
                if(false !== $stmt->fetch()){
                    $est_valide = true;
                }
                $stmtDel->execute();

            }catch(PDOException $e){}
        }

        return $est_valide;
    }

}

?>