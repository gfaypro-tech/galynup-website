-- =============================================================
-- CV Builder OBR — Seed base de connaissance Ollivier BRONSIN
-- À importer via phpMyAdmin (base OVH)
-- Généré le 2026-07-01
-- =============================================================

SET NAMES utf8mb4;

INSERT INTO obr_knowledge (type, title, content, meta_json, period_start, is_active) VALUES

-- ── PROFIL GÉNÉRAL ───────────────────────────────────────────
(
  'import',
  'Profil général',
  'Chef de Projet IT avec plus de 15 ans d''expérience dans la conduite de projets digitaux complexes en banque et finance. Habitué aux environnements Agile (Scrum) et hybrides (cycle en V), je maîtrise l''ensemble du cycle de vie projet : cadrage fonctionnel, pilotage, coordination transverse, mise en production. À l''aise dans des contextes techniques exigeants : API, microservices, cloud, CI/CD.',
  NULL,
  NULL,
  1
),

-- ── EXPÉRIENCES ──────────────────────────────────────────────
(
  'experience',
  'Chef de Projet IT — AFNOR',
  'Coordination de projets techniques sur plusieurs applications : migration de bases de données, décommissionnements applicatifs, migration d''OS.
Pilotage de projets de migration d''API SOAP vers REST.
Suivi de projets externes au forfait et mise aux normes RGAA des sites de l''AFNOR.
Relevé et suivi des KPI agiles des différentes équipes.

Environnement : SQL Server, Azure DevOps, Azure Insight, .NET',
  '{"company": "AFNOR", "role": "Chef de Projet IT", "period": "Mars 2026 – aujourd''hui"}',
  2026,
  1
),

(
  'experience',
  'Chef de Projet Digital — Crédit Agricole Leasing & Factoring',
  'Pilotage de projets de bout en bout : nouvelles offres commerciales, refonte du frontal partenaire (API REST, signature électronique), intégration de services pour la filiale espagnole.
Refonte complète de l''outil d''octroi automatique : coordination entre équipes métier, développeurs (Lisbonne, Inde) et intégrateurs externes.
Coordination d''équipes multiculturelles en méthode Agile (sprints, backlog, cérémonies) — France, Portugal, Inde.
Conduite de chantiers d''obsolescence, migrations techniques et renforcement de la sécurité applicative.
Garant du respect des délais, des budgets et de la qualité des livrables.

Environnement : API REST/SOAP, Kubernetes, Docker, GitLab, ArgoCD, Java, Oracle, SQL Server, Linux',
  '{"company": "Crédit Agricole Leasing & Factoring", "role": "Chef de Projet Digital", "period": "Juillet 2011 – Mars 2025"}',
  2011,
  1
),

(
  'experience',
  'Responsable Applicatif — Crédit Agricole L&F (prestataire)',
  'Maintenance et évolution du frontal dédié aux demandes de financement en Crédit-Bail Mobilier.
Gestion des livrables, des incidents et des tickets dans un contexte de production exigeant.

Environnement : API SOAP, PHP 5, Oracle, Linux',
  '{"company": "Crédit Agricole L&F", "role": "Responsable Applicatif", "period": "Novembre 2006 – Juillet 2011"}',
  2006,
  1
),

(
  'experience',
  'Chef de Projet Technique / Ingénieur d''études — Peugeot Citroën Automobiles SA',
  'Conception, déploiement et maintenance d''un outil de visualisation de la performance pour le réseau de concessionnaires.

Environnement : Java, Struts, Oracle, UML, MS Project, Maven',
  '{"company": "Peugeot Citroën Automobiles SA", "role": "Chef de Projet Technique / Ingénieur d''études", "period": "Février 2006 – Novembre 2006"}',
  2006,
  1
),

(
  'experience',
  'Ingénieur d''études — HSBC Épargne Entreprise',
  'Maintenance et évolution d''une application web d''épargne salariale pour les clients externes.
Recueil des besoins et modification d''une architecture technique multicouches (Java, WebSphere).',
  '{"company": "HSBC Épargne Entreprise", "role": "Ingénieur d''études", "period": "Juin 2004 – Janvier 2006"}',
  2004,
  1
),

(
  'experience',
  'Ingénieur d''études — Société Générale Asset Management',
  'Développement d''une application web interne de gestion de l''épargne salariale.
Maintenance du portail, du site internet et de l''Intranet SGAM.',
  '{"company": "Société Générale Asset Management", "role": "Ingénieur d''études", "period": "Mars 2000 – Juin 2003"}',
  2000,
  1
),

-- ── COMPÉTENCES ──────────────────────────────────────────────
(
  'competence',
  'Gestion de projet',
  'Cadrage fonctionnel et technique, pilotage, planification, gestion budgétaire, gestion des risques, reporting, animation de comités de pilotage. Pilotage de projets complexes multi-équipes en environnement exigeant (banque, industrie). Garant du respect des délais, des budgets et de la qualité des livrables.',
  NULL,
  NULL,
  1
),

(
  'competence',
  'Méthodologies : Agile / Scrum et Cycle en V',
  'Agile / Scrum : gestion du backlog, sprints, cérémonies (daily, sprint review, rétrospective). Mode hybride (Agile + Cycle en V). Coordination transverse entre équipes métier, développeurs et intégrateurs externes. Coordination d''équipes multiculturelles (France, Portugal, Inde).',
  NULL,
  NULL,
  1
),

(
  'competence',
  'Compétences techniques',
  'API REST / SOAP, microservices, Kubernetes, Docker, GitLab, ArgoCD, Azure DevOps, Azure Insight, CI/CD. Langages et frameworks : .NET, Java, Struts, PHP, Maven. Bases de données : Oracle, SQL Server. Systèmes : Linux. Mise aux normes RGAA. Sécurité applicative.',
  NULL,
  NULL,
  1
),

(
  'competence',
  'Savoir-être & Langues',
  'Management transversal, coordination d''équipes multiculturelles (France, Portugal, Inde), gestion des parties prenantes. Communication claire avec interlocuteurs métier et techniques. Langues : Français (natif), Anglais (B2 professionnel).',
  NULL,
  NULL,
  1
),

-- ── FORMATION ────────────────────────────────────────────────
(
  'formation',
  'MIAGE — Méthodes Informatiques Appliquées à la Gestion des Entreprises',
  'MIAGE — Université Paris-Sud, Orsay. Promotion 1998–1999. Méthodes Informatiques Appliquées à la Gestion des Entreprises.',
  NULL,
  1998,
  1
);
