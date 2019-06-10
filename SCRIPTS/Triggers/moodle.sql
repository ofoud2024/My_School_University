CREATE OR REPLACE FUNCTION support_enseignant_responsable() returns trigger as $$
DECLARE

BEGIN
    perform * from module_enseigne_par 
        where id_enseignant = new.id_enseignant
        and ref_module = new.ref_module 
        and est_responsable;
    
    IF(NOT FOUND) THEN
        RAISE EXCEPTION 'Dépôt refusé' USING HINT = 'Vous devez être responsable du module pour y déposer des cours'; 
    END IF;
    RETURN  NEW;
END;
$$ LANGUAGE PLPGSQL;



CREATE TRIGGER trig_verif_depots 
    BEFORE UPDATE OR INSERT ON support_pedagogique
    FOR EACH ROW
    EXECUTE PROCEDURE support_enseignant_responsable();


