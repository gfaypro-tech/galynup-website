-- Executer une seule fois dans phpMyAdmin (OVH)
ALTER TABLE cv_applications ADD COLUMN letter_content TEXT DEFAULT NULL AFTER cv_content;
