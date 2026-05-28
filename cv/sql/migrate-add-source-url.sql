-- Migration : ajout du champ source_url dans cv_applications
-- Executer une seule fois dans la base OVH

ALTER TABLE cv_applications ADD COLUMN source_url VARCHAR(500) DEFAULT NULL AFTER job_posting;
