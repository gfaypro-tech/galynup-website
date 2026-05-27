<?php
/**
 * Seed de la base de connaissance — Gaëlle FAY
 * Usage : php php/seed-knowledge.php
 * SUPPRIMER ce fichier après exécution.
 */

// Lancement depuis la racine /cv/ ou depuis /cv/php/
$root = __DIR__ . '/..';
require_once $root . '/config.php';
require_once $root . '/includes/db.php';

$db = getDB();

// Vider les entrées existantes avant le seed
$db->exec("DELETE FROM cv_knowledge");
echo "Base vidée.\n\n";

$stmt = $db->prepare("INSERT INTO cv_knowledge (type, title, content, meta_json) VALUES (?, ?, ?, ?)");

function insert(PDO $db, $stmt, string $type, string $title, string $content, ?array $meta = null): void {
    $metaJson = $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null;
    $stmt->execute([$type, $title, trim($content), $metaJson]);
    echo "✓ $title\n";
}

// ═══════════════════════════════════════════════════════════════
// 1. PROFIL SYNTHÉTIQUE
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'import', 'Profil synthétique — Gaëlle FAY', <<<'EOT'
Gaëlle FAY — Directrice des Systèmes d'Information | Manager de Transition IT | CIO Advisory
Email : gaelle.fay@galynup.fr | LinkedIn : linkedin.com/in/gaellefay | Paris et périphérie

RÉSUMÉ :
25 ans d'expérience en transformation digitale et pilotage de programmes stratégiques complexes (150+ collaborateurs, budgets jusqu'à 20M€) dans les secteurs banque, assurance, services publics et conseil.

APPROCHE :
Aligner la DSI sur les enjeux business en optimisant sa chaîne de valeur de bout en bout — gouvernance IT, architecture d'entreprise, services ITSM et intégration de l'Intelligence Artificielle — au service de la performance opérationnelle et de la création de valeur métier.

POSITIONNEMENT :
Intervient aussi bien en direction pérenne qu'en mission de transition pour structurer, redresser ou accélérer une DSI.

SIGNATURE PROFESSIONNELLE :
"Je mets de l'ordre dans les organisations qui ont grandi trop vite. Je fluidifie la circulation de l'information pour générer de la connaissance capitalisable et partageable."

SECTEURS : Banque · Assurance · Finance · Services Publics · Secteur Public · Conseil

CIBLES DE MISSION :
• Missions C-Level DSI / Gouvernance SI (TJ > 1000€)
• Poste DSI ou Direction SI (CDI, > 100K€/an)
• Directrice MOA dans un SI
• Ateliers IA et formation gestion de projet

LANGUES : Français (langue maternelle) · Anglais C1 courant · Espagnol débutant
EOT);


// ═══════════════════════════════════════════════════════════════
// 2. COMPÉTENCES CLÉS & CERTIFICATIONS
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'competence', 'Compétences clés & certifications', <<<'EOT'
COMPÉTENCES CLÉS :
• Transformation digitale & gouvernance IT
• Architecture d'entreprise (TOGAF 10)
• Intelligence Artificielle & technologies émergentes
• Gestion de portefeuille projets & programmes
• Encadrement d'équipes pluridisciplinaires (jusqu'à 150 personnes)
• Management d'équipe (jusqu'à 14 collaborateurs directs)
• Gestion budgétaire (jusqu'à 20M€/an)
• Pilotage en environnement matriciel & international
• Gouvernance informatique
• Scrum Master
• Gestion de la transition
• Conduite du changement
• Gestion des risques & conformité (RGPD, KYC, ESG)
• MOA / Maîtrise d'Ouvrage
• BPMN — Modélisation de processus
• Pilotage de RFP / Appels d'offres
• Pilotage de domaines de services numériques
• Feuilles de route, schémas directeurs SI & portefeuilles projets
• Amélioration continue, démarche qualité & ITSM/ITIL
• Infrastructure Cloud, IA & veille technologique

CERTIFICATIONS PROFESSIONNELLES :
• PMP — Project Management Professional (PMI)
• PMI-ACP — Agile Certified Practitioner (PMI)
• TOGAF 10 Foundation (The Open Group)
• ITIL 4 Foundation (Axelos)
• Stratégie@HEC — HEC Paris (Mars 2024 – Mars 2025)
• Agile Scrum Foundation (ASF) — EXIN
EOT);


// ═══════════════════════════════════════════════════════════════
// 3. FORMATION
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'formation', 'Formation académique', <<<'EOT'
Master 2 — Management des Systèmes d'Information et de la Connaissance
IAE Paris — Sorbonne Business School | 2007–2008
Formation pluridisciplinaire intégrant stratégie des SI, architecture d'entreprise, transformation numérique.

Maîtrise — Économie et Gestion, Mention Gestion d'entreprise
Université de Marne-la-Vallée | 1996–2000
EOT);


// ═══════════════════════════════════════════════════════════════
// 4. RAYONNEMENT PROFESSIONNEL
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'import', 'Rayonnement professionnel & publications', <<<'EOT'
PUBLICATION :
Co-auteure : "L'IA au service de la gestion de projet — transformons nos pratiques"
AFNOR Éditions, 2025.
Ouvrage de référence pour les chefs de projet et DSI intégrant l'IA générative dans leurs pratiques.

FORMATION :
Formatrice CEGOS — modules Gestion de projet & IA au service de la gestion de projet.

RÉDACTION :
Rédactrice — Blog Gestion de Projet (BGDP)
Articles de fond, études de cas et analyses sur la gestion de projet et les cas d'usage IA.

CONTENU DIGITAL :
Créatrice de contenu YouTube — Chaîne MindGaëlle
Vulgarisation des usages IA pour chefs de projet. Décryptages, retours d'expérience, méthodes concrètes.
Live Café IA — Partenariat Blog Gestion De Projet.

BÉNÉVOLAT :
Membre PMI France et Cheffe de projet PMI France.
EOT);


// ═══════════════════════════════════════════════════════════════
// 5. AFNOR — CIO Office (2025)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Responsable CIO Office — AFNOR Groupe (2025)', <<<'EOT'
MISSION : Responsable de Département Gouvernance, Architecture et Flux DSI (CIO Office)
ORGANISATION : AFNOR Groupe | Secteur public | Plaine Stade De France
PÉRIODE : Août 2025 – Novembre 2025 (4 mois / 8 semaines actives)
TYPE : Mission de cadrage & structuration (Freelance)

CONTEXTE :
DSI récemment élevée au rang de direction, avec une légitimité encore à consolider. Prise en main d'un nouveau département CIO Office regroupant deux pôles préexistants — PMO et Architecture d'Entreprise (10 collaborateurs) — ayant connu plusieurs managers et plusieurs DSI en l'espace de deux ans. Absence de cadre commun et de visibilité consolidée sur leur production. Pratiques Agile fragmentées faute d'accompagnement continu. DSI sans visibilité sur les résultats et le suivi budgétaire. Aucune instance de gouvernance de la demande métier.

APPROCHE :
Respecter la culture et les pratiques existantes, consolider ce qui fonctionne, corriger là où c'est nécessaire. Pas de révolution : une structuration progressive et concertée.

ACTIONS MENÉES :
• Structuration du CIO Office : Identification des rôles et compétences de chaque membre, mise en place d'un board de suivi sous forme de backlog dans Azure DevOps, construction de tableaux de bord par profil et pour la DSI, entretiens hebdomadaires individuels (suivi opérationnel + management de proximité).
• Harmonisation de la chaîne de delivery : Diagnostic et définition d'un scénario d'harmonisation des pratiques en moins de 15 jours. Nouveau rythme : 3 semaines de sprint + 1 semaine inter-sprint avec Sprint Planning élargi (POs, équipes dev, prod, flux, test, RSSI). Lancement de 2 POCs. Objectif cible : jour PI Planning (notion SAFe).
• Gouvernance de la demande : Conception d'une Design Authority : instance de saisine centralisée (Business Architect, Architecte applicatif, Architecte solution IA, RSSI). Qualification des demandes business, identification des parties prenantes, restitution sous 48h des solutions disponibles sur étagère.
• Conduite du changement : Ateliers hebdomadaires de 2h pour former des ambassadeurs internes à l'hybridation méthodologique (Agile / cycle en V). Présentation aux directeurs métier — adhésion étendue au-delà de la DSI.

RÉSULTATS :
En 8 semaines : diagnostic complet de la chaîne de valeur DSI livré, nouveau département structuré, cadre de delivery fluidifié, Design Authority conçue et validée par les directions métier, tableau de bord C-Level opérationnel.
Fin de mission suite à coupure budgétaire importante (non liée à la performance).

CHIFFRES CLÉS :
• 8 semaines de mission active
• 10 collaborateurs senior managés en direct
• 15 jours pour livrer le diagnostic complet
• 4 chantiers lancés simultanément

OUTILS : Azure DevOps
MÉTHODES : Agile, SAFe, Sprint Planning, PI Planning, Design Authority
COMPÉTENCES ILLUSTRÉES : Gouvernance IT, Management d'équipe, Conduite du changement, Pilotage de la DSI, Agilité, CIO Office
EOT, ['company' => 'AFNOR Groupe', 'role' => 'Responsable CIO Office', 'period' => 'Août–Nov. 2025']);


// ═══════════════════════════════════════════════════════════════
// 6. ANAH — Architecture d'Entreprise (2025)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Directrice de Projet Architecture d\'Entreprise — ANAH (2024-2025)', <<<'EOT'
MISSION : Directrice de Projet — Service Architecture d'Entreprise
ORGANISATION : ANAH — Agence Nationale pour l'Amélioration de l'Habitat | Secteur public | Paris 8
PÉRIODE : Janvier 2025 – Août 2025 (8 mois) | Novembre 2024 – Août 2025 (selon CV)
TYPE : Coordination & Gouvernance Architecture

CONTEXTE :
Équipe de 14 architectes (solution et entreprise) fonctionnant en silos, sans gouvernance commune ni visibilité partagée sur l'avancement des chantiers. Les BU ne pouvaient pas suivre les études menées par le SAE. Le responsable de service, surchargé, avait besoin d'un bras droit opérationnel.

APPROCHE :
Mettre en place un cadre de gouvernance commun, outiller la visibilité transverse et assurer le rôle de second opérationnel du responsable de service.

ACTIONS MENÉES :
• Structuration de la gouvernance : Mise en place d'un cadre de gouvernance au sein du SAE. Centralisation sur Jira et Confluence — co-construction avec les architectes. Visibilité étendue aux BU et directions métier. Animation des instances de coordination. Second du responsable de service.
• Analyse de processus & BPMN : Analyse et modélisation des processus métier et SI en BPMN. Production de schémas de référence pour formaliser une vision commune entre architecture, métier et DSI.
• Plateforme unique de distribution des aides : Contribution à la coordination sur le chantier stratégique de conception de la future plateforme unifiée de distribution des aides de l'ANAH.
• Appel d'offres Back-Office : Contribution au cadrage technique du renouvellement du plus gros contrat de l'ANAH (externalisation de la gestion des dossiers). Participation au contenu de l'AO.

RÉSULTATS :
Équipe de 14 architectes fédérée autour d'un cadre de gouvernance commun et d'outils partagés. Visibilité sur les chantiers étendue aux directions métier. Deux projets stratégiques majeurs contribués dans les délais.

CHIFFRES CLÉS :
• 14 architectes fédérés autour d'un cadre commun
• 2 projets stratégiques majeurs contribués
• Outils : Jira + Confluence
• Visibilité étendue aux directions métier hors DSI

OUTILS : Jira, Confluence
MÉTHODES : TOGAF 10, BPMN, Gouvernance Architecture, Appel d'offres
COMPÉTENCES ILLUSTRÉES : Architecture d'Entreprise, Gouvernance IT, Management transverse, BPMN, TOGAF 10, Design Authority, RFP
EOT, ['company' => 'ANAH', 'role' => 'Directrice de Projet Architecture d\'Entreprise', 'period' => 'Nov. 2024 – Août 2025']);


// ═══════════════════════════════════════════════════════════════
// 7. ANAH — Distribution des Aides (2023-2024)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Directrice de Projets/Produits DSIN — ANAH MaPrimeRénov\' (2023-2024)', <<<'EOT'
MISSION : Directrice de Projets / Produits DSIN — Distribution des Aides (MaPrimeRénov', MaPrimeAdapt')
ORGANISATION : ANAH — Agence Nationale pour l'Amélioration de l'Habitat | Secteur public | Paris
PÉRIODE : Mars 2023 – Novembre 2024 (20 mois)
TYPE : Rôle d'interface stratégique

CONTEXTE :
Dispositifs nationaux d'aide à la rénovation parmi les plus utilisés de France portés par un SI critique, dans un contexte de réorganisation simultanée de la DSI et des métiers. Évolutions réglementaires fréquentes, pression politique forte, multitude d'acteurs aux enjeux divergents (ANAH, Ministère, éditeurs, prestataires, directions métier).

APPROCHE :
Assurer le rôle de pont entre usagers, orientations politiques, directions métier et DSI. Faciliter la réorganisation interne face à des résistances au changement marquées. Maintenir l'alignement continu des enjeux divergents.

ACTIONS MENÉES :
• Interface stratégique : Pont entre usagers, orientations politiques, directions métier et DSI. Facilitatrice de la réorganisation interne face à des résistances au changement marquées.
• Pilotage de projets d'envergure : Direction de 4 à 5 projets simultanés : évolutions réglementaires, sécurité, maintenance applicative. Constitution et pilotage d'une équipe dédiée de 5 personnes. Coordination d'une équipe projet globale de plus de 100 parties prenantes.
• Gestion de l'obsolescence & migration : Pilotage d'un projet critique : montée de version simultanée d'un outil stratégique (déployé en 2006) et des serveurs hébergés au Ministère. Initiative de mise en transparence tripartite (ANAH, Ministère, prestataires). Constitution d'une équipe projet de 50 personnes. Livraison dans les délais malgré la double contrainte applicative + infrastructure.
• Continuité de service : Maintien en condition opérationnelle d'un dispositif traitant 50 000 dossiers par jour, sans rupture de service majeure sur 20 mois.

RÉSULTATS :
Continuité de service garantie sur l'un des dispositifs publics les plus sollicités de France. Conformité réglementaire maintenue dans un contexte législatif en évolution permanente. Projet d'obsolescence (migration applicative + infra) livré dans les délais. Réorganisation DSI/métiers accompagnée avec succès malgré un contexte de résistance au changement.

CHIFFRES CLÉS :
• 50 000 dossiers/jour sur le dispositif
• Budget géré : 10–20M€
• 100+ parties prenantes coordonnées
• 0 rupture de service majeure sur 20 mois
• 50 personnes dans l'équipe projet migration

MIGRATION (expérience clé) :
Montée de version majeure d'une application déployée en 2006 vers une version 2024, en parallèle d'une migration d'infrastructure orchestrée par l'hébergeur ministériel. Double contrainte : migration applicative ET infrastructure simultanées. Plan de bascule, gestion des risques, tests de non-régression, coordination tripartite ANAH/Ministère/prestataires.

MÉTHODES : Gestion de programme, Conduite du changement, Coordination tripartite, Gestion de l'obsolescence, Migration applicative
COMPÉTENCES ILLUSTRÉES : Transformation digitale, Gestion de programme, Interface stratégique, Conduite du changement, Gestion budgétaire, Migration de plateforme, Continuité de service
EOT, ['company' => 'ANAH', 'role' => 'Directrice de Projets/Produits DSIN', 'period' => 'Mars 2023 – Nov. 2024']);


// ═══════════════════════════════════════════════════════════════
// 8. SOCIÉTÉ GÉNÉRALE — Programme Leasing (2022)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Directrice de Programme — Refonte Plateforme Leasing SG CIB (2022)', <<<'EOT'
MISSION : Directrice de Programme / Senior Project Manager — Refonte Plateforme Leasing
ORGANISATION : Société Générale CIB — Corporate and Institutional Banking (via Devoteam Creative Tech)
SECTEUR : Banque | La Défense
PÉRIODE : Mars 2022 – Octobre 2022 (6 mois)
TYPE : Mission de cadrage — Programme international

CONTEXTE :
Refonte totale d'une plateforme internationale de financement structuré (leasing) à cadrer en urgence. Une étude similaire avait déjà échoué, générant un fort freinage interne. Quatre streams internationaux aux intérêts divergents (Paris, Londres, Luxembourg, Roumanie), enjeux fiscaux multi-pays, directions prêtes à menacer de démissionner en steerco. Programme sponsorisé par les directions GLBA et GBSU.

APPROCHE :
Mise en place de la gouvernance, structuration des 4 streams en environnement international, évaluation rigoureuse des scénarios sans parti pris, désamorçage des tensions.

ACTIONS MENÉES :
• Cadrage architecture & gouvernance : Mise en place de la gouvernance du programme, structuration des 4 streams (Compta, Leasing, SI, Data) en environnement international. Prise en compte des contraintes fiscales multi-pays.
• RFP & gestion des éditeurs : Pilotage des appels d'offres, organisation des démonstrations éditeurs (Cassiopae, Alfa), analyse comparative des scénarios (conservation/évolution vs refonte totale). Évaluation rigoureuse sans parti pris malgré les pressions.
• Gestion des parties prenantes conflictuelles : Désamorçage des tensions en steerco, gestion de la communication entre directions aux intérêts opposés, mise en confiance progressive des profils les plus réfractaires.
• Étude de migration de plateforme : Direction de la phase d'étude de migration complète de la plateforme internationale de financement structuré, avec évaluation de tous les scénarios y compris migration des données et bascule de flux.

RÉSULTATS :
Scénario de transformation validé et documenté en 6 mois, dans les délais imposés par l'obsolescence. Consensus obtenu malgré un historique d'échec et un contexte hautement conflictuel. Mission saluée — recommandation interne directe pour une mission ESG stratégique.

CHIFFRES CLÉS :
• 80+ parties prenantes managées
• 4 pays : Paris · Londres · Luxembourg · Roumanie
• 5–10M€ de programme validé
• 6 mois de cadrage sous contrainte d'obsolescence

OUTILS : Cassiopae, Alfa
MÉTHODES : Gouvernance de programme, RFP, Gestion de parties prenantes, Cadrage architecture, Étude de migration
CONTEXTE SECTORIEL : Financement structuré, leasing, plateforme bancaire internationale
COMPÉTENCES ILLUSTRÉES : Gestion de programme international, RFP, Cadrage architecture, Gestion de conflits, Transformation digitale, Étude de migration de plateforme bancaire
EOT, ['company' => 'Société Générale CIB', 'role' => 'Directrice de Programme Leasing', 'period' => 'Mars – Oct. 2022']);


// ═══════════════════════════════════════════════════════════════
// 9. SOCIÉTÉ GÉNÉRALE — Data ESG (2022-2023)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Directrice Chantier Data ESG — Société Générale CIB (2022-2023)', <<<'EOT'
MISSION : Directrice de Projet / Senior Project Manager — Stream Data ESG (Programme "ESG by Design")
ORGANISATION : Société Générale CIB — Corporate and Institutional Banking (via Devoteam Creative Tech)
SECTEUR : Banque | La Défense
PÉRIODE : Octobre 2022 – Mars 2023 (5 mois)
TYPE : Mission de structuration

CONTEXTE :
Programme ESG stratégique venant de changer de direction, avec des équipes data non structurées et des processus de modification de la donnée non formalisés. Aucune cartographie des parties prenantes par instance. 50+ contributeurs issus de 4 streams (Data, Finance, Risques, Compliance) et de l'ensemble des BU du groupe à aligner — sans base organisationnelle établie.

APPROCHE :
Structurer la gouvernance data, cartographier les parties prenantes, mettre en place les instances et organiser la circulation de l'information.

ACTIONS MENÉES :
• Structuration de la gouvernance data : Cartographie complète du processus de modification de la donnée (ESG et non-ESG) pour identifier les bonnes parties prenantes à convier au bon moment. Travail salué car jamais réalisé auparavant.
• Mise en place des instances : Définition de la composition et du rôle de chaque instance. Organisation de la circulation de l'information entre les streams et les BU pour fluidifier les échanges et capitaliser la connaissance.
• Alignement & reporting : Coordination transverse de 50+ parties prenantes cross-BU. Mise en place du reporting et du suivi du programme.

RÉSULTATS :
Gouvernance data ESG structurée et opérationnelle en 5 mois sur un programme en reconstruction. Bases solides posées et transmises pour la continuité. Départ unanimement regretté.

CHIFFRES CLÉS :
• 50+ parties prenantes alignées
• 4 streams : Data · Finance · Risques · Compliance
• 5 mois pour structurer un programme en reconstruction
• 0 base documentaire existante à l'arrivée

MÉTHODES : Gouvernance data, Cartographie des parties prenantes, Reporting de programme, ESG by Design
RÉGLEMENTATION : ESG, Conformité bancaire
COMPÉTENCES ILLUSTRÉES : Gestion des risques ESG, Gouvernance data, Coordination transverse, Knowledge management, Conformité réglementaire
EOT, ['company' => 'Société Générale CIB', 'role' => 'Directrice Chantier Data ESG', 'period' => 'Oct. 2022 – Mars 2023']);


// ═══════════════════════════════════════════════════════════════
// 10. BNP PARIBAS — Data Quality KYC (2018)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Cheffe de Projets MOA Data Quality KYC — BNP Paribas CIB (2018)', <<<'EOT'
MISSION : Cheffe de Projets Senior MOA — Data Quality KYC
ORGANISATION : BNP Paribas CIB — Corporate and Institutional Banking (via Altran)
SECTEUR : Banque | Paris 19
PÉRIODE : Août 2018 – Décembre 2018 (4 mois)
TYPE : Mission d'étude

CONTEXTE :
Une donnée identifiée comme redondante et inutile est sur le point d'être décommissionnée. Une étude est commandée — pour la forme — avant de valider la décision. Aucun impact business n'est anticipé.

APPROCHE :
Refus de valider une décision sans en vérifier les conséquences réelles. Investigation mondiale auprès de toutes les parties prenantes.

ACTIONS MENÉES :
• Enquête mondiale : Investigation auprès de toutes les parties prenantes mondiales (USA, EMEA, Asie) pour recenser l'ensemble des systèmes en aval utilisant cette donnée.
• Ateliers worldwide : Animation d'ateliers multi-régions pour cartographier les usages réels de la donnée. Découverte que la donnée avait été détournée de son usage initial et utilisée de façon critique par Global Markets — impactant directement les activités de trading.
• Recommandation : Conclusion documentée et défendue : le décommissionnement aurait eu des conséquences business significatives. Recommandation de maintien de la donnée.

RÉSULTATS :
Décommissionnement annulé suite à l'étude. Business protégé, notamment sur le périmètre Global Markets (trading). Mission remarquée en interne — accès direct au programme Fenergo (le plus important de BNP Paribas CIB) en reconnaissance de la rigueur et de l'indépendance d'analyse démontrées.

CHIFFRES CLÉS :
• 3 régions investigées : USA · EMEA · Asie
• Activité Global Markets / trading protégée
• Décommissionnement annulé sur recommandation

MÉTHODES : Data Quality, Investigation mondiale, Analyse d'impact, Ateliers multi-régions
COMPÉTENCES ILLUSTRÉES : Data Quality, Gestion des risques, Analyse d'impact business, Investigation, Indépendance d'analyse, KYC
EOT, ['company' => 'BNP Paribas CIB', 'role' => 'Cheffe de Projets MOA Data Quality KYC', 'period' => 'Août–Déc. 2018']);


// ═══════════════════════════════════════════════════════════════
// 11. BNP PARIBAS — Fenergo CLM/KYC (2018-2019)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Cheffe de Projets Senior MOA Programme CLM/KYC Fenergo — BNP Paribas CIB (2018-2019)', <<<'EOT'
MISSION : Cheffe de Projets Senior MOA — Programme CLM / KYC (Fenergo)
ORGANISATION : BNP Paribas CIB — Corporate and Institutional Banking (via Altran)
SECTEUR : Banque | Paris 19
PÉRIODE : Août 2018 – Novembre 2019 (14 mois dont 4 mois Data Quality + 10 mois Fenergo)
PROGRAMME : CLM — Client Lifecycle Management

CONTEXTE :
Modernisation du cycle de vie client et des processus KYC / Due Diligence via l'implémentation du progiciel Fenergo. Besoins non fonctionnels complexes : conformité RGPD, gestion des données et contraintes de confidentialité par juridiction (Suisse, Jersey, Luxembourg).

APPROCHE :
Contribution aux besoins non fonctionnels et data. Double rôle : contributrice projet au sein de l'équipe BNP et correspondante Altran pour 10 consultants.

ACTIONS MENÉES :
• Besoins non fonctionnels & data : Contribution à l'élaboration du dictionnaire de données du programme. Analyse et prise en compte des contraintes de confidentialité spécifiques par juridiction (Suisse, Jersey, Luxembourg) dans les spécifications.
• Conformité RGPD : Participation à l'élaboration du PIA (Privacy Impact Assessment) et contribution aux comités RGPD pour le suivi et la validation avec le CDO et le DPO.
• Coordination & facilitation : Double rôle : contributrice projet au sein de l'équipe BNP et correspondante Altran pour 10 consultants du même département.

RÉSULTATS :
Périmètre non fonctionnel structuré et documenté sur un programme KYC à fort enjeu réglementaire. Conformité RGPD engagée et tracée. Contraintes multi-juridictions intégrées dans les spécifications Fenergo.

CHIFFRES CLÉS :
• 3 juridictions à contraintes spécifiques : Suisse · Jersey · Luxembourg
• 10 consultants coordonnés
• PIA élaboré et validé en comité RGPD

OUTILS : Fenergo (progiciel KYC)
MÉTHODES : KYC / Due Diligence, RGPD / PIA, Scrum Master, Data Management
RÉGLEMENTATION : KYC, RGPD, Conformité bancaire internationale
COMPÉTENCES ILLUSTRÉES : KYC, Conformité RGPD, Gestion des données, Coordination multi-équipes, MOA, Fenergo
EOT, ['company' => 'BNP Paribas CIB', 'role' => 'Cheffe de Projets MOA CLM/KYC Fenergo', 'period' => 'Août 2018 – Nov. 2019']);


// ═══════════════════════════════════════════════════════════════
// 12. SIACI — Dispenses Frais de Santé (2019-2021)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Responsable Projet & Commercialisation — SIACI SAINT HONORÉ (2019-2021)', <<<'EOT'
MISSION : Responsable de Projets — Solution Front to Back (Dispenses Frais de Santé)
ORGANISATION : SIACI SAINT HONORÉ | Assurance / Courtage | Paris 17
PÉRIODE : Décembre 2019 – Novembre 2021 (2 ans)
TYPE : Rôle pivot — triple casquette

CONTEXTE :
Industrialisation d'une solution de gestion des dispenses de frais de santé — sujet complexe pour les DRH, impliquant des règles de gestion métier fines, des échanges de flux entre systèmes et une adoption par des milliers de salariés côté client. Chaque déploiement nécessitait une coordination complète entre front, back, DSI, éditeurs et équipes métier client.

APPROCHE :
Triple casquette : Chef de projet, interlocutrice commerciale (prospection et SAV) et responsable du développement fonctionnel de la solution. Élément pivot entre toutes les parties prenantes internes et externes.

ACTIONS MENÉES :
• Déploiements clients grands comptes : Pilotage de bout en bout de 3 déploiements majeurs auprès de grandes enseignes nationales de la distribution (+54 000 salariés) et du textile (5 000 à 9 000 salariés) : cadrage, paramétrage des règles de gestion, recette, conduite du changement auprès des équipes DRH et des salariés.
• Data Quality & Framework : Travail approfondi de data quality sur les données salariés et les flux. Création d'un framework de déploiement pour standardiser et accélérer les futures implémentations.

RÉSULTATS :
3 déploiements majeurs livrés de bout en bout auprès de grands comptes nationaux. Le plus grand client représentait 3M€ de revenus annuels pour SIACI. Framework d'implémentation créé et transmis. Experte reconnue sur la solution.

CHIFFRES CLÉS :
• 3M€ de revenus annuels générés (plus grand client)
• 54 000+ salariés concernés (plus grand client)
• 1 framework d'implémentation créé
• 2 directeurs ayant cherché à la retenir au moment du départ

MÉTHODES : Gestion de projet end-to-end, Conduite du changement, Data Quality, Développement fonctionnel, Déploiement client
COMPÉTENCES ILLUSTRÉES : Leadership, Conduite du changement, Gestion de déploiement, Data Quality, Relation client, Développement commercial
EOT, ['company' => 'SIACI SAINT HONORÉ', 'role' => 'Responsable Projet & Commercialisation', 'period' => 'Déc. 2019 – Nov. 2021']);


// ═══════════════════════════════════════════════════════════════
// 13. TRANSACTIS — MOA SWIFT (2021-2022)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Responsable Équipe MOA SWIFT — TRANSACTIS (2021-2022)', <<<'EOT'
MISSION : Responsable Équipe MOA SWIFT (Management de transition)
ORGANISATION : TRANSACTIS — Filiale informatique Société Générale / Groupe La Poste
SECTEUR : Banque / Informatique | Fontenay-sous-Bois
PÉRIODE : Décembre 2021 – Mars 2022 (4 mois)

CONTEXTE : Management de transition dans un contexte de réorganisation.

ACTIONS MENÉES :
• Management hiérarchique d'une équipe de 14 Chefs de projets SI et homologateurs SWIFT.
• Transition et stabilisation de l'équipe dans un contexte de réorganisation.

CHIFFRES CLÉS :
• 14 chefs de projets SI et homologateurs managés

COMPÉTENCES ILLUSTRÉES : Management d'équipe, MOA, SWIFT, Management de transition
EOT, ['company' => 'TRANSACTIS', 'role' => 'Responsable Équipe MOA SWIFT', 'period' => 'Déc. 2021 – Mars 2022']);


// ═══════════════════════════════════════════════════════════════
// 14. ENTREPRENEURIAT (2013-2018)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Dirigeante de TPE & Présidente Association Commerçants (2013-2018)', <<<'EOT'
MISSION : Dirigeante de TPE & Présidente d'Association des Commerçants
ORGANISATION : 3 TPE créées + Association des Commerçants (80 commerces)
SECTEUR : Commerce / Entrepreneuriat | Région parisienne — Essonne
PÉRIODE : Juin 2013 – Juin 2018 (5 ans)

CONTEXTE :
Création et développement de 3 entreprises en parallèle dans un environnement contraint : ressources limitées, marchés compétitifs, obligations légales et financières complexes. En simultané : mandat de présidente de l'association des commerçants d'un centre commercial regroupant 80 commerces.

PROJETS :
• 2013 : Brûlerie & épicerie fine — Création d'une entreprise et développement d'une marque propre sur un marché de niche — 3 établissements gérés pendant 5 ans. Lauréate CCI Essonne.
• 2014 : Cave à vins — Développement de la gamme proposée au client. Lauréate CCI Essonne.
• 2015 : Société de conseil marketing — Mandat de gestion de la communication et du marketing pour 80 commerces (Association des Commerçants).

ACTIONS MENÉES :
• Direction opérationnelle : Création, gestion et développement de 3 projets entrepreneuriaux. Management hiérarchique de 12 salariés sur 5 ans. Pilotage budgétaire en temps réel. CA global annuel jusqu'à 900K€.
• Financement & négociation : Obtention de 3 prêts à taux 0 (CCI Essonne) et 2 prêts bancaires. Négociation avec les tribunaux pour la liquidation maîtrisée des activités via mandataire. Défense de projets devant jurys professionnels.
• Leadership territorial : Présidente de l'association des commerçants : animation et représentation de 80 commerces, pilotage de la stratégie de communication et marketing du centre commercial.

RÉSULTATS :
5 ans d'entrepreneuriat forgent des compétences que peu de CIO possèdent : gestion de crise, négociation sous pression, management humain, pilotage budgétaire terrain. Lauréate de plusieurs premiers prix CCI Essonne. Vision business 360° au service de la DSI.

CHIFFRES CLÉS :
• 12 salariés managés
• 900K€ de CA global annuel
• 80 commerces représentés (présidente)
• 5 financements obtenus (CCI + banques)

COMPÉTENCES ILLUSTRÉES : Management humain, Pilotage budgétaire, Gestion de crise, Négociation, Leadership, Création d'entreprise, Vision business 360°, GPEC
EOT, ['company' => '3 TPE + Association', 'role' => 'Dirigeante & Présidente', 'period' => 'Juin 2013 – Juin 2018']);


// ═══════════════════════════════════════════════════════════════
// 15. CACEIS — MOA OST/Fiscalité & Contrôle Dépositaire (2005-2010)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Chef de Projet MOA CACEIS — OST/Fiscalité & Contrôle Dépositaire (2005-2010)', <<<'EOT'
MISSION : Chef de projet MOA OST / Fiscalité & Contrôle Dépositaire
ORGANISATION : CACEIS (Groupe Crédit Agricole / Groupe La Poste) | Banque / Gestion d'actifs
PÉRIODE : Janvier 2005 – Août 2010 (5 ans 8 mois)

ACTIVITÉS MOA OST / Fiscalité (2008–2010) :
• Élaboration des expressions de besoins, cahiers des charges et de recette.
• Formations utilisateurs.
• Mobilisation des acteurs (MOE, utilisateurs, prestataires).
• Pilotage de projets divers : évolutions applicatives, mise en conformité réglementaire, migrations de comptes.
• Pilotage d'un sous-lot d'une migration internationale (Luxembourg → Paris) : migration de données et de flux entre deux systèmes, coordination des parties prenantes, plan de bascule.

ACTIVITÉS MOA Contrôle Dépositaire (2005–2008) :
• Projets de mutualisation d'outils, refontes d'applicatifs.
• Gestion de projets sur lots et sous-projets pour le contrôle dépositaire OPCVM.
• Suivi de production et maintenances correctives.
• Création de requêtes SQL.
• Gestion relation éditeurs et fournisseurs externes.

EXPÉRIENCE MIGRATION NOTABLE :
Pilotage d'un sous-lot d'une migration internationale CACEIS Luxembourg → Paris : migration de données complexes, coordination des équipes locales et internationales, bascule de flux financiers entre systèmes.

OUTILS : Mig21, Samkhya, Navis, Rosa, Grival, SQL
SECTEUR : Gestion d'actifs, OPCVM, Marchés financiers, Titres
COMPÉTENCES ILLUSTRÉES : MOA, Gestion de projet, OPCVM, Fiscalité titres, Migration de données internationale, SQL, Contrôle dépositaire
EOT, ['company' => 'CACEIS', 'role' => 'Chef de Projet MOA', 'period' => 'Janv. 2005 – Août 2010']);


// ═══════════════════════════════════════════════════════════════
// 16. CRÉDIT AGRICOLE CONSUMER FINANCE — Monétique 3D Secure (2012-2013)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Chef de Projets MOA Monétique 3D Secure — Crédit Agricole Consumer Finance (2012-2013)', <<<'EOT'
MISSION : Chef de Projets MOA Monétique
ORGANISATION : Crédit Agricole Consumer Finance (Sofinco / Finaref) | Banque / Crédit | Évry
PÉRIODE : Janvier 2012 – Février 2013 (1 an 2 mois)

CONTEXTE :
Projet réglementaire : mise en place d'une solution 3D Secure pour les cartes partenaires non bancaires de Crédit Agricole Consumer Finance. Enjeux de mutualisation entre deux processus métiers sur deux SI différents.

DOMAINE MONÉTIQUE :
Projet dans le domaine de la monétique (cartes de paiement partenaires, authentification 3D Secure). Interface entre les processus d'émission carte et les systèmes d'autorisation. Coordination avec les schèmes de paiement et les partenaires commerçants.

COMPÉTENCES ILLUSTRÉES : MOA, Monétique, 3D Secure, Projet réglementaire, Cartes partenaires, Émission carte, Processus de paiement
EOT, ['company' => 'Crédit Agricole Consumer Finance', 'role' => 'Chef de Projets MOA Monétique', 'period' => 'Janv. 2012 – Fév. 2013']);


// ═══════════════════════════════════════════════════════════════
// 17. CRÉDIT AGRICOLE LEASING — Front Office Leasing (2010-2011)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Chef de Projet MOA Front Office Leasing — Crédit Agricole Leasing (2010-2011)', <<<'EOT'
MISSION : Chef de Projet MOA — Front Office Leasing
ORGANISATION : Crédit Agricole Leasing & Factoring | Banque / Leasing
PÉRIODE : Août 2010 – Décembre 2011 (1 an 5 mois)

ACTIVITÉS :
• Pilotage/cadrage de projets sur le périmètre front du crédit-bail mobilier et immobilier.
• Parties prenantes : caisses régionales, partenaires entreprises.
• Sponsor : direction commerciale.
• Contribution à l'étude de montée de version Cassiopae V4 (plateforme de leasing / financement structuré).

OUTIL CASSIOPAE :
Connaissance fonctionnelle de Cassiopae — plateforme de gestion du leasing et du financement structuré (même outil utilisé à la Société Générale CIB pour la plateforme internationale de financement structuré analysée en 2022).

OUTILS : Cassiopae
SECTEUR : Leasing, Crédit-bail mobilier et immobilier, Financement structuré
COMPÉTENCES ILLUSTRÉES : MOA, Leasing, Gestion de projet, Front Office, Cassiopae, Financement structuré
EOT, ['company' => 'Crédit Agricole Leasing & Factoring', 'role' => 'Chef de Projet MOA Front Office Leasing', 'period' => 'Août 2010 – Déc. 2011']);


// ═══════════════════════════════════════════════════════════════
// 18. CARRIÈRE DÉBUT (2001-2004)
// ═══════════════════════════════════════════════════════════════
insert($db, $stmt, 'experience', 'Début de carrière — Groupe CDC, IXIS, SG CIB (2001-2004)', <<<'EOT'
DÉBUT DE CARRIÈRE BANCAIRE (2001–2004)

Technicienne Back Office Transversal Trésorerie — Société Générale CIB
Période : Février 2001 – Décembre 2001 (11 mois)
• Rapprochements comptables, trésorerie, pays émergents.

Chargée d'Étude — Back-Office Règlement Livraison — Groupe Caisse des Dépôts (filiale IXIS)
Période : Décembre 2001 – Octobre 2003 (1 an 11 mois)
• Règlement-livraison, back-office, marchés financiers.
• Marchés France, US et pays émergents.

Account Manager — Clients Institutionnels Non Résidents — IXIS Investor Services
Période : Octobre 2003 – Décembre 2004 (1 an 3 mois) | Arcueil
• Gestion de comptes institutionnels, SWIFT, OPCVM, relation client internationale.

COMPÉTENCES ILLUSTRÉES : Marchés financiers, Back-office, Règlement-livraison, OPCVM, SWIFT, Trésorerie, Relation client institutionnel
EOT, ['company' => 'SG CIB / CDC / IXIS', 'role' => 'Début de carrière bancaire', 'period' => 'Fév. 2001 – Déc. 2004']);


// ═══════════════════════════════════════════════════════════════
// RÉSUMÉ
// ═══════════════════════════════════════════════════════════════
echo "\n══════════════════════════════════════\n";
echo "Seed terminé avec succès.\n";
echo "Pense à supprimer ce fichier : php/seed-knowledge.php\n";
echo "══════════════════════════════════════\n";
