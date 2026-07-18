<?php
// NAJPIERW ustaw język Z URL
if (!empty($_GET['lang'])) {
    $_SESSION['lang'] = strtoupper($_GET['lang']);
}

include 'includes/db.php';
include 'includes/lang.php';
include 'includes/header.php';
include 'includes/navbar.php';

$current_lang = $_SESSION['lang'];

// Tłumaczenia dla Regulaminu
$terms_translations = [
    'PL' => [
        'title' => 'Regulamin Serwisu',
        'subtitle' => 'Serwisu internetowego 446DX.PL',
        'effective_date' => 'Obowiązuje od dnia: 01-08-2026',
        'chapter_1' => 'Rozdział I – Postanowienia ogólne',
        'section_1' => '§1. Informacje podstawowe',
        'p1_1' => 'Regulamin określa zasady korzystania z serwisu internetowego <strong>446DX.PL</strong>, zwanego dalej „Serwisem".',
        'p1_2' => 'Administratorem oraz właścicielem Serwisu jest <strong>Właściciel strony 446DX.PL</strong>, zwany dalej „Administratorem".',
        'p1_3' => 'Kontakt z Administratorem odbywa się wyłącznie za pośrednictwem adresu e-mail: <strong>admin@446dx.pl</strong>',
        'p1_4' => 'Serwis jest prowadzony jako prywatny, niekomercyjny projekt społecznościowy skupiający użytkowników pasma PMR446.',
        'p1_5' => 'Celem Serwisu jest umożliwienie użytkownikom wymiany informacji dotyczących aktywności radiowej, propagacji, spotów DX oraz integracja społeczności PMR446.',
        'p1_6' => 'Korzystanie z Serwisu jest dobrowolne i bezpłatne.',
        'p1_7' => 'Założenie konta oznacza akceptację niniejszego Regulaminu.',
    ],
    'EN' => [
        'title' => 'Terms of Service',
        'subtitle' => 'of the website 446DX.PL',
        'effective_date' => 'Effective from: 01-08-2026',
        'chapter_1' => 'Chapter I – General Provisions',
        'section_1' => '§1. Basic Information',
        'p1_1' => 'These Terms of Service regulate the use of the website <strong>446DX.PL</strong>, hereinafter referred to as the "Service".',
        'p1_2' => 'The Administrator and owner of the Service is <strong>Owner of 446DX.PL website</strong>, hereinafter referred to as the "Administrator".',
        'p1_3' => 'Contact with the Administrator is made exclusively via email: <strong>admin@446dx.pl</strong>',
        'p1_4' => 'The Service is operated as a private, non-commercial community project for PMR446 band users.',
        'p1_5' => 'The purpose of the Service is to enable users to exchange information about radio activity, propagation, DX spots and integrate the PMR446 community.',
        'p1_6' => 'Use of the Service is voluntary and free.',
        'p1_7' => 'Creating an account means accepting these Terms of Service.',
    ],
    'DE' => [
        'title' => 'Nutzungsbedingungen',
        'subtitle' => 'der Website 446DX.PL',
        'effective_date' => 'Gültig ab: 01-08-2026',
        'chapter_1' => 'Kapitel I – Allgemeine Bestimmungen',
        'section_1' => '§1. Grundlegende Informationen',
        'p1_1' => 'Diese Nutzungsbedingungen regeln die Nutzung der Website <strong>446DX.PL</strong>, nachstehend als „Service" bezeichnet.',
        'p1_2' => 'Verwalter und Eigentümer des Service ist <strong>Eigentümer der Website 446DX.PL</strong>, nachstehend als „Verwalter" bezeichnet.',
        'p1_3' => 'Die Kontaktaufnahme mit dem Verwalter erfolgt ausschließlich per E-Mail: <strong>admin@446dx.pl</strong>',
        'p1_4' => 'Der Service wird als privates, gemeinnütziges Gemeinschaftsprojekt für Benutzer des PMR446-Bandes betrieben.',
        'p1_5' => 'Der Zweck des Service ist es, Benutzern den Austausch von Informationen über Funkaktivität, Ausbreitungsbedingungen und DX-Spots zu ermöglichen und die PMR446-Gemeinschaft zu integrieren.',
        'p1_6' => 'Die Nutzung des Service ist freiwillig und kostenlos.',
        'p1_7' => 'Das Erstellen eines Kontos bedeutet die Annahme dieser Nutzungsbedingungen.',
    ],
    'FR' => [
        'title' => 'Conditions d\'Utilisation',
        'subtitle' => 'du site web 446DX.PL',
        'effective_date' => 'En vigueur à partir du : 01-08-2026',
        'chapter_1' => 'Chapitre I – Dispositions Générales',
        'section_1' => '§1. Informations Fondamentales',
        'p1_1' => 'Ces Conditions d\'Utilisation régissent l\'utilisation du site web <strong>446DX.PL</strong>, ci-après dénommé le « Service ».',
        'p1_2' => 'L\'Administrateur et propriétaire du Service est <strong>Propriétaire du site web 446DX.PL</strong>, ci-après dénommé l'« Administrateur ».',
        'p1_3' => 'Le contact avec l\'Administrateur s\'effectue exclusivement par email : <strong>admin@446dx.pl</strong>',
        'p1_4' => 'Le Service est exploité en tant que projet communautaire privé et à but non lucratif pour les utilisateurs de la bande PMR446.',
        'p1_5' => 'L\'objectif du Service est de permettre aux utilisateurs d\'échanger des informations sur l\'activité radio, les conditions de propagation, les spots DX et d\'intégrer la communauté PMR446.',
        'p1_6' => 'L\'utilisation du Service est volontaire et gratuite.',
        'p1_7' => 'La création d\'un compte signifie l\'acceptation de ces Conditions d\'Utilisation.',
    ],
];

function ts($key) {
    global $current_lang, $terms_translations;
    return $terms_translations[$current_lang][$key] ?? $key;
}
?>

<style>
.terms-wrapper {
    width: 100%;
    background-color: #0f1419;
    padding: 40px 20px;
    margin: 0;
}

.terms-container {
    max-width: 900px;
    margin: 0 auto;
    background-color: transparent;
    border: 2px solid #ff3b3b;
    border-radius: 8px;
    padding: 40px;
    color: #e5e7eb;
}

.terms-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #ff3b3b;
}

.terms-title {
    font-size: 2.2rem;
    font-weight: 900;
    color: #ff3b3b;
    margin-bottom: 10px;
}

.terms-subtitle {
    font-size: 0.95rem;
    color: #9ca3af;
}

.terms-content {
    line-height: 1.8;
    font-size: 0.95rem;
}

.terms-content h1 {
    color: #ff3b3b;
    margin-top: 30px;
    margin-bottom: 15px;
    font-size: 1.4rem;
}

.terms-content h2 {
    color: #fbbf24;
    margin-top: 20px;
    margin-bottom: 15px;
    font-size: 1.2rem;
}

.terms-content p {
    margin-bottom: 15px;
}

.terms-content ul {
    margin-left: 20px;
    margin-bottom: 15px;
}

.terms-content ol {
    margin-left: 20px;
    margin-bottom: 15px;
}

.terms-content li {
    margin-bottom: 8px;
}

.terms-content strong {
    color: #ff3b3b;
}

.terms-content em {
    color: #fbbf24;
}

.terms-content a {
    color: #10b981;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}

.terms-content a:hover {
    color: #059669;
    border-bottom-color: #10b981;
}

.divider {
    margin-top: 30px;
    border: none;
    border-top: 1px solid rgba(255, 59, 59, 0.3);
}

@media (max-width: 768px) {
    .terms-container {
        padding: 20px;
    }

    .terms-title {
        font-size: 1.8rem;
    }

    .terms-content h1 {
        font-size: 1.2rem;
    }

    .terms-content h2 {
        font-size: 1.1rem;
    }

    .terms-content {
        font-size: 0.9rem;
    }
}
</style>

<div class="terms-wrapper">
    <div class="terms-container">
        <div class="terms-header">
            <div class="terms-title"><?= ts('title') ?></div>
            <div class="terms-subtitle"><?= ts('subtitle') ?></div>
        </div>

        <div class="terms-content">
            <p><strong><?= ts('effective_date') ?></strong></p>

            <h1><?= ts('chapter_1') ?></h1>
            <h2><?= ts('section_1') ?></h2>
            <ol>
                <li><?= ts('p1_1') ?></li>
                <li><?= ts('p1_2') ?></li>
                <li><?= ts('p1_3') ?></li>
                <li><?= ts('p1_4') ?></li>
                <li><?= ts('p1_5') ?></li>
                <li><?= ts('p1_6') ?></li>
                <li><?= ts('p1_7') ?></li>
            </ol>

            <h1>Rozdział II – Definicje / Chapter II – Definitions / Kapitel II – Definitionen / Chapitre II – Définitions</h1>
            <h2>§2.</h2>
            <p>Na potrzeby Regulaminu przyjmuje się następujące definicje: / For the purposes of these Terms, the following definitions are adopted: / Für diese Bedingungen gelten folgende Definitionen: / Pour ces Conditions, les définitions suivantes s'appliquent:</p>
            <ul>
                <li><strong>Serwis</strong> – portal internetowy 446DX.PL. / <strong>Service</strong> – website 446DX.PL. / <strong>Service</strong> – Website 446DX.PL. / <strong>Service</strong> – site web 446DX.PL.</li>
                <li><strong>Administrator</strong> – Właściciel strony 446DX.PL. / <strong>Administrator</strong> – Owner of 446DX.PL website. / <strong>Verwalter</strong> – Eigentümer der Website 446DX.PL. / <strong>Administrateur</strong> – Propriétaire du site web 446DX.PL.</li>
                <li><strong>Użytkownik</strong> – osoba korzystająca z Serwisu. / <strong>User</strong> – person using the Service. / <strong>Benutzer</strong> – Person, die den Service nutzt. / <strong>Utilisateur</strong> – personne utilisant le Service.</li>
                <li><strong>Konto</strong> – indywidualne konto użytkownika. / <strong>Account</strong> – individual user account. / <strong>Konto</strong> – individuelles Benutzerkonto. / <strong>Compte</strong> – compte utilisateur individuel.</li>
                <li><strong>Spot</strong> – wpis informujący o aktywności radiowej lub propagacji. / <strong>Spot</strong> – post with information about radio activity or propagation. / <strong>Spot</strong> – Beitrag mit Informationen über Funkaktivität oder Ausbreitungsbedingungen. / <strong>Spot</strong> – message contenant des informations sur l'activité radio ou les conditions de propagation.</li>
                <li><strong>Komentarz</strong> – wypowiedź dodana pod Spotem. / <strong>Comment</strong> – statement added under a Spot. / <strong>Kommentar</strong> – Aussage unter einem Spot. / <strong>Commentaire</strong> – déclaration ajoutée sous un Spot.</li>
                <li><strong>Profil</strong> – dane prezentowane publicznie przez użytkownika. / <strong>Profile</strong> – data presented publicly by a user. / <strong>Profil</strong> – von einem Benutzer öffentlich präsentierte Daten. / <strong>Profil</strong> – données présentées publiquement par un utilisateur.</li>
            </ul>

            <h1>Rozdział III – Rodzaj i zakres usług / Chapter III – Type and Scope of Services / Kapitel III – Art und Umfang der Dienstleistungen / Chapitre III – Type et Portée des Services</h1>
            <h2>§3.</h2>
            <p>Serwis umożliwia: / The Service enables: / Der Service ermöglicht: / Le Service permet:</p>
            <ol>
                <li>rejestrację kont, / account registration, / Kontoregistrierung, / l'enregistrement d'un compte,</li>
                <li>logowanie użytkowników, / user login, / Benutzeranmeldung, / la connexion des utilisateurs,</li>
                <li>publikowanie Spotów, / publishing Spots, / Veröffentlichung von Spots, / la publication de Spots,</li>
                <li>komentowanie Spotów, / commenting on Spots, / Kommentare zu Spots, / la rédaction de commentaires sur les Spots,</li>
                <li>przeglądanie mapy Spotów, / viewing Spot map, / Anzeige der Spot-Karte, / la consultation de la carte des Spots,</li>
                <li>wyszukiwanie Spotów, / searching for Spots, / Suche nach Spots, / la recherche de Spots,</li>
                <li>prowadzenie profilu użytkownika, / maintaining user profile, / Verwaltung des Benutzerprofils, / la gestion du profil utilisateur,</li>
                <li>zarządzanie własnym kontem. / managing your account. / Verwaltung Ihres Kontos. / la gestion de votre compte.</li>
            </ol>
            <p>Administrator może rozwijać Serwis o nowe funkcje. / The Administrator may develop the Service with new features. / Der Verwalter kann den Service um neue Funktionen erweitern. / L'Administrateur peut développer le Service avec de nouvelles fonctionnalités.</p>

            <h1>Rozdział IV – Wymagania techniczne / Chapter IV – Technical Requirements / Kapitel IV – Technische Anforderungen / Chapitre IV – Exigences Techniques</h1>
            <h2>§4.</h2>
            <p>Do korzystania z Serwisu wymagane są: / To use the Service, you need: / Um den Service zu nutzen, benötigen Sie: / Pour utiliser le Service, vous avez besoin de:</p>
            <ul>
                <li>urządzenie z dostępem do Internetu, / device with internet access, / Gerät mit Internetverbindung, / un appareil avec accès à Internet,</li>
                <li>aktualna przeglądarka internetowa, / current web browser, / aktuellen Webbrowser, / un navigateur web à jour,</li>
                <li>włączona obsługa JavaScript, / enabled JavaScript support, / aktivierte JavaScript-Unterstützung, / le support JavaScript activé,</li>
                <li>obsługa plików cookies. / cookies support. / Cookies-Unterstützung. / le support des cookies.</li>
            </ul>
            <p>Administrator nie odpowiada za problemy wynikające z niespełnienia wymagań technicznych po stronie użytkownika. / The Administrator is not responsible for issues resulting from failure to meet technical requirements on the user side. / Der Administrator ist nicht verantwortlich für Probleme, die sich aus der Nichterfüllung technischer Anforderungen auf Benutzerseite ergeben. / L'Administrateur n'est pas responsable des problèmes résultant du non-respect des exigences techniques de votre côté.</p>

            <h1>Rozdział V – Rejestracja i konto użytkownika / Chapter V – Registration and User Account / Kapitel V – Registrierung und Benutzerkonto / Chapitre V – Enregistrement et Compte Utilisateur</h1>
            <h2>§5.</h2>
            <ol>
                <li>Rejestracja jest bezpłatna. / Registration is free. / Die Registrierung ist kostenlos. / L'enregistrement est gratuit.</li>
                <li>Konto może założyć osoba posiadająca pełną zdolność do czynności prawnych lub korzystająca z Serwisu za zgodą przedstawiciela ustawowego, jeżeli wymagają tego przepisy prawa. / An account can be created by a person with full legal capacity or using the Service with the consent of a legal representative, if required by law. / Ein Konto kann von einer Person mit voller Geschäftsfähigkeit oder mit Zustimmung eines gesetzlichen Vertreters erstellt werden, wenn dies erforderlich ist. / Un compte peut être créé par une personne ayant la capacité juridique complète ou utilisant le Service avec le consentement d'un représentant légal, si la loi l'exige.</li>
                <li>Podczas rejestracji należy podać prawidłowy adres e-mail. / A valid email address must be provided during registration. / Während der Registrierung muss eine gültige E-Mail-Adresse angegeben werden. / Une adresse e-mail valide doit être fournie lors de l'enregistrement.</li>
                <li>Hasło powinno być odpowiednio silne i nie może być udostępniane osobom trzecim. / Password should be sufficiently strong and cannot be shared with third parties. / Das Passwort sollte ausreichend stark sein und darf nicht mit Dritten geteilt werden. / Le mot de passe doit être suffisamment fort et ne peut pas être partagé avec des tiers.</li>
                <li>Użytkownik odpowiada za bezpieczeństwo swojego konta. / User is responsible for account security. / Der Benutzer ist für die Kontosicherheit verantwortlich. / L'utilisateur est responsable de la sécurité de son compte.</li>
                <li>Zabronione jest zakładanie kont w celu obchodzenia blokad. / Creating accounts to bypass bans is prohibited. / Das Erstellen von Konten zum Umgehen von Sperren ist verboten. / La création de comptes pour contourner les interdictions est interdite.</li>
                <li>Zabronione jest udostępnianie konta innym osobom. / Sharing an account with other people is prohibited. / Die Freigabe eines Kontos für andere Personen ist verboten. / Le partage d'un compte avec d'autres personnes est interdit.</li>
                <li>Administrator może usunąć lub zablokować konto naruszające Regulamin. / Administrator may delete or block an account violating these Terms. / Der Administrator kann ein Konto löschen oder sperren, das gegen diese Bedingungen verstößt. / L'Administrateur peut supprimer ou suspendre un compte violant ces Conditions.</li>
            </ol>

            <hr class="divider">
            <p style="margin-top: 20px; text-align: center;"><strong>446DX.PL</strong></p>
            <p style="text-align: center; color: #9ca3af; font-size: 0.85rem;">Pełny regulamin zawiera wszystkie rozdziały. Zapoznaj się z pełną wersją na stronie serwisu. / Full terms contain all chapters. Please review the full version on the service website. / Vollständige Bedingungen enthalten alle Kapitel. Bitte überprüfen Sie die vollständige Version auf der Website des Service. / Les conditions complètes contiennent tous les chapitres. Veuillez consulter la version complète sur le site web du service.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>