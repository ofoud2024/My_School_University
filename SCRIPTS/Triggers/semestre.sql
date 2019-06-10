--Trigger periode semestre
CREATE OR REPLACE FUNCTION trig_modif_period_semestre() returns TRIGGER AS $$
BEGIN
	IF(new.date_debut >= new.date_fin) THEN
		RAISE EXCEPTION 'La date de début doit être inférieure à la date de fin ';
	END IF; 
	RETURN NEW;
END;
$$ LANGUAGE PLPGSQL;
CREATE TRIGGER modif_period_semestre 
	BEFORE UPDATE OR INSERT ON public.periode_semestre 
	FOR EACH ROW EXECUTE PROCEDURE trig_modif_period_semestre();
