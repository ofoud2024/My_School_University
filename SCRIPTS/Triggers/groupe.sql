
CREATE OR REPLACE FUNCTION verif_sous_groupe() RETURNS TRIGGER AS $$

BEGIN
    IF(est_un_sous_groupe(new.id_groupe_parent, NEW.id_groupe_fils)) THEN
        RAISE EXCEPTION 'SOUS GROUPE INVALIDE' USING HINT = 'Le groupe parent est déjà enfant du groupe fils';
    END IF;
    
    IF(NEW.id_groupe_fils = new.id_groupe_parent) THEN
        RAISE EXCEPTION 'SOUS GROUPE INVALIDE' USING HINT = 'Un groupe ne peut être un sous groupe de lui même';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE PLPGSQL;


CREATE TRIGGER verif_sous_groupe 
    BEFORE INSERT OR UPDATE ON sous_groupe
    FOR EACH ROW
    EXECUTE PROCEDURE verif_sous_groupe();