<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gaëlle FAY - Directrice Transformation SI (IA). Experte en transformation digitale, architecture d'entreprise et gestion de projet.">
    <meta name="keywords" content="transformation digitale, IA, gestion de projet, architecture d'entreprise, conseil, formation">
    <meta name="author" content="Gaëlle FAY">
    
    <!-- Forcer la mise à jour du cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>Gaëlle FAY - Présidente GALYN'UP | CIO ADVISORY</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="images/galynup-logo.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/galynup-logo-180.png">
    
    <link rel="stylesheet" href="css/style.css?v=1.1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
</head>
<body class="page-index">

    <!-- Cookie Consent Banner -->
    <div id="cookieBanner" class="cookie-banner" style="display: none;">
        <div class="cookie-content">
            <div class="cookie-text">
                <h3>🍪 Gestion des cookies</h3>
                <p>Nous utilisons des cookies pour améliorer votre expérience et analyser le trafic de notre site. Vous pouvez accepter, refuser ou personnaliser vos préférences.</p>
            </div>
            <div class="cookie-buttons">
                <button id="acceptCookies" class="btn btn-primary">Accepter</button>
                <button id="refuseCookies" class="btn btn-outline">Refuser</button>
                <button id="customizeCookies" class="btn btn-outline">Personnaliser</button>
            </div>
        </div>
    </div>

    <!-- Cookie Preferences Modal -->
    <div id="cookieModal" class="cookie-modal" style="display: none;">
        <div class="cookie-modal-content">
            <div class="cookie-modal-header">
                <h2>Préférences des cookies</h2>
                <button id="closeCookieModal" class="cookie-close">&times;</button>
            </div>
            <div class="cookie-modal-body">
                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <h3>Cookies essentiels</h3>
                        <label class="cookie-switch">
                            <input type="checkbox" checked disabled>
                            <span class="cookie-slider"></span>
                        </label>
                    </div>
                    <p>Ces cookies sont nécessaires au fonctionnement du site et ne peuvent pas être désactivés.</p>
                </div>
                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <h3>Cookies analytiques</h3>
                        <label class="cookie-switch">
                            <input type="checkbox" id="analyticsCookies">
                            <span class="cookie-slider"></span>
                        </label>
                    </div>
                    <p>Ces cookies nous permettent de mesurer l'audience et d'améliorer notre site (Google Analytics).</p>
                </div>
            </div>
            <div class="cookie-modal-footer">
                <button id="saveCookiePreferences" class="btn btn-primary">Enregistrer mes préférences</button>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="nav-brand">
                    <img src="images/galynup-logo-180.png" alt="GALYN'UP Logo" class="nav-logo">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <div class="nav-menu" id="navMenu">
                    <a href="#about" class="nav-link">À propos</a>
                    <a href="#competences" class="nav-link">Compétences</a>
                    <a href="#prestations" class="nav-link">Prestations</a>
                    <a href="#realisations" class="nav-link">Réalisations</a>
                    <a href="#recommandations" class="nav-link">Recommandations</a>
                    <a href="#contact" class="nav-link">Contact</a>
                    <a href="simulateur-llm.php" class="nav-link" style="color:#D3A625;font-weight:500;">🧮 Simulateur LLM</a>
                    <!-- Boutons mobiles -->
                    <div class="nav-cta-group-mobile">
                        <a href="#contact" class="btn btn-primary-mobile">Demander un devis</a>
                        <a href="ajouter-recommandation.html" target="_blank" class="btn btn-secondary-mobile">Ajouter une recommandation</a>
                    </div>
                </div>
                <div class="nav-cta-group">
                    <a href="#contact" class="btn btn-primary">Demander un devis</a>
                    <a href="ajouter-recommandation.html" target="_blank" class="btn btn-secondary-subtle">Ajouter une recommandation</a>
                </div>
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content-new">
                <!-- Branding + Image -->
                <div class="hero-brand-row">
                    <div class="brand-container">
                        <h1 class="brand-name">GALYN'UP</h1>
                        <p class="brand-subtitle">CIO ADVISORY</p>
                        <p class="positioning-text">DSI Advisory | Stratégie SI & IA | Transformation Digitale</p>
                    </div>
                    <div class="hero-brand-image">
                        <img src="images/hero-ai-governance.png" alt="AI-powered IT Governance and Strategy">
                    </div>
                </div>

                <!-- À propos -->
                <div class="about-content">
                    <div class="about-photo">
                        <img src="images/gaelle-fay-photo.jpg" alt="Gaëlle FAY" class="profile-photo">
                    </div>
                    <div class="about-text-wrapper">
                        <h3 class="about-name">Gaëlle FAY</h3>
                        <p class="about-text">
                            Présidente de GALYN'UP et CIO ADVISORY, j'accompagne les organisations dans leur transformation numérique en apportant une expertise de Direction des Systèmes d'Information. Mes certifications Strategy@HEC, TOGAF®10, ITIL®4, complétées par PMP®, ACP® et ASF, me donnent une vision stratégique globale de l'alignement entre le SI, les services IT et les objectifs business de l'entreprise. Forte de plus de 20 ans d'expérience en gouvernance IT et pilotage des systèmes d'information, j'apporte une approche holistique alliant vision stratégique, architecture d'entreprise et excellence opérationnelle.
                            <br><br>
                            Auteure et créatrice de contenu, je conçois des méthodologies projet/produit augmentées par l'IA, avec une approche centrée sur les cas d'usage concrets et la vision stratégique. J'ai piloté des initiatives de transformation digitale dans les secteurs bancaire, finance, assurance, public et conseil, en coordonnant des équipes de plus de 150 personnes et des budgets supérieurs à 20 millions d'euros par an.
                            <br><br>
                            Passionnée par la transmission de connaissances, je suis auteure du livre <a href="https://www.boutique.afnor.org/fr-fr/livre/lia-au-service-de-la-gestion-de-projet-transformons-nos-pratiques/fa213549/443550" target="_blank" rel="noopener noreferrer" class="text-link">"L'IA au service de la gestion de projet, transformons nos pratiques"</a> publié aux éditions AFNOR. Je rédige également des articles pour le <a href="https://blog-gestion-de-projet.com/gaelle-fay/" target="_blank" rel="noopener noreferrer" class="text-link">Blog Gestion De Projet (BGDP)</a> et je crée des contenus pédagogiques à travers mon Live Café IA et ma chaîne YouTube <a href="https://www.youtube.com/@MindGaëlle" target="_blank" rel="noopener noreferrer" class="text-link">MindGaëlle</a>, où je partage mon expertise sur l'intelligence artificielle appliquée à la gestion de projet.
                        </p>
                    </div>
                </div>

                <!-- Strategic Keywords Carousel -->
                <div class="keywords-carousel">
                    <div class="keyword-carousel-item active" data-keyword="0">
                        <svg class="keyword-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                        <span class="keyword-text">Gouvernance IT</span>
                    </div>
                    <div class="keyword-carousel-item" data-keyword="1">
                        <svg class="keyword-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M3 9h18M9 21V9"/>
                        </svg>
                        <span class="keyword-text">Architecture d'Entreprise</span>
                    </div>
                    <div class="keyword-carousel-item" data-keyword="2">
                        <svg class="keyword-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2a10 10 0 0 0-9.95 9h11.64L9.74 7.05a1 1 0 0 1 1.41-1.41l5.66 5.65a1 1 0 0 1 0 1.42l-5.66 5.65a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41L13.69 13H2.05A10 10 0 1 0 12 2z"/>
                        </svg>
                        <span class="keyword-text">Transformation IA</span>
                    </div>
                    <div class="keyword-carousel-item" data-keyword="3">
                        <svg class="keyword-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M2 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
                        </svg>
                        <span class="keyword-text">Stratégie Digitale</span>
                    </div>
                </div>

                <!-- CTA -->
                <div class="cta-section">
                    <a href="#prestations" class="cta-button-primary">
                        Découvrir mes prestations
                        <svg class="cta-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>



    <!-- Certifications Section -->
    <section id="competences" class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Compétences</h2>
                <p class="section-description">
                    Découvrez en détail mes certifications et les compétences associées pour accompagner votre transformation.
                </p>
            </div>

            <div class="certifications-grid">
                <!-- Certification 1 -->
                <div class="certification-card">
                    <div class="certification-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <h3 class="certification-title">Strategy@HEC</h3>
                    <p class="certification-badge-text">Stratégie d'entreprise - HEC Paris (24 semaines)</p>
                    <p class="certification-description">
                        Formation approfondie en stratégie d'entreprise dispensée par HEC Paris, l'une des meilleures business schools au monde.
                    </p>
                    <ul class="certification-features">
                        <li>Analyse stratégique et diagnostic d'entreprise</li>
                        <li>Formulation et déploiement de stratégies corporate</li>
                        <li>Alignement stratégique SI/Business</li>
                        <li>Gouvernance d'entreprise et pilotage stratégique</li>
                    </ul>
                </div>

                <!-- Certification 2 -->
                <div class="certification-card">
                    <div class="certification-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M3 9h18M9 21V9"/>
                        </svg>
                    </div>
                    <h3 class="certification-title">TOGAF®10</h3>
                    <p class="certification-badge-text">Architecture d'entreprise - The Open Group</p>
                    <p class="certification-description">
                        Certification en architecture d'entreprise selon le framework TOGAF, référence mondiale pour structurer et gouverner l'architecture des systèmes d'information.
                    </p>
                    <ul class="certification-features">
                        <li>Architecture d'entreprise et urbanisation du SI</li>
                        <li>Méthode ADM (Architecture Development Method)</li>
                        <li>Gouvernance et gestion du portefeuille d'architecture</li>
                        <li>Alignement architecture/stratégie business</li>
                    </ul>
                </div>

                <!-- Certification 3 -->
                <div class="certification-card">
                    <div class="certification-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
                        </svg>
                    </div>
                    <h3 class="certification-title">ITIL®4</h3>
                    <p class="certification-badge-text">Gestion des services IT - AXELOS</p>
                    <p class="certification-description">
                        Framework de référence pour la gestion des services IT et l'excellence opérationnelle.
                    </p>
                    <ul class="certification-features">
                        <li>Gestion des services IT et ITSM</li>
                        <li>Chaîne de valeur des services (Service Value Chain)</li>
                        <li>Pratiques ITIL et amélioration continue</li>
                        <li>Gouvernance IT et gestion des opérations</li>
                    </ul>
                </div>

                <!-- Certification 4 -->
                <div class="certification-card">
                    <div class="certification-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3v18h18"/>
                            <path d="M18 17V9M13 17v-6M8 17v-3"/>
                        </svg>
                    </div>
                    <h3 class="certification-title">PMP®</h3>
                    <p class="certification-badge-text">Project Management Professional - PMI (Above Target)</p>
                    <p class="certification-description">
                        Certification du PMI, référence mondiale en management de projet. Obtenue avec mention "Above Target".
                    </p>
                    <ul class="certification-features">
                        <li>Management de projets complexes (budget 20 M€/an)</li>
                        <li>Coordination de projets transverses (>150 personnes)</li>
                        <li>Gestion de portefeuille projets et gouvernance</li>
                        <li>Leadership et management d'équipes pluridisciplinaires</li>
                    </ul>
                </div>

                <!-- Certification 5 -->
                <div class="certification-card">
                    <div class="certification-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="7.5 4.21 12 6.81 16.5 4.21"/>
                            <polyline points="7.5 19.79 7.5 14.6 3 12"/>
                            <polyline points="21 12 16.5 14.6 16.5 19.79"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <h3 class="certification-title">ACP®</h3>
                    <p class="certification-badge-text">Agile Certified Practitioner - PMI</p>
                    <p class="certification-description">
                        Expertise dans les méthodologies agiles et leur application dans des contextes projets variés.
                    </p>
                    <ul class="certification-features">
                        <li>Méthodologies agiles (Scrum, Kanban, Lean)</li>
                        <li>Pilotage de projets en mode hybride</li>
                        <li>Facilitation et coaching d'équipes agiles</li>
                        <li>Amélioration continue et rétrospectives</li>
                    </ul>
                </div>

                <!-- Certification 6 -->
                <div class="certification-card">
                    <div class="certification-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <h3 class="certification-title">ASF</h3>
                    <p class="certification-badge-text">Agile Scrum Foundation - Exin</p>
                    <p class="certification-description">
                        Compréhension des principes fondamentaux de Scrum et de l'agilité.
                    </p>
                    <ul class="certification-features">
                        <li>Framework Scrum (rôles, événements, artefacts)</li>
                        <li>Principes et valeurs agiles</li>
                        <li>Cérémonies Scrum (Sprint Planning, Daily, Review, Retrospective)</li>
                        <li>Collaboration et auto-organisation d'équipe</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Prestations Section -->
    <section id="prestations" class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Prestations</h2>
                <p class="section-description">
                    Découvrez mes offres d'accompagnement pour transformer votre fonction SI et accélérer votre stratégie IA.
                </p>
            </div>

            <div class="prestations-grid">
                <!-- Prestation 1 -->
                <div class="prestation-card featured">
                    <div class="prestation-badge">⭐ Phare</div>
                    <div class="prestation-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <h3 class="prestation-title">Direction SI - Conseil Stratégique</h3>
                    <p class="prestation-description">
                        Accompagnement stratégique et opérationnel en qualité de DSI de transition ou à temps partagé pour piloter votre fonction SI et aligner vos investissements IT avec vos objectifs business.
                    </p>
                    <ul class="prestation-features">
                        <li>DSI à temps partagé</li>
                        <li>Mission de transition</li>
                        <li>Audit stratégique SI</li>
                        <li>Schéma directeur</li>
                    </ul>
                </div>

                <!-- Prestation 2 -->
                <div class="prestation-card featured">
                    <div class="prestation-badge nouveau">🆕 Nouveau</div>
                    <div class="prestation-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                    </div>
                    <h3 class="prestation-title">Stratégie IA & Innovation</h3>
                    <p class="prestation-description">
                        Définition et mise en œuvre de votre stratégie IA, de l'identification des cas d'usage à forte valeur jusqu'à l'industrialisation, avec une approche éthique et responsable.
                    </p>
                    <ul class="prestation-features">
                        <li>Élaboration de stratégie IA</li>
                        <li>Identification cas d'usage</li>
                        <li>Accompagnement POC</li>
                        <li>Gouvernance IA</li>
                    </ul>
                </div>

                <!-- Simulateur LLM -->
                <div class="prestation-card">
                    <div class="prestation-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <h3 class="prestation-title">Simulateur Coût LLM</h3>
                    <p class="prestation-description">
                        Comparez le coût mensuel et le taux d'hallucination des principaux LLM pour votre projet IA. Un outil gratuit pour aider les DSI à choisir le bon modèle.
                    </p>
                    <ul class="prestation-features">
                        <li>Comparaison des LLM du marché</li>
                        <li>Estimation du coût mensuel</li>
                        <li>Taux d'hallucination</li>
                        <li>Aide à la décision DSI</li>
                    </ul>
                    <a href="simulateur-llm.php" style="display:inline-block;margin-top:16px;color:#D3A625;font-weight:500;font-size:13px;text-decoration:none;">→ Accéder au simulateur</a>
                </div>

                <!-- Prestation 3 -->
                <div class="prestation-card">
                    <div class="prestation-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                        </svg>
                    </div>
                    <h3 class="prestation-title">Gouvernance IT & Architecture d'Entreprise</h3>
                    <p class="prestation-description">
                        Mise en place ou optimisation de votre gouvernance IT et de votre architecture d'entreprise selon les frameworks TOGAF®10 et ITIL®4.
                    </p>
                    <ul class="prestation-features">
                        <li>Architecture d'entreprise (TOGAF®)</li>
                        <li>Gouvernance SI</li>
                        <li>Gestion services IT (ITIL®)</li>
                        <li>Architecture Data & IA</li>
                    </ul>
                </div>

                <!-- Prestation 4 -->
                <div class="prestation-card">
                    <div class="prestation-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <h3 class="prestation-title">Transformation Digitale & Conduite du Changement</h3>
                    <p class="prestation-description">
                        Pilotage de vos programmes de transformation digitale avec une approche centrée sur l'humain, de la stratégie à l'adoption des nouvelles technologies.
                    </p>
                    <ul class="prestation-features">
                        <li>Direction de programmes</li>
                        <li>Conduite du changement</li>
                        <li>Adoption IA, Cloud, Data</li>
                        <li>Pilotage de la valeur</li>
                    </ul>
                </div>

                <!-- Prestation 5 -->
                <div class="prestation-card">
                    <div class="prestation-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </div>
                    <h3 class="prestation-title">Formations Professionnelles IT & IA</h3>
                    <p class="prestation-description">
                        Formations sur mesure pour dirigeants, DSI et équipes IT sur la gouvernance IT, la stratégie IA et le pilotage de la transformation digitale.
                    </p>
                    <ul class="prestation-features">
                        <li>Stratégie IA pour dirigeants</li>
                        <li>Gouvernance IT (ITIL)</li>
                        <li>Architecture (TOGAF)</li>
                        <li>Management de projets complexes</li>
                    </ul>
                </div>
            </div>

            <div class="prestations-cta">
                <p>Toutes les prestations sont sur devis selon vos besoins spécifiques.</p>
                <a href="#contact" class="btn btn-primary btn-lg">Demander un devis personnalisé</a>
            </div>
        </div>
    </section>

    <!-- Réalisations Section -->
    <section id="realisations" class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Réalisations</h2>
                <p class="section-description">Missions récentes &mdash; Impact mesurable, contextes complexes</p>
            </div>

            <!-- Roue de compétences interactive -->
            <div class="wheel-container">
                <!-- Filtres mobiles -->
                <div class="wheel-mobile-filters">
                    <button class="filter-btn active" data-domain="all">Tous</button>
                    <button class="filter-btn" data-domain="gouvernance">Gouvernance IT</button>
                    <button class="filter-btn" data-domain="architecture">Architecture</button>
                    <button class="filter-btn" data-domain="transformation">Transformation</button>
                    <button class="filter-btn" data-domain="risques">Risques</button>
                    <button class="filter-btn" data-domain="leadership">Leadership</button>
                </div>
                <!-- Roue SVG desktop -->
                <div class="competence-wheel-wrapper">
                    <svg id="competenceWheel" viewBox="0 0 600 600" class="competence-wheel" role="img" aria-label="Roue de compétences — cliquer un domaine pour filtrer les réalisations">
                        <!-- Pétale 1 : Gouvernance IT -->
                        <g class="wheel-petal" data-domain="gouvernance" tabindex="0" aria-label="Gouvernance IT">
                            <path d="M 207.27 151.60 A 175 175 0 0 1 392.73 151.60 L 342.39 232.16 A 80 80 0 0 0 257.61 232.16 Z" fill="#9333ea"/>
                            <circle cx="300" cy="160" r="6" fill="white" stroke="#9333ea" stroke-width="2"/>
                            <text x="300" y="155" text-anchor="middle" font-family="Roboto,sans-serif" font-size="8" fill="white" font-weight="700">ITIL 4</text>
                            <title>Gouvernance IT — ITIL 4 Foundation</title>
                        </g>
                        <!-- Pétale 2 : Architecture d'Entreprise -->
                        <g class="wheel-petal" data-domain="architecture" tabindex="0" aria-label="Architecture d'Entreprise">
                            <path d="M 412.49 165.95 A 175 175 0 0 1 469.80 342.33 L 377.62 319.35 A 80 80 0 0 0 351.42 238.72 Z" fill="#7e22ce"/>
                            <circle cx="433" cy="257" r="6" fill="white" stroke="#7e22ce" stroke-width="2"/>
                            <text x="424" y="252" text-anchor="middle" font-family="Roboto,sans-serif" font-size="8" fill="white" font-weight="700">TOGAF</text>
                            <title>Architecture d'Entreprise — TOGAF 10 Foundation</title>
                        </g>
                        <!-- Pétale 3 : Transformation Digitale -->
                        <g class="wheel-petal" data-domain="transformation" tabindex="0" aria-label="Transformation Digitale">
                            <path d="M 462.26 365.56 A 175 175 0 0 1 312.22 474.58 L 305.58 379.81 A 80 80 0 0 0 374.18 329.97 Z" fill="#d97706"/>
                            <circle cx="382" cy="413" r="6" fill="white" stroke="#d97706" stroke-width="2"/>
                            <text x="382" y="408" text-anchor="middle" font-family="Roboto,sans-serif" font-size="8" fill="white" font-weight="700">HEC</text>
                            <title>Transformation Digitale — Stratégie@HEC</title>
                        </g>
                        <!-- Pétale 4 : Gestion des Risques -->
                        <g class="wheel-petal" data-domain="risques" tabindex="0" aria-label="Gestion des Risques">
                            <path d="M 287.78 474.58 A 175 175 0 0 1 137.74 365.56 L 225.82 329.97 A 80 80 0 0 0 294.42 379.81 Z" fill="#b45309"/>
                            <circle cx="218" cy="413" r="6" fill="white" stroke="#b45309" stroke-width="2"/>
                            <text x="218" y="408" text-anchor="middle" font-family="Roboto,sans-serif" font-size="8" fill="white" font-weight="700">PMP</text>
                            <title>Gestion des Risques — PMP</title>
                        </g>
                        <!-- Pétale 5 : Leadership -->
                        <g class="wheel-petal" data-domain="leadership" tabindex="0" aria-label="Leadership">
                            <path d="M 130.20 342.33 A 175 175 0 0 1 187.51 165.95 L 248.58 238.72 A 80 80 0 0 0 222.38 319.35 Z" fill="#c026d3"/>
                            <circle cx="167" cy="257" r="6" fill="white" stroke="#c026d3" stroke-width="2"/>
                            <text x="175" y="252" text-anchor="middle" font-family="Roboto,sans-serif" font-size="8" fill="white" font-weight="700">PMI-ACP</text>
                            <title>Leadership — PMI-ACP + Agile Scrum Foundation</title>
                        </g>
                        <!-- Étiquettes externes -->
                        <text text-anchor="middle" font-family="Roboto,sans-serif">
                            <tspan x="300" y="97" font-size="12" font-weight="700" fill="#9333ea">Gouvernance IT</tspan>
                            <tspan x="300" dy="15" font-size="10" fill="#6b7280">ITIL 4 Foundation</tspan>
                        </text>
                        <text text-anchor="middle" font-family="Roboto,sans-serif">
                            <tspan x="487" y="232" font-size="12" font-weight="700" fill="#7e22ce">Architecture</tspan>
                            <tspan x="487" dy="15" font-size="10" fill="#6b7280">TOGAF 10</tspan>
                        </text>
                        <text text-anchor="middle" font-family="Roboto,sans-serif">
                            <tspan x="422" y="467" font-size="12" font-weight="700" fill="#d97706">Transformation</tspan>
                            <tspan x="422" dy="15" font-size="10" fill="#6b7280">Stratégie@HEC</tspan>
                        </text>
                        <text text-anchor="middle" font-family="Roboto,sans-serif">
                            <tspan x="178" y="467" font-size="12" font-weight="700" fill="#b45309">Gestion des Risques</tspan>
                            <tspan x="178" dy="15" font-size="10" fill="#6b7280">PMP</tspan>
                        </text>
                        <text text-anchor="middle" font-family="Roboto,sans-serif">
                            <tspan x="113" y="232" font-size="12" font-weight="700" fill="#c026d3">Leadership</tspan>
                            <tspan x="113" dy="15" font-size="10" fill="#6b7280">PMI-ACP + ASF</tspan>
                        </text>
                        <!-- Cercle central -->
                        <circle id="wheelCenter" cx="300" cy="300" r="80" fill="white" stroke="#9333ea" stroke-width="2" style="cursor:pointer;"/>
                        <text text-anchor="middle" font-family="Roboto,sans-serif" pointer-events="none">
                            <tspan x="300" y="291" font-size="11.5" font-weight="700" fill="#1f2937">Gaëlle Fay</tspan>
                            <tspan x="300" y="307" font-size="10" font-weight="600" fill="#9333ea">CIO Advisory</tspan>
                            <tspan x="300" y="321" font-size="8" fill="#9ca3af">↺ tout afficher</tspan>
                        </text>
                    </svg>
                </div>
            </div>

            <!-- Grille de cartes (condensées) -->
            <div class="realisations-grid" id="realisationsGrid">

                <!-- Carte 1 — AFNOR -->
                <div class="realisation-card" data-domain="gouvernance">
                    <span class="realisation-domain-tag" style="background:#9333ea;">Gouvernance IT</span>
                    <h3 class="realisation-card-title">Gouvernance IT &amp; Pilotage de la DSI</h3>
                    <p class="realisation-card-client">AFNOR Groupe — Secteur public</p>
                    <p class="realisation-card-duration">8 semaines | Août / Oct 2025</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">8 sem. mission</span>
                        <span class="realisation-kpi">10 collaborateurs senior</span>
                        <span class="realisation-kpi">4 chantiers simultanés</span>
                    </div>
                    <p class="realisation-card-result">En 8 semaines : diagnostic livré, département structuré, delivery fluidifié, Design Authority validée, tableau de bord C-Level opérationnel.</p>
                </div>

                <!-- Carte 2 — ANAH Architecture -->
                <div class="realisation-card" data-domain="architecture">
                    <span class="realisation-domain-tag" style="background:#7e22ce;">Architecture d'Entreprise</span>
                    <h3 class="realisation-card-title">Architecture d'Entreprise (TOGAF 10)</h3>
                    <p class="realisation-card-client">ANAH — Service Architecture d'Entreprise</p>
                    <p class="realisation-card-duration">8 mois | Jan / Août 2025</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">14 architectes fédérés</span>
                        <span class="realisation-kpi">2 projets stratégiques</span>
                        <span class="realisation-kpi">Métiers inclus</span>
                    </div>
                    <p class="realisation-card-result">14 architectes fédérés. Visibilité étendue aux métiers. Deux projets majeurs contribués dans les délais.</p>
                </div>

                <!-- Carte 3 — ANAH Transformation -->
                <div class="realisation-card" data-domain="transformation">
                    <span class="realisation-domain-tag" style="background:#d97706;">Transformation Digitale</span>
                    <h3 class="realisation-card-title">Transformation Digitale &amp; Interface Stratégique</h3>
                    <p class="realisation-card-client">ANAH — MaPrimeRénov', MaPrimeAdapt'</p>
                    <p class="realisation-card-duration">20 mois | Mars 2023 / Nov 2024</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">50 000 dossiers/jour</span>
                        <span class="realisation-kpi">100+ parties prenantes</span>
                        <span class="realisation-kpi">0 rupture de service</span>
                    </div>
                    <p class="realisation-card-result">Continuité garantie. Conformité maintenue. Obsolescence livrée. Réorganisation accompagnée malgré les résistances.</p>
                </div>

                <!-- Carte 4 — SG Leasing -->
                <div class="realisation-card" data-domain="transformation">
                    <span class="realisation-domain-tag" style="background:#d97706;">Transformation Digitale</span>
                    <h3 class="realisation-card-title">Cadrage Programme International — Leasing</h3>
                    <p class="realisation-card-client">Société Générale CIB</p>
                    <p class="realisation-card-duration">6 mois | Mars / Oct 2022</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">80+ parties prenantes</span>
                        <span class="realisation-kpi">4 pays</span>
                        <span class="realisation-kpi">5-10M€ validés</span>
                    </div>
                    <p class="realisation-card-result">Scénario validé en 6 mois. Consensus obtenu malgré l'historique d'échec. Recommandation directe pour le programme ESG.</p>
                </div>

                <!-- Carte 5 — SG ESG -->
                <div class="realisation-card" data-domain="risques">
                    <span class="realisation-domain-tag" style="background:#b45309;">Gestion des Risques</span>
                    <h3 class="realisation-card-title">Gouvernance Data ESG</h3>
                    <p class="realisation-card-client">Société Générale CIB — Programme ESG by Design</p>
                    <p class="realisation-card-duration">5 mois | Oct 2022 / Fév 2023</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">50+ parties prenantes</span>
                        <span class="realisation-kpi">4 streams</span>
                        <span class="realisation-kpi">0 base documentaire</span>
                    </div>
                    <p class="realisation-card-result">Gouvernance structurée en 5 mois. Bases transmises. Départ unanimement regretté.</p>
                </div>

                <!-- Carte 6 — BNP Data Quality -->
                <div class="realisation-card" data-domain="risques">
                    <span class="realisation-domain-tag" style="background:#b45309;">Gestion des Risques</span>
                    <h3 class="realisation-card-title">Data Quality &amp; Protection du Business</h3>
                    <p class="realisation-card-client">BNP Paribas CIB — Data Quality KYC</p>
                    <p class="realisation-card-duration">4 mois | Août / Déc 2018</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">3 régions</span>
                        <span class="realisation-kpi">Global Markets protégé</span>
                        <span class="realisation-kpi">Décommissionnement annulé</span>
                    </div>
                    <p class="realisation-card-result">Décommissionnement annulé. Global Markets protégé. Mission Fenergo obtenue par reconnaissance de la rigueur.</p>
                </div>

                <!-- Carte 7 — BNP KYC -->
                <div class="realisation-card" data-domain="risques">
                    <span class="realisation-domain-tag" style="background:#b45309;">Gestion des Risques</span>
                    <h3 class="realisation-card-title">KYC, Conformité &amp; Data Privacy</h3>
                    <p class="realisation-card-client">BNP Paribas CIB — Programme CLM (Fenergo)</p>
                    <p class="realisation-card-duration">14 mois | Août 2018 / Nov 2019</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">3 juridictions</span>
                        <span class="realisation-kpi">10 consultants</span>
                        <span class="realisation-kpi">PIA validé</span>
                    </div>
                    <p class="realisation-card-result">Périmètre structuré. RGPD tracé. Multi-juridictions intégrés. Facilitatrice reconnue au-delà du projet.</p>
                </div>

                <!-- Carte 8 — SIACI -->
                <div class="realisation-card" data-domain="leadership">
                    <span class="realisation-domain-tag" style="background:#c026d3;">Leadership</span>
                    <h3 class="realisation-card-title">Leadership &amp; Conduite du Changement</h3>
                    <p class="realisation-card-client">SIACI SAINT HONORÉ — Dispenses de frais de santé</p>
                    <p class="realisation-card-duration">2 ans | Déc 2019 / Nov 2021</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">3M€ revenus</span>
                        <span class="realisation-kpi">54 000+ salariés</span>
                        <span class="realisation-kpi">1 framework créé</span>
                    </div>
                    <p class="realisation-card-result">3 déploiements livrés. 3M€ de revenus pour SIACI. Framework transmis. Deux directeurs ont cherché à retenir.</p>
                </div>

                <!-- Carte 9 — Entrepreneuriat -->
                <div class="realisation-card" data-domain="leadership">
                    <span class="realisation-domain-tag" style="background:#c026d3;">Leadership</span>
                    <h3 class="realisation-card-title">Leadership Entrepreneurial — Différenciation CIO</h3>
                    <p class="realisation-card-client">TPE &amp; Association des Commerçants</p>
                    <p class="realisation-card-duration">5 ans | 2013-2018 — Région parisienne</p>
                    <div class="realisation-kpis">
                        <span class="realisation-kpi">12 salariés</span>
                        <span class="realisation-kpi">900K€ CA annuel</span>
                        <span class="realisation-kpi">80 commerces</span>
                    </div>
                    <p class="realisation-card-result">Compétences rares pour un profil CIO : gestion de crise, négociation, management, pilotage budgétaire. Lauréate CCI Essonne.</p>
                </div>

            </div>

            <div class="realisations-cta">
                <a href="realisations.html" class="btn btn-primary btn-lg">Voir toutes mes réalisations →</a>
            </div>
        </div>
    </section>

    <!-- Recommandations Section -->
    <section id="recommandations" class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Recommandations</h2>
                <p class="section-subtitle">Ce que disent mes clients et partenaires</p>
            </div>
            
            <!-- Compteur et filtres -->
            <div class="recommandations-header">
                <div class="recommandations-counter" id="recommendationsCounter"></div>
                <div class="recommandations-filters">
                    <select id="sortFilter" class="sort-select">
                        <option value="recent">Plus récentes</option>
                        <option value="best-rated">Meilleures notes</option>
                        <option value="oldest">Plus anciennes</option>
                    </select>
                </div>
            </div>

            <div class="recommandations-grid" id="recommendationsGrid">
                <?php
                // Inclure le script de chargement des recommandations
                require_once 'php/get-recommendations.php';
                
                // Charger et afficher les recommandations approuvées
                $recommendations = getApprovedRecommendations();
                
                if (empty($recommendations)) {
                    // Si aucune recommandation approuvée, afficher les exemples par défaut
                    echo '<!-- Aucune recommandation approuvée - Affichage des exemples par défaut -->';
                ?>
                <!-- Recommandation 1 (exemple par défaut) -->
                <div class="recommandation-card">
                    <div class="recommandation-header">
                        <div class="recommandation-avatar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="recommandation-info">
                            <h3 class="recommandation-name">Jean Dupont</h3>
                            <p class="recommandation-title">Directeur des Systèmes d'Information</p>
                            <p class="recommandation-company">Banque de France</p>
                        </div>
                    </div>

                    <div class="recommandation-ratings">
                        <div class="rating-item">
                            <span class="rating-label">Leadership</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Direction de projet</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Stratégie</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Méthodologie</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Gouvernance</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-overall">
                            <span class="rating-score">5.0</span>
                            <span class="rating-max">/5</span>
                        </div>
                    </div>

                    <div class="recommandation-text">
                        <p>"Gaëlle a dirigé avec brio notre transformation digitale. Son expertise en gouvernance IT et sa capacité à fédérer les équipes ont été déterminantes dans la réussite de ce projet stratégique de 20M€."</p>
                    </div>
                </div>

                <!-- Recommandation 2 -->
                <div class="recommandation-card">
                    <div class="recommandation-header">
                        <div class="recommandation-avatar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="recommandation-info">
                            <h3 class="recommandation-name">Marie Martin</h3>
                            <p class="recommandation-title">Chief Digital Officer</p>
                            <p class="recommandation-company">Groupe Assurance Mutuelle</p>
                        </div>
                    </div>

                    <div class="recommandation-ratings">
                        <div class="rating-item">
                            <span class="rating-label">Leadership</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Direction de projet</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Stratégie</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Méthodologie</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Gouvernance</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-overall">
                            <span class="rating-score">5.0</span>
                            <span class="rating-max">/5</span>
                        </div>
                    </div>

                    <div class="recommandation-text">
                        <p>"Une experte reconnue en stratégie IA. Gaëlle a su nous accompagner dans l'identification et le déploiement de cas d'usage concrets, avec une approche pragmatique et orientée résultats."</p>
                    </div>
                </div>

                <!-- Recommandation 3 -->
                <div class="recommandation-card">
                    <div class="recommandation-header">
                        <div class="recommandation-avatar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="recommandation-info">
                            <h3 class="recommandation-name">Pierre Dubois</h3>
                            <p class="recommandation-title">Directeur de Programme</p>
                            <p class="recommandation-company">Ministère de l'Économie</p>
                        </div>
                    </div>

                    <div class="recommandation-ratings">
                        <div class="rating-item">
                            <span class="rating-label">Leadership</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Direction de projet</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Stratégie</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Méthodologie</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                            </div>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Gouvernance</span>
                            <div class="stars">
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star filled">★</span>
                                <span class="star">★</span>
                            </div>
                        </div>
                        <div class="rating-overall">
                            <span class="rating-score">4.8</span>
                            <span class="rating-max">/5</span>
                        </div>
                    </div>

                    <div class="recommandation-text">
                        <p>"Gaëlle maîtrise parfaitement les méthodologies projet (PMP, ACP) et sait les adapter au contexte. Sa formation sur les méthodologies agiles augmentées par l'IA a été très appréciée par nos équipes."</p>
                    </div>
                </div>
                <?php
                } else {
                    // Afficher les recommandations approuvées depuis la base de données
                    foreach ($recommendations as $rec) {
                        echo generateRecommendationCard($rec);
                    }
                }
                ?>
            </div>
            
            <!-- Contrôles du carrousel (desktop uniquement) -->
            <div class="carousel-controls">
                <button class="carousel-btn carousel-prev" aria-label="Recommandation précédente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>
                <div class="carousel-dots"></div>
                <button class="carousel-btn carousel-next" aria-label="Recommandation suivante">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Demander un devis</h2>
                <p class="section-description">
                    Vous avez un projet ? Contactez-moi pour discuter de vos besoins et obtenir un devis personnalisé.
                </p>
            </div>

            <div class="contact-wrapper">
                <div class="contact-form-card">
                    <form id="contactForm" action="php/send-email.php" method="POST">
                        <div class="form-group">
                            <label for="fullName" class="form-label">Nom complet *</label>
                            <input type="text" id="fullName" name="fullName" class="form-input" placeholder="Votre nom et prénom" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="votre.email@exemple.fr" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="06 12 34 56 78">
                        </div>

                        <div class="form-group">
                            <label for="company" class="form-label">Entreprise</label>
                            <input type="text" id="company" name="company" class="form-input" placeholder="Nom de votre entreprise">
                        </div>

                        <div class="form-group">
                            <label for="serviceType" class="form-label">Type de prestation *</label>
                            <select id="serviceType" name="serviceType" class="form-input" required>
                                <option value="">Sélectionnez une prestation</option>
                                <optgroup label="⭐ Prestations Phares">
                                    <option value="Direction SI - Conseil Stratégique">💼 Direction des Systèmes d'Information - Conseil Stratégique</option>
                                    <option value="Stratégie IA & Innovation">🆕 Stratégie Intelligence Artificielle & Innovation</option>
                                </optgroup>
                                <optgroup label="🎯 Gouvernance & Architecture">
                                    <option value="Gouvernance IT & Architecture d'Entreprise">Gouvernance IT & Architecture d'Entreprise</option>
                                </optgroup>
                                <optgroup label="🚀 Transformation">
                                    <option value="Transformation Digitale & Conduite du Changement">Transformation Digitale & Accompagnement du Changement</option>
                                </optgroup>
                                <optgroup label="🎓 Formation">
                                    <option value="Formations Professionnelles IT & IA">Formations Professionnelles IT & IA</option>
                                </optgroup>
                                <optgroup label="Autre">
                                    <option value="Autre prestation">Autre prestation (préciser dans le message)</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Description de votre projet *</label>
                            <textarea id="message" name="message" class="form-input form-textarea" placeholder="Décrivez votre projet, vos besoins et vos objectifs..." rows="6" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-full">Envoyer ma demande</button>
                    </form>

                    <div id="formMessage" class="form-message" style="display: none;"></div>

                    <div class="contact-info">
                        <h3 class="contact-info-title">Ou contactez-moi directement</h3>
                        <div class="contact-item">
                            <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <path d="M22 6l-10 7L2 6"/>
                            </svg>
                            <a href="mailto:gaelle.fay@galynup.fr">gaelle.fay@galynup.fr</a>
                        </div>
                        <div class="contact-item">
                            <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                            <a href="https://www.linkedin.com/in/gaellefay/" target="_blank" rel="noopener noreferrer">Profil LinkedIn</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <svg class="footer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                    <span>GALYN'UP</span>
                </div>
                
                <div class="footer-legal">
                    
                    <p class="footer-text">Hébergeur : OVH SAS - 2 rue Kellermann, 59100 Roubaix - Tél : 1007</p>
                </div>

                <div class="footer-links">
                    <a href="mentions-legales.html">Mentions légales</a>
                    <span class="footer-separator">|</span>
                    <a href="politique-confidentialite.html">Politique de confidentialité</a>
                    <span class="footer-separator">|</span>
                    <a href="#" id="manageCookies">Gérer les cookies</a>
                </div>

                <p class="footer-copyright">© <span id="currentYear"></span> GALYN'UP - Tous droits réservés</p>
            </div>
        </div>
    </footer>

    <!-- Bouton devis flottant -->
    <a href="#contact" id="floatingQuote" class="floating-quote-btn" aria-label="Demander un devis">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            <path d="M8 10h8M8 14h4"/>
        </svg>
        <span>Demander un devis</span>
    </a>

    <!-- Bouton flottant en etoile pour ajouter une recommandation (mobile) -->
    <a href="ajouter-recommandation.html" target="_blank" id="floatingRecommendation" class="floating-recommendation-btn" aria-label="Ajouter une recommandation">
        <svg viewBox="0 0 24 24" fill="currentColor" stroke="none">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
    </a>

    <!-- Bouton retour en haut -->
    <button id="backToTop" class="back-to-top" aria-label="Retour en haut">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 19V5M5 12l7-7 7 7"/>
        </svg>
    </button>

    <script src="js/script.js"></script>
    <script>
    // Roue de compétences interactive
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            var petals = document.querySelectorAll('#competenceWheel .wheel-petal');
            var filterBtns = document.querySelectorAll('#realisations .filter-btn');
            var cards = document.querySelectorAll('#realisationsGrid .realisation-card');
            var wheelCenter = document.getElementById('wheelCenter');
            var activeFilter = 'all';

            if (!petals.length && !filterBtns.length) return;

            function setFilter(domain) {
                if (domain === activeFilter && domain !== 'all') {
                    domain = 'all';
                }
                activeFilter = domain;

                petals.forEach(function(p) {
                    if (domain === 'all') {
                        p.classList.remove('active', 'dimmed');
                    } else if (p.dataset.domain === domain) {
                        p.classList.add('active');
                        p.classList.remove('dimmed');
                    } else {
                        p.classList.add('dimmed');
                        p.classList.remove('active');
                    }
                });

                filterBtns.forEach(function(b) {
                    var match = b.dataset.domain === domain || (domain === 'all' && b.dataset.domain === 'all');
                    b.classList.toggle('active', match);
                });

                cards.forEach(function(c) { c.style.opacity = '0'; });

                setTimeout(function() {
                    cards.forEach(function(c) {
                        var show = domain === 'all' || c.dataset.domain === domain;
                        c.style.display = show ? '' : 'none';
                    });
                    requestAnimationFrame(function() {
                        cards.forEach(function(c) {
                            if (c.style.display !== 'none') c.style.opacity = '1';
                        });
                    });
                }, 220);
            }

            petals.forEach(function(p) {
                p.addEventListener('click', function() { setFilter(p.dataset.domain); });
                p.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setFilter(p.dataset.domain); }
                });
            });

            if (wheelCenter) {
                wheelCenter.addEventListener('click', function() { setFilter('all'); });
            }

            filterBtns.forEach(function(b) {
                b.addEventListener('click', function() { setFilter(b.dataset.domain); });
            });
        });
    })();
    </script>

<script src="script.js"></script>
    
    <!-- Carrousel Recommandations -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth < 768) return;
        
        const grid = document.querySelector('.recommandations-grid');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const dotsContainer = document.getElementById('carouselDots');
        
        if (!grid || !prevBtn || !nextBtn) return;
        
        const cards = grid.querySelectorAll('.recommandation-card');
        const cardsPerPage = 3;
        const totalPages = Math.ceil(cards.length / cardsPerPage);
        let currentPage = 0;
        
        function createDots() {
            dotsContainer.innerHTML = '';
            for (let i = 0; i < totalPages; i++) {
                const dot = document.createElement('div');
                dot.className = 'carousel-dot';
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToPage(i));
                dotsContainer.appendChild(dot);
            }
        }
        
        function goToPage(page) {
            if (page < 0 || page >= totalPages) return;
            currentPage = page;
            
            const cardWidth = cards[0].offsetWidth;
            const gap = 32;
            const scrollAmount = page * (cardWidth * cardsPerPage + gap * cardsPerPage);
            
            grid.scrollTo({ left: scrollAmount, behavior: 'smooth' });
            
            document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
                dot.classList.toggle('active', i === page);
            });
            
            prevBtn.disabled = page === 0;
            nextBtn.disabled = page === totalPages - 1;
            prevBtn.style.opacity = prevBtn.disabled ? '0.5' : '1';
            nextBtn.style.opacity = nextBtn.disabled ? '0.5' : '1';
        }
        
        prevBtn.addEventListener('click', () => goToPage(currentPage - 1));
        nextBtn.addEventListener('click', () => goToPage(currentPage + 1));
        
        createDots();
        goToPage(0);
    });
    </script>
<script>
// Rendre "Lire plus" fonctionnel sur TOUTES les tailles d'écran
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.recommandation-text').forEach(function(text) {
        // Rendre cliquable
        text.style.cursor = 'pointer';
        
        text.addEventListener('click', function() {
            // Vérifier si c'est étendu ou non
            const isExpanded = this.getAttribute('data-expanded') === 'true';
            
            if (!isExpanded) {
                // ÉTENDRE
                this.style.webkitLineClamp = 'unset';
                this.style.maxHeight = 'none';
                this.setAttribute('data-expanded', 'true');
            } else {
                // RÉDUIRE
                this.style.webkitLineClamp = '3';
                this.style.maxHeight = '4.8em';
                this.setAttribute('data-expanded', 'false');
            }
        });
    });
});
</script>
</body>
</html>
