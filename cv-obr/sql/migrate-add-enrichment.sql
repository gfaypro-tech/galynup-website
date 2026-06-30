-- Migration : enrichissement base de connaissance et candidatures
-- Executer une seule fois dans la base OVH

ALTER TABLE cv_knowledge ADD COLUMN keywords TEXT DEFAULT NULL;
ALTER TABLE cv_applications ADD COLUMN enrichment_data TEXT DEFAULT NULL;
