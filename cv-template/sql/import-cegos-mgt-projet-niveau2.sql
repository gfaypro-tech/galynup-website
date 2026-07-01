-- ============================================================
-- Import : Formation CEGOS — Les clés du Management de Projet Niveau 2
-- Source  : Gr_00011313 v11.0 — Slides Animateur
-- Rôle    : Animatrice (formatrice certifiée Cegos)
-- Date    : 2026-06-15
-- ============================================================

INSERT INTO cv_knowledge (type, title, content, meta_json, period_start, keywords, is_active) VALUES

-- ---------------------------------------------------------------
-- 1. FORMATION — Animation Cegos Management de Projet Niveau 2
-- ---------------------------------------------------------------
(
  'formation',
  'Animation de formation — Les clés du Management de Projet Niveau 2 (CEGOS)',
  'Animatrice de la formation certifiante Cegos « Les clés du Management de Projet Niveau 2 » (réf. Gr_00011313, v11.0), à destination de chefs de projet confirmés. Maîtrise pédagogique et opérationnelle de l\'ensemble des outils et méthodes couverts dans les 5 modules : (1) Création de valeur — Business Case, OKR (Objectives & Key Results), RSE/ESG, (2) Stratégie de réalisation hybride — Agile/Scrum/Kanban vs Prédictif, Planning Macro Hybride, WBS/OT, PBS, MoSCoW, (3) Pilotage par les risques — ISO 31000, PMBoK, Monte Carlo, VMA/EMV, modèle JADE, signaux faibles, pensée latérale (De Bono), (4) Management des parties prenantes — Matrice Mendelow (Power/Interest Grid), Creative Problem Solving (CPS), conduite du changement, (5) Tableaux de bord avancés — EVM/Valeur Acquise (CPI, SPI, courbes en S), OTD, DOD, Power BI, Microsoft Project, Notion, IA dans le projet.',
  '{"organisme": "CEGOS", "reference": "Gr_00011313", "version": "11.0", "role": "Animatrice", "niveau": "Management de Projet Niveau 2", "public": "Chefs de projet confirmés", "modules": ["Business Case OKR RSE", "Hybride Agile Prédictif", "Risques ISO 31000", "Parties prenantes Mendelow", "EVM Tableaux de bord IA"]}',
  2026,
  'cegos, formation, animation, formatrice, management projet, chef de projet, OKR, EVM, valeur acquise, hybride, agile, scrum, kanban, risques, ISO 31000, Mendelow, RSE, CSRD, Business Case, conduite du changement, Power BI, Microsoft Project, Notion, Monte Carlo, MoSCoW, WBS',
  1
),

-- ---------------------------------------------------------------
-- 2. COMPETENCE — Management de projet hybride (Scrum, Kanban, WBS, MoSCoW)
-- ---------------------------------------------------------------
(
  'competence',
  'Management de projet hybride — Scrum, Kanban, WBS, MoSCoW, GANTT, PERT',
  'Maîtrise des approches hybrides combinant Agile (Scrum, Kanban) et prédictif (GANTT, PERT, Cascade). Construction du Planning Macro Hybride : décomposition du projet via le WBS/OT (Work Breakdown Structure / Organigramme des Tâches) et le PBS (Product Breakdown Structure), identification des composantes en mode Agile (Sprints, Product Backlog, User Stories, priorisation MoSCoW, MVP) vs prédictif (jalons contractuels, PERT, GANTT), synchronisation et points d\'accostage entre lots. Mise en place du Project Board Hybride pour le pilotage en temps réel (board Kanban par lot + GANTT macro). Choix du delivery model (niveau d\'agilité) selon une grille de critères : nature et stabilité des livrables, maturité Agile des équipes, contraintes client, écosystème organisationnel. Phase d\'avant-projet robuste : note de cadrage, macro-jalonnement, stratégie de découpage, gouvernance. Référentiels : PMI PMBOK 7, Agile Practice Guide PMI, Disciplined Agile (DA). Outils de pilotage courants : Microsoft Project, Notion, Jira.',
  '{"domaine": "Management de projet", "methodologie": ["Hybride", "Scrum", "Kanban", "Prédictif", "PMBOK", "Disciplined Agile"], "outils": ["Planning Macro Hybride", "Project Board Hybride", "WBS/OT", "PBS", "Product Backlog", "Kanban", "GANTT", "PERT", "User Stories", "MoSCoW", "MVP", "Microsoft Project", "Notion", "Jira"]}',
  2026,
  'management projet hybride, agile, scrum, kanban, sprint, product backlog, user stories, MoSCoW, MVP, WBS, work breakdown structure, OT, organigramme tâches, PBS, GANTT, PERT, planning, jalons, PMI, PMBOK, disciplined agile, delivery model, avant-projet, Microsoft Project, Jira, Notion',
  1
),

-- ---------------------------------------------------------------
-- 3. COMPETENCE — Management des risques (ISO 31000, Monte Carlo, VMA/EMV, JADE)
-- ---------------------------------------------------------------
(
  'competence',
  'Management des risques projet — ISO 31000, Monte Carlo, VMA/EMV, Matrice de criticité, FMEA',
  'Application du processus complet de management des risques projet selon ISO 31000 et PMBoK : (1) Identification — brainstorming, REX (Retour d\'Expérience), dire d\'expert, check-list, diagramme cause-effet Ishikawa, méthode Delphi, organigramme des risques par typologies ; (2) Analyse qualitative — Matrice de criticité (probabilité × impact), analyse de l\'urgence, analyse de la détectabilité, méthode Delphi ; (3) Analyse quantitative — simulation Monte Carlo, Valeur Monétaire Attendue/VMA (EMV — Expected Monetary Value) pour le calcul de provisions risques ; (4) Réponse aux risques — stratégies génériques (éviter, transférer, atténuer, accepter) ; (5) Surveillance et maîtrise — tableau de bord des risques, double matrice menaces/opportunités (Attention Arrow). Détection proactive des signaux faibles : micro-écarts, rituels de revue (5\'), vigilance collective (références : ISO 31000, HRO, Lean/Agile, FMEA). Transformation des risques en opportunités via la pensée latérale (Édouard de Bono) : reformulation, inversion, paradoxe, brainstorming divergent/convergent. Modèle JADE (Joueur, Aventurier, Défenseur, Évitant) pour adapter le management aux attitudes individuelles face au risque.',
  '{"domaine": "Management des risques", "referentiels": ["ISO 31000", "PMBoK", "HRO", "FMEA"], "outils": ["Matrice de criticité", "VMA/EMV", "Monte Carlo", "Modèle JADE", "Double matrice Attention Arrow", "Diagramme Ishikawa", "Méthode Delphi", "Tableau de bord des risques"]}',
  2026,
  'risques projet, gestion des risques, ISO 31000, PMBoK, matrice criticité, VMA, EMV, valeur monétaire attendue, expected monetary value, Monte Carlo, simulation, signaux faibles, pensée latérale, De Bono, modèle JADE, FMEA, Ishikawa, Delphi, brainstorming risques, opportunités, HRO, retour expérience, REX',
  1
),

-- ---------------------------------------------------------------
-- 4. COMPETENCE — OKR, Business Case, création de valeur
-- ---------------------------------------------------------------
(
  'competence',
  'Création de valeur projet — OKR (Objectives & Key Results), Business Case, ROI, KPI',
  'OKR (Objectives & Key Results) : méthode de pilotage par les objectifs créée par Andy Grove (Intel) et popularisée par Google. Définition et déclinaison des OKR à trois niveaux — Corporate/Stratégique (Objectif qualitatif + 2 à 5 Key Results mesurables), Chef de projet/Opérationnel, Responsable de tâche/Micro-opérationnel. Distinction rigoureuse : Objectif = direction qualitative et inspirante ; Key Results = résultats mesurables et vérifiables (pourcentages, seuils, états) ; Actions = moyens (ne figurent pas dans les OKR). Construction de Business Case structurés : résumé exécutif, définition du problème (symptômes, impacts, limites système), vue d\'ensemble du projet (périmètre, objectifs, KPI de réussite, hypothèses, contraintes, jalons), impact organisationnel (outils, processus, rôles, formation), analyse coûts/bénéfices (ROI), analyse des alternatives, alignement stratégique. Identification et mesure de toutes les dimensions de valeur : valeur financière (gains, économies, ROI, TCO), valeur opérationnelle, valeur environnementale, valeur sociale, valeur gouvernance.',
  '{"domaine": "Stratégie et Valeur", "referentiels": ["OKR", "PMBoK"], "outils": ["OKR", "Business Case", "KPI", "ROI", "TCO", "Fiche de cadrage"]}',
  2026,
  'OKR, objectives key results, objectifs clés résultats, business case, création de valeur, ROI, KPI, TCO, valeur financière, valeur non financière, valeur opérationnelle, valeur sociale, valeur environnementale, valeur gouvernance, alignement stratégique, Google, Intel, Andy Grove, John Doerr, pilotage objectifs',
  1
),

-- ---------------------------------------------------------------
-- 5. COMPETENCE — RSE / ESG (CSRD, ESRS, GRI, ISO 26000, EcoVadis, B Corp)
-- ---------------------------------------------------------------
(
  'competence',
  'RSE / ESG dans les projets — CSRD, ESRS, GRI Standards, ISO 26000, EcoVadis, B Corp',
  'Intégration des enjeux RSE (Responsabilité Sociétale des Entreprises) et ESG (Environnement, Social, Gouvernance) dans le cycle de vie projet. Analyse des enjeux RSE dès l\'initiation : pollution, consommation énergétique, inclusion, égalité des chances, accessibilité. Évaluation d\'impact environnemental (Analyse du Cycle de Vie/ACV, bilan carbone) et social (emploi, diversité, bien-être au travail). Intégration d\'objectifs durables dans la fiche de cadrage et les Business Case (valeurs ESG mesurables). Sélection de fournisseurs responsables (labels environnementaux, politique sociale, circuits courts). Mise en place de gouvernance éthique et transparente. Connaissance des référentiels RSE/ESG : cadres internationaux — ISO 26000 (lignes directrices RSE), ODD/SDGs (17 Objectifs de Développement Durable ONU), OCDE Guidelines, UN Global Compact, AA1000, GRI Standards (Global Reporting Initiative) ; normes et labels — SA8000, B Corp, EcoVadis (notation RSE) ; réglementation européenne — CSRD (Corporate Sustainability Reporting Directive), Taxonomie européenne verte, ESRS (European Sustainability Reporting Standards). Connaissance des 14 familles RSE alignées CSRD/ESRS.',
  '{"domaine": "RSE / ESG / Développement durable", "referentiels": ["ISO 26000", "CSRD", "ESRS", "ODD/SDGs", "GRI Standards", "UN Global Compact", "AA1000", "SA8000", "EcoVadis", "B Corp"], "dimensions": ["Environnement", "Social", "Gouvernance"]}',
  2026,
  'RSE, ESG, développement durable, CSRD, corporate sustainability reporting directive, ESRS, european sustainability reporting standards, ISO 26000, ODD, SDGs, objectifs développement durable, GRI, global reporting initiative, EcoVadis, B Corp, UN Global Compact, AA1000, SA8000, bilan carbone, ACV, taxonomie européenne, impact environnemental, impact social, gouvernance, fournisseurs responsables',
  1
),

-- ---------------------------------------------------------------
-- 6. COMPETENCE — Parties prenantes (Matrice Mendelow, CPS, conduite du changement)
-- ---------------------------------------------------------------
(
  'competence',
  'Management des parties prenantes — Matrice Mendelow (Power/Interest Grid), Creative Problem Solving, conduite du changement',
  'Cartographie complète des parties prenantes internes (employés, utilisateurs, managers, IRP, direction) et externes (clients, fournisseurs, administrations, médias, associations, régulateurs, collectivités, élus, acteurs financiers) via la Matrice Mendelow (Power/Interest Grid — fort pouvoir / fort intérêt = gérer étroitement ; fort pouvoir / faible intérêt = satisfaire ; faible pouvoir / fort intérêt = informer ; faible pouvoir / faible intérêt = surveiller). Stratégie d\'engagement différenciée par quadrant : mobilisation des acteurs "pour/actifs" en ambassadeurs, levée des résistances des acteurs "contre/actifs", activation des passifs. Cartographie des réseaux d\'influence. Application du Creative Problem Solving (CPS) pour co-construire des solutions. Construction du plan d\'accompagnement du changement : plan de communication (newsletter, kit managérial, vidéos témoignages), plan de formation (nouveaux métiers, travail à distance, management), plan de change (journées onboarding, ateliers expression, brainstorming, parcours d\'intégration). Modèle des 3 phases de la transformation (William Bridges : fin du passé / zone neutre / renouveau) et 4 leviers du changement.',
  '{"domaine": "Conduite du changement et parties prenantes", "referentiels": ["Matrice Mendelow", "Power Interest Grid", "William Bridges", "Creative Problem Solving (CPS)"], "outils": ["Matrice Mendelow", "Stakeholder Register", "Plan de changement", "Plan de communication", "Plan de formation", "Réseau d\'influence"]}',
  2026,
  'parties prenantes, stakeholders, Mendelow, Power Interest Grid, matrice pouvoir intérêt, stakeholder mapping, cartographie parties prenantes, conduite du changement, change management, Creative Problem Solving, CPS, ambassadeurs, résistances au changement, William Bridges, transition, plan accompagnement, plan communication, plan formation, réseau influence, engagement parties prenantes',
  1
),

-- ---------------------------------------------------------------
-- 7. COMPETENCE — EVM / Valeur Acquise (CPI, SPI, courbes en S, Power BI, MS Project)
-- ---------------------------------------------------------------
(
  'competence',
  'Pilotage projet — EVM / Valeur Acquise, CPI, SPI, courbes en S, Power BI, Microsoft Project, Notion',
  'Conception et animation de tableaux de bord projet couvrant coûts, délais, qualité, risques, périmètre et ressources. Maîtrise complète de l\'Earned Value Management (EVM / Valeur Acquise — référentiel PMBoK) : EV/VA (Earned Value / Valeur Acquise = CBTE), PV/VP (Planned Value / Valeur Planifiée = CBTP), AC/CA (Actual Cost / Coût Actuel = CRTE), CPI (Cost Performance Index / Indice de Performance des Coûts = VA/CA), SPI (Schedule Performance Index / Indice de Performance des Délais = VA/VP), écarts coûts (CV) et délais (SV), réestimation du Budget à Terminaison (BAC/EAC), courbes en S (CBTP). Mesure de l\'avancement physique : méthode 0/100 %, unités mesurables, jalons intermédiaires pondérés. Indicateurs opérationnels : OTD (On Time Delivery), DOD (Definition of Done). Diagramme temps/temps pour le suivi des jalons. Data visualisation : construction de tableaux de bord avec Power BI, Microsoft Project, Notion ; principes de visualisation efficace (checklist IMPACT : une idée par visuel, minimalisme, 2-3 couleurs, axes à zéro, contexte, test non-expert). Rituels de captation de l\'avancement et détection des signaux faibles d\'écart.',
  '{"domaine": "Pilotage projet", "referentiels": ["PMBoK", "EVM", "Earned Value Management"], "indicateurs": ["EV/VA", "PV/VP", "AC/CA", "CPI/IPC", "SPI/IPD", "OTD", "DOD", "BAC/EAC"], "outils": ["EVM", "Courbes en S", "Tableau de bord", "Diagramme jalons", "Diagramme Temps/Temps", "Power BI", "Microsoft Project", "Notion"]}',
  2026,
  'EVM, earned value management, valeur acquise, CPI, cost performance index, IPC, SPI, schedule performance index, IPD, CBTE, CBTP, CRTE, EV, PV, AC, VA, VP, CA, courbes en S, tableau de bord projet, OTD, on time delivery, DOD, definition of done, avancement physique, pilotage projet, KPI projet, diagramme jalons, temps/temps, Power BI, Microsoft Project, Notion, data visualisation, BAC, EAC, budget terminaison',
  1
),

-- ---------------------------------------------------------------
-- 8. COMPETENCE — IA dans le management de projet (ChatGPT, Copilot, prompt engineering)
-- ---------------------------------------------------------------
(
  'competence',
  'Intelligence Artificielle dans le management de projet — ChatGPT, Microsoft Copilot, Prompt Engineering, RACI, WBS, Backlog',
  'Maîtrise et enseignement des cas d\'usages de l\'IA générative (ChatGPT, Claude, Microsoft Copilot, Copilot for Microsoft 365, Notion AI) dans le cycle de vie projet. Phase d\'initiation : définition de personas, cadrage projet par prompt (génération de fiche de cadrage structurée — contexte, objectifs, périmètre, risques, KPI), réalisation d\'un Business Case. Phase de préparation : génération d\'un cahier des charges fonctionnel, d\'un Product Backlog, d\'un WBS/OT (Work Breakdown Structure), d\'une matrice RACI, d\'un planning et d\'un budget. Phase d\'exécution : aide à la conception et au maquettage, génération de comptes-rendus de réunions avec actions et décisions, préparation de négociations avec parties prenantes, aide à la prise de décision en Comité de Pilotage (Copil), résolution de conflits, conduite du changement assistée. Phase de clôture : génération de supports de formation utilisateurs, synthèse de Retour d\'Expérience (REX). Prompt Engineering structuré : Master Prompt de fiche de cadrage, itérations, chaînage de prompts. Bonnes pratiques : objectif avant le 1er prompt, ajustement itératif, gestion des risques IA (confidentialité des données, biais, hallucinations). Animation d\'ateliers IA & gestion de projet.',
  '{"domaine": "IA & Management de projet", "outils": ["ChatGPT", "Claude", "Microsoft Copilot", "Copilot for Microsoft 365", "Notion AI", "Prompt Engineering"], "usages": ["Fiche de cadrage", "Business Case", "RACI", "WBS", "Backlog", "Compte-rendu", "REX", "Atelier IA"]}',
  2026,
  'intelligence artificielle, IA, AI, ChatGPT, Claude, Microsoft Copilot, Copilot M365, Notion AI, prompt engineering, prompting, LLM, GPT, management projet IA, cadrage projet IA, RACI, WBS, backlog, compte rendu IA, atelier IA, formation IA, REX, fiche cadrage, Copilot, automatisation projet, hallucination, biais IA',
  1
);
