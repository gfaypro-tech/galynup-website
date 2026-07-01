-- Executer une seule fois dans phpMyAdmin (OVH)
ALTER TABLE cv_knowledge ADD COLUMN period_start SMALLINT DEFAULT NULL AFTER meta_json;

-- Peupler la colonne pour les entrees existantes dont meta_json contient une annee
UPDATE cv_knowledge
SET period_start = CAST(
    REGEXP_SUBSTR(JSON_UNQUOTE(JSON_EXTRACT(meta_json, '$.period')), '[0-9]{4}') AS UNSIGNED
)
WHERE type = 'experience'
  AND meta_json IS NOT NULL
  AND JSON_UNQUOTE(JSON_EXTRACT(meta_json, '$.period')) REGEXP '[0-9]{4}';
