DROP FUNCTION IF EXISTS connect_user(varchar, varchar);


CREATE OR REPLACE FUNCTION connect_user(pseudo varchar(45), mot_de_passe varchar(80)) returns integer as $$

DECLARE
    id integer;
BEGIN
    perform * from utilisateur where pseudo_utilisateur = pseudo;

    IF(NOT FOUND) THEN
        RAISE EXCEPTION 'L''utilisateur % est inéxistant', pseudo USING hint = 'Cet utilisateur est inéxistant';
    ELSE
        select id_utilisateur into id from utilisateur 
            where pseudo_utilisateur = pseudo and
            mot_de_passe_utilisateur = mot_de_passe;
        IF(NOT FOUND OR id IS NULL) THEN
            RAISE EXCEPTION 'Le mot de passe est incorrecte ' USING hint = 'Le mot de passe est incorrecte';
        END IF;
    END IF;

    RETURN id;

END;
$$ language plpgsql;
