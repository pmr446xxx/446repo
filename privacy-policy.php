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

// Tłumaczenia dla Polityki Prywatności
$privacy_translations = [
    'PL' => [
        'title' => 'Polityka Prywatności',
        'subtitle' => 'Serwisu internetowego 446DX.PL',
        'effective_date' => 'Obowiązuje od dnia: 01-08-2026',
        'chapter_1' => '1. Postanowienia ogólne',
        'p1_1' => 'Niniejsza Polityka Prywatności określa zasady przetwarzania danych osobowych użytkowników serwisu internetowego <strong>446DX.PL</strong>, zwanego dalej „Serwisem".',
        'p1_2' => 'Administrator dokłada wszelkich starań, aby dane osobowe były przetwarzane zgodnie z obowiązującymi przepisami prawa, w szczególności z:',
        'p1_2_1' => 'Rozporządzeniem Parlamentu Europejskiego i Rady (UE) 2016/679 (RODO),',
        'p1_2_2' => 'ustawą z dnia 10 maja 2018 r. o ochronie danych osobowych,',
        'p1_2_3' => 'innymi obowiązującymi przepisami prawa.',
        'p1_3' => 'Korzystanie z Serwisu oznacza zapoznanie się z niniejszą Polityką Prywatności.',
        'chapter_2' => '2. Administrator danych',
        'p2_text' => 'Administratorem danych osobowych jest:',
        'p2_admin' => 'Właściciel strony 446DX.PL',
        'p2_contact' => 'Kontakt z Administratorem:',
        'p2_email' => 'E-mail:',
        'chapter_3' => '3. Jakie dane zbieramy',
        'p3_text' => 'W zależności od sposobu korzystania z Serwisu możemy przetwarzać następujące dane:',
        'p3_heading_1' => 'Dane podawane podczas rejestracji',
        'p3_heading_1_1' => 'nazwa użytkownika,',
        'p3_heading_1_2' => 'adres e-mail,',
        'p3_heading_1_3' => 'zaszyfrowane hasło.',
        'p3_heading_2' => 'Dane techniczne',
        'p3_heading_2_1' => 'adres IP,',
        'p3_heading_2_2' => 'data i godzina logowania,',
        'p3_heading_2_3' => 'identyfikator sesji,',
        'p3_heading_2_4' => 'informacje o przeglądarce internetowej,',
        'p3_heading_2_5' => 'informacje o systemie operacyjnym,',
        'p3_heading_2_6' => 'dane dotyczące urządzenia.',
        'p3_heading_3' => 'Dane publikowane przez użytkownika',
        'p3_heading_3_1' => 'treść spotów,',
        'p3_heading_3_2' => 'komentarze,',
        'p3_heading_3_3' => 'opis profilu,',
        'p3_heading_3_4' => 'zdjęcie profilowe (jeżeli użytkownik je doda).',
        'p3_heading_4' => 'Dane związane z bezpieczeństwem',
        'p3_heading_4_1' => 'historia logowań,',
        'p3_heading_4_2' => 'informacje o próbach logowania,',
        'p3_heading_4_3' => 'informacje niezbędne do wykrywania nadużyć i ochrony Serwisu.',
        'p3_footer' => 'Administrator nie wymaga podawania imienia, nazwiska ani adresu zamieszkania.',
        'chapter_4' => '4. Cele przetwarzania danych',
        'p4_text' => 'Dane przetwarzane są wyłącznie w celu:',
        'chapter_5' => '5. Podstawa prawna przetwarzania',
        'p5_text' => 'Dane osobowe przetwarzane są na podstawie:',
        'chapter_6' => '6. Udostępnianie danych',
        'p6_text' => 'Administrator nie sprzedaje ani nie udostępnia danych osobowych użytkowników osobom trzecim w celach marketingowych.',
        'p6_text_2' => 'Dane mogą zostać przekazane wyłącznie:',
        'chapter_7' => '7. Okres przechowywania danych',
        'p7_text' => 'Dane przechowywane są:',
        'chapter_8' => '8. Prawa użytkownika',
        'p8_text' => 'Każdy użytkownik ma prawo do:',
        'chapter_9' => '9. Bezpieczeństwo danych',
        'p9_text' => 'Administrator stosuje odpowiednie środki techniczne i organizacyjne mające na celu ochronę danych osobowych przed:',
        'p9_text_2' => 'Hasła użytkowników przechowywane są wyłącznie w postaci zaszyfrowanych skrótów (hashy). Administrator nie ma możliwości odczytania haseł użytkowników.',
        'chapter_10' => '10. Publikowane treści',
        'p10_text' => 'Spoty, komentarze oraz inne treści dodane przez użytkownika mogą być publicznie widoczne dla innych osób korzystających z Serwisu.',
        'p10_text_2' => 'Użytkownik powinien zachować ostrożność przy publikowaniu informacji i nie zamieszczać danych osobowych osób trzecich bez odpowiedniej podstawy prawnej.',
        'chapter_11' => '11. Lokalizacja spotów',
        'p11_text' => 'Jeżeli Serwis umożliwia wskazanie lokalizacji spotu na mapie, dane lokalizacyjne są publikowane wyłącznie na podstawie informacji dobrowolnie wprowadzonych przez użytkownika.',
        'p11_text_2' => 'Serwis nie pobiera automatycznie lokalizacji urządzenia użytkownika bez jego wyraźnego działania lub zgody.',
        'chapter_12' => '12. Pliki cookies',
        'p12_text' => 'Serwis wykorzystuje pliki cookies niezbędne do:',
        'p12_text_2' => 'Szczegółowe informacje znajdują się w odrębnej <strong>Polityce Cookies</strong>.',
        'chapter_13' => '13. Przekazywanie danych poza Europejski Obszar Gospodarczy',
        'p13_text' => 'Co do zasady dane użytkowników nie są przekazywane poza Europejski Obszar Gospodarczy.',
        'p13_text_2' => 'Jeżeli w przyszłości będzie to konieczne z uwagi na korzystanie z usług zewnętrznych, Administrator zapewni odpowiednie zabezpieczenia wymagane przez obowiązujące przepisy.',
        'chapter_14' => '14. Zmiany Polityki Prywatności',
        'p14_text' => 'Administrator może zmienić niniejszą Politykę Prywatności w przypadku:',
        'p14_text_2' => 'Nowa wersja Polityki Prywatności obowiązuje od dnia jej opublikowania w Serwisie.',
        'chapter_15' => '15. Kontakt',
        'p15_text' => 'W sprawach dotyczących ochrony danych osobowych można kontaktować się z Administratorem:',
        'chapter_16' => '16. Postanowienia końcowe',
        'p16_text_1' => 'Niniejsza Polityka Prywatności stanowi integralne uzupełnienie Regulaminu Serwisu 446DX.PL.',
        'p16_text_2' => 'Aktualna wersja dokumentu jest stale dostępna w Serwisie.',
        'p16_text_3' => 'Polityka Prywatności wchodzi w życie z dniem jej opublikowania.',
    ],
    'EN' => [
        'title' => 'Privacy Policy',
        'subtitle' => 'of the website 446DX.PL',
        'effective_date' => 'Effective from: 01-08-2026',
        'chapter_1' => '1. General Provisions',
        'p1_1' => 'This Privacy Policy sets out the principles for processing personal data of users of the website <strong>446DX.PL</strong>, hereinafter referred to as the "Service".',
        'p1_2' => 'The Administrator makes every effort to process personal data in accordance with applicable law, in particular with:',
        'p1_2_1' => 'Regulation (EU) 2016/679 of the European Parliament and of the Council (GDPR),',
        'p1_2_2' => 'the Act of May 10, 2018 on the protection of personal data,',
        'p1_2_3' => 'other applicable legal provisions.',
        'p1_3' => 'Using the Service means you have read and understood this Privacy Policy.',
        'chapter_2' => '2. Data Controller',
        'p2_text' => 'The data controller is:',
        'p2_admin' => 'Owner of 446DX.PL website',
        'p2_contact' => 'Contact with the Administrator:',
        'p2_email' => 'Email:',
        'chapter_3' => '3. What Data We Collect',
        'p3_text' => 'Depending on how you use the Service, we may process the following data:',
        'p3_heading_1' => 'Data provided during registration',
        'p3_heading_1_1' => 'username,',
        'p3_heading_1_2' => 'email address,',
        'p3_heading_1_3' => 'encrypted password.',
        'p3_heading_2' => 'Technical data',
        'p3_heading_2_1' => 'IP address,',
        'p3_heading_2_2' => 'date and time of login,',
        'p3_heading_2_3' => 'session identifier,',
        'p3_heading_2_4' => 'web browser information,',
        'p3_heading_2_5' => 'operating system information,',
        'p3_heading_2_6' => 'device data.',
        'p3_heading_3' => 'Data published by the user',
        'p3_heading_3_1' => 'spot content,',
        'p3_heading_3_2' => 'comments,',
        'p3_heading_3_3' => 'profile description,',
        'p3_heading_3_4' => 'profile photo (if user adds one).',
        'p3_heading_4' => 'Security-related data',
        'p3_heading_4_1' => 'login history,',
        'p3_heading_4_2' => 'login attempt information,',
        'p3_heading_4_3' => 'information necessary to detect abuse and protect the Service.',
        'p3_footer' => 'The Administrator does not require providing first name, last name or residential address.',
        'chapter_4' => '4. Purposes of Data Processing',
        'p4_text' => 'Data is processed solely for the purpose of:',
        'chapter_5' => '5. Legal Basis for Processing',
        'p5_text' => 'Personal data is processed on the basis of:',
        'chapter_6' => '6. Data Sharing',
        'p6_text' => 'The Administrator does not sell or share user personal data with third parties for marketing purposes.',
        'p6_text_2' => 'Data may only be transferred to:',
        'chapter_7' => '7. Data Retention Period',
        'p7_text' => 'Data is stored:',
        'chapter_8' => '8. User Rights',
        'p8_text' => 'Each user has the right to:',
        'chapter_9' => '9. Data Security',
        'p9_text' => 'The Administrator applies appropriate technical and organizational measures to protect personal data from:',
        'p9_text_2' => 'User passwords are stored only as encrypted hashes. The Administrator cannot read user passwords.',
        'chapter_10' => '10. Published Content',
        'p10_text' => 'Spots, comments and other content added by a user may be publicly visible to other Service users.',
        'p10_text_2' => 'Users should be careful when publishing information and should not post personal data of third parties without appropriate legal basis.',
        'chapter_11' => '11. Spot Location',
        'p11_text' => 'If the Service allows you to indicate a spot location on a map, location data is published only based on information voluntarily entered by the user.',
        'p11_text_2' => 'The Service does not automatically collect device location without your explicit action or consent.',
        'chapter_12' => '12. Cookies',
        'p12_text' => 'The Service uses cookies necessary for:',
        'p12_text_2' => 'Detailed information is in a separate <strong>Cookie Policy</strong>.',
        'chapter_13' => '13. Data Transfer Outside the European Economic Area',
        'p13_text' => 'Generally, user data is not transferred outside the European Economic Area.',
        'p13_text_2' => 'If this becomes necessary in the future due to the use of external services, the Administrator will provide appropriate safeguards required by applicable law.',
        'chapter_14' => '14. Changes to Privacy Policy',
        'p14_text' => 'The Administrator may change this Privacy Policy in case of:',
        'p14_text_2' => 'The new Privacy Policy version is effective from the date of its publication on the Service.',
        'chapter_15' => '15. Contact',
        'p15_text' => 'For matters regarding personal data protection, you can contact the Administrator:',
        'chapter_16' => '16. Final Provisions',
        'p16_text_1' => 'This Privacy Policy is an integral supplement to the Terms of Service of 446DX.PL.',
        'p16_text_2' => 'The current version of this document is always available on the Service.',
        'p16_text_3' => 'This Privacy Policy becomes effective on the date of its publication.',
    ],
    'DE' => [
        'title' => 'Datenschutzrichtlinie',
        'subtitle' => 'der Website 446DX.PL',
        'effective_date' => 'Gültig ab: 01-08-2026',
        'chapter_1' => '1. Allgemeine Bestimmungen',
        'p1_1' => 'Diese Datenschutzrichtlinie legt die Grundsätze für die Verarbeitung personenbezogener Daten von Nutzern der Website <strong>446DX.PL</strong>, nachstehend als „Service" bezeichnet, fest.',
        'p1_2' => 'Der Administrator bemüht sich, personenbezogene Daten gemäß den geltenden Rechtsvorschriften zu verarbeiten, insbesondere gemäß:',
        'p1_2_1' => 'Verordnung (EU) 2016/679 des Europäischen Parlaments und des Rates (DSGVO),',
        'p1_2_2' => 'dem Gesetz vom 10. Mai 2018 zum Schutz personenbezogener Daten,',
        'p1_2_3' => 'anderen geltenden Rechtsvorschriften.',
        'p1_3' => 'Die Nutzung des Service bedeutet, dass Sie diese Datenschutzrichtlinie gelesen und verstanden haben.',
        'chapter_2' => '2. Verantwortlicher für die Datenverarbeitung',
        'p2_text' => 'Der Verantwortliche für die Datenverarbeitung ist:',
        'p2_admin' => 'Eigentümer der Website 446DX.PL',
        'p2_contact' => 'Kontakt mit dem Administrator:',
        'p2_email' => 'E-Mail:',
        'chapter_3' => '3. Welche Daten wir sammeln',
        'p3_text' => 'Je nachdem, wie Sie den Service nutzen, können wir die folgenden Daten verarbeiten:',
        'p3_heading_1' => 'Bei der Registrierung angegebene Daten',
        'p3_heading_1_1' => 'Benutzername,',
        'p3_heading_1_2' => 'E-Mail-Adresse,',
        'p3_heading_1_3' => 'verschlüsseltes Passwort.',
        'p3_heading_2' => 'Technische Daten',
        'p3_heading_2_1' => 'IP-Adresse,',
        'p3_heading_2_2' => 'Anmeldedatum und -uhrzeit,',
        'p3_heading_2_3' => 'Sitzungs-ID,',
        'p3_heading_2_4' => 'Webbrowser-Informationen,',
        'p3_heading_2_5' => 'Betriebssystem-Informationen,',
        'p3_heading_2_6' => 'Gerätedaten.',
        'p3_heading_3' => 'Vom Benutzer veröffentlichte Daten',
        'p3_heading_3_1' => 'Spot-Inhalt,',
        'p3_heading_3_2' => 'Kommentare,',
        'p3_heading_3_3' => 'Profilbeschreibung,',
        'p3_heading_3_4' => 'Profilfoto (sofern vom Benutzer hinzugefügt).',
        'p3_heading_4' => 'Sicherheitsbezogene Daten',
        'p3_heading_4_1' => 'Anmeldedatensätze,',
        'p3_heading_4_2' => 'Informationen über Anmeldeversuche,',
        'p3_heading_4_3' => 'Informationen, die zur Erkennung von Missbrauch und zum Schutz des Service erforderlich sind.',
        'p3_footer' => 'Der Administrator verlangt keine Angabe von Vor- und Nachname oder Wohnort.',
        'chapter_4' => '4. Zwecke der Datenverarbeitung',
        'p4_text' => 'Daten werden ausschließlich verarbeitet, um:',
        'chapter_5' => '5. Rechtsgrundlage für die Verarbeitung',
        'p5_text' => 'Personenbezogene Daten werden auf der Grundlage von:',
        'chapter_6' => '6. Datenweitergabe',
        'p6_text' => 'Der Administrator verkauft oder teilt personenbezogene Benutzerdaten nicht zu Marketingzwecken mit Dritten.',
        'p6_text_2' => 'Daten können nur weitergegeben werden an:',
        'chapter_7' => '7. Zeitraum der Datenspeicherung',
        'p7_text' => 'Daten werden gespeichert:',
        'chapter_8' => '8. Benutzerrechte',
        'p8_text' => 'Jeder Benutzer hat das Recht:',
        'chapter_9' => '9. Datensicherheit',
        'p9_text' => 'Der Administrator wendet angemessene technische und organisatorische Maßnahmen an, um personenbezogene Daten zu schützen vor:',
        'p9_text_2' => 'Benutzerkennwörter werden nur als verschlüsselte Hashes gespeichert. Der Administrator kann Benutzerkennwörter nicht lesen.',
        'chapter_10' => '10. Veröffentlichte Inhalte',
        'p10_text' => 'Spots, Kommentare und andere vom Benutzer hinzugefügte Inhalte können für andere Service-Nutzer öffentlich sichtbar sein.',
        'p10_text_2' => 'Benutzer sollten bei der Veröffentlichung von Informationen vorsichtig sein und persönliche Daten von Dritten nicht ohne entsprechende Rechtsgrundlage veröffentlichen.',
        'chapter_11' => '11. Spot-Standort',
        'p11_text' => 'Wenn der Service es ermöglicht, einen Spot-Standort auf einer Karte anzugeben, werden Standortdaten nur auf der Grundlage von Informationen veröffentlicht, die vom Benutzer freiwillig eingegeben wurden.',
        'p11_text_2' => 'Der Service erfasst nicht automatisch den Gerätestandort ohne Ihre ausdrückliche Aktion oder Zustimmung.',
        'chapter_12' => '12. Cookies',
        'p12_text' => 'Der Service nutzt Cookies, die für folgende Zwecke erforderlich sind:',
        'p12_text_2' => 'Detaillierte Informationen finden Sie in einer separaten <strong>Cookie-Richtlinie</strong>.',
        'chapter_13' => '13. Datenübertragung außerhalb des Europäischen Wirtschaftsraums',
        'p13_text' => 'Grundsätzlich werden Benutzerdaten nicht außerhalb des Europäischen Wirtschaftsraums übertragen.',
        'p13_text_2' => 'Sollte dies in Zukunft aufgrund der Nutzung von externen Services erforderlich werden, stellt der Administrator angemessene Schutzmaßnahmen gemäß geltenden Rechtsvorschriften bereit.',
        'chapter_14' => '14. Änderungen der Datenschutzrichtlinie',
        'p14_text' => 'Der Administrator kann diese Datenschutzrichtlinie ändern in folgenden Fällen:',
        'p14_text_2' => 'Die neue Version der Datenschutzrichtlinie ist ab dem Veröffentlichungsdatum im Service gültig.',
        'chapter_15' => '15. Kontakt',
        'p15_text' => 'In Angelegenheiten zum Schutz personenbezogener Daten können Sie den Administrator kontaktieren:',
        'chapter_16' => '16. Schlussbestimmungen',
        'p16_text_1' => 'Diese Datenschutzrichtlinie ist ein integraler Bestandteil der Nutzungsbedingungen von 446DX.PL.',
        'p16_text_2' => 'Die aktuelle Version dieses Dokuments ist jederzeit im Service verfügbar.',
        'p16_text_3' => 'Diese Datenschutzrichtlinie wird ab dem Veröffentlichungsdatum gültig.',
    ],
    'FR' => [
        'title' => 'Politique de Confidentialité',
        'subtitle' => 'du site web 446DX.PL',
        'effective_date' => 'En vigueur à partir du : 01-08-2026',
        'chapter_1' => '1. Dispositions Générales',
        'p1_1' => 'Cette Politique de Confidentialité énonce les principes de traitement des données personnelles des utilisateurs du site web <strong>446DX.PL</strong>, ci-après dénommé le « Service ».',
        'p1_2' => 'L\'Administrateur s\'efforce de traiter les données personnelles conformément à la loi applicable, en particulier selon :',
        'p1_2_1' => 'Règlement (UE) 2016/679 du Parlement européen et du Conseil (RGPD),',
        'p1_2_2' => 'la Loi du 10 mai 2018 sur la protection des données personnelles,',
        'p1_2_3' => 'autres dispositions légales applicables.',
        'p1_3' => 'L\'utilisation du Service signifie que vous avez lu et compris cette Politique de Confidentialité.',
        'chapter_2' => '2. Responsable du Traitement des Données',
        'p2_text' => 'Le responsable du traitement des données est :',
        'p2_admin' => 'Propriétaire du site web 446DX.PL',
        'p2_contact' => 'Contact avec l\'Administrateur :',
        'p2_email' => 'E-mail :',
        'chapter_3' => '3. Quelles Données Nous Collectons',
        'p3_text' => 'Selon votre utilisation du Service, nous pouvons traiter les données suivantes :',
        'p3_heading_1' => 'Données fournies lors de l\'inscription',
        'p3_heading_1_1' => 'nom d\'utilisateur,',
        'p3_heading_1_2' => 'adresse e-mail,',
        'p3_heading_1_3' => 'mot de passe crypté.',
        'p3_heading_2' => 'Données techniques',
        'p3_heading_2_1' => 'adresse IP,',
        'p3_heading_2_2' => 'date et heure de connexion,',
        'p3_heading_2_3' => 'identifiant de session,',
        'p3_heading_2_4' => 'informations sur le navigateur web,',
        'p3_heading_2_5' => 'informations sur le système d\'exploitation,',
        'p3_heading_2_6' => 'données du dispositif.',
        'p3_heading_3' => 'Données publiées par l\'utilisateur',
        'p3_heading_3_1' => 'contenu des spots,',
        'p3_heading_3_2' => 'commentaires,',
        'p3_heading_3_3' => 'description du profil,',
        'p3_heading_3_4' => 'photo de profil (si l\'utilisateur l\'ajoute).',
        'p3_heading_4' => 'Données liées à la sécurité',
        'p3_heading_4_1' => 'historique des connexions,',
        'p3_heading_4_2' => 'informations sur les tentatives de connexion,',
        'p3_heading_4_3' => 'informations nécessaires pour détecter les abus et protéger le Service.',
        'p3_footer' => 'L\'Administrateur ne vous demande pas de fournir votre nom, prénom ou adresse de résidence.',
        'chapter_4' => '4. Objectifs du Traitement des Données',
        'p4_text' => 'Les données sont traitées uniquement aux fins de :',
        'chapter_5' => '5. Base Juridique du Traitement',
        'p5_text' => 'Les données personnelles sont traitées sur la base de :',
        'chapter_6' => '6. Partage des Données',
        'p6_text' => 'L\'Administrateur ne vend pas et ne partage pas les données personnelles des utilisateurs avec des tiers à des fins marketing.',
        'p6_text_2' => 'Les données ne peuvent être transmises qu\'à :',
        'chapter_7' => '7. Période de Conservation des Données',
        'p7_text' => 'Les données sont conservées :',
        'chapter_8' => '8. Droits de l\'Utilisateur',
        'p8_text' => 'Chaque utilisateur a le droit de :',
        'chapter_9' => '9. Sécurité des Données',
        'p9_text' => 'L\'Administrateur applique des mesures techniques et organisationnelles appropriées pour protéger les données personnelles contre :',
        'p9_text_2' => 'Les mots de passe des utilisateurs sont stockés uniquement sous forme de hachages cryptés. L\'Administrateur ne peut pas lire les mots de passe des utilisateurs.',
        'chapter_10' => '10. Contenu Publié',
        'p10_text' => 'Les spots, commentaires et autres contenus ajoutés par un utilisateur peuvent être visibles publiquement pour les autres utilisateurs du Service.',
        'p10_text_2' => 'Les utilisateurs doivent être prudents lors de la publication d\'informations et ne pas publier les données personnelles de tiers sans base juridique appropriée.',
        'chapter_11' => '11. Localisation des Spots',
        'p11_text' => 'Si le Service permet d\'indiquer la localisation d\'un spot sur une carte, les données de localisation ne sont publiées que sur la base d\'informations entrées volontairement par l\'utilisateur.',
        'p11_text_2' => 'Le Service ne collecte pas automatiquement la localisation du dispositif sans votre action explicite ou consentement.',
        'chapter_12' => '12. Cookies',
        'p12_text' => 'Le Service utilise des cookies nécessaires pour :',
        'p12_text_2' => 'Les informations détaillées se trouvent dans une <strong>Politique des Cookies</strong> séparée.',
        'chapter_13' => '13. Transfert de Données en Dehors de l\'Espace Économique Européen',
        'p13_text' => 'Généralement, les données utilisateur ne sont pas transférées en dehors de l\'Espace Économique Européen.',
        'p13_text_2' => 'Si cela devient nécessaire à l\'avenir en raison de l\'utilisation de services externes, l\'Administrateur fournira des garanties appropriées conformément aux lois applicables.',
        'chapter_14' => '14. Modifications de la Politique de Confidentialité',
        'p14_text' => 'L\'Administrateur peut modifier cette Politique de Confidentialité en cas de :',
        'p14_text_2' => 'La nouvelle version de la Politique de Confidentialité est en vigueur à partir de la date de sa publication sur le Service.',
        'chapter_15' => '15. Contact',
        'p15_text' => 'Pour toute question concernant la protection des données personnelles, vous pouvez contacter l\'Administrateur :',
        'chapter_16' => '16. Dispositions Finales',
        'p16_text_1' => 'Cette Politique de Confidentialité est un complément intégral des Conditions d\'Utilisation de 446DX.PL.',
        'p16_text_2' => 'La version actuelle de ce document est toujours disponible sur le Service.',
        'p16_text_3' => 'Cette Politique de Confidentialité entre en vigueur à la date de sa publication.',
    ],
];

function pp($key) {
    global $current_lang, $privacy_translations;
    return $privacy_translations[$current_lang][$key] ?? $key;
}
?>

<style>
.privacy-wrapper {
    width: 100%;
    background-color: #0f1419;
    padding: 40px 20px;
    margin: 0;
}

.privacy-container {
    max-width: 900px;
    margin: 0 auto;
    background-color: transparent;
    border: 2px solid #10b981;
    border-radius: 8px;
    padding: 40px;
    color: #e5e7eb;
}

.privacy-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #10b981;
}

.privacy-title {
    font-size: 2.2rem;
    font-weight: 900;
    color: #10b981;
    margin-bottom: 10px;
}

.privacy-subtitle {
    font-size: 0.95rem;
    color: #9ca3af;
}

.privacy-content {
    line-height: 1.8;
    font-size: 0.95rem;
}

.privacy-content h1 {
    color: #10b981;
    margin-top: 30px;
    margin-bottom: 15px;
    font-size: 1.4rem;
}

.privacy-content h2 {
    color: #10b981;
    margin-top: 30px;
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.privacy-content h3 {
    color: #fbbf24;
    margin-top: 20px;
    margin-bottom: 10px;
    font-size: 1.1rem;
}

.privacy-content p {
    margin-bottom: 15px;
}

.privacy-content ul {
    margin-left: 20px;
    margin-bottom: 15px;
}

.privacy-content ol {
    margin-left: 20px;
    margin-bottom: 15px;
}

.privacy-content li {
    margin-bottom: 8px;
}

.privacy-content strong {
    color: #10b981;
}

.privacy-content em {
    color: #fbbf24;
}

.privacy-content a {
    color: #10b981;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}

.privacy-content a:hover {
    color: #059669;
    border-bottom-color: #10b981;
}

.divider {
    margin-top: 30px;
    border: none;
    border-top: 1px solid rgba(16, 185, 129, 0.3);
}

@media (max-width: 768px) {
    .privacy-container {
        padding: 20px;
    }

    .privacy-title {
        font-size: 1.8rem;
    }

    .privacy-content h1 {
        font-size: 1.2rem;
    }

    .privacy-content h2 {
        font-size: 1.1rem;
    }

    .privacy-content h3 {
        font-size: 1rem;
    }

    .privacy-content {
        font-size: 0.9rem;
    }
}
</style>

<div class="privacy-wrapper">
    <div class="privacy-container">
        <div class="privacy-header">
            <div class="privacy-title"><?= pp('title') ?></div>
            <div class="privacy-subtitle"><?= pp('subtitle') ?></div>
        </div>

        <div class="privacy-content">
            <p><strong><?= pp('effective_date') ?></strong></p>

            <h1><?= pp('chapter_1') ?></h1>
            <ol>
                <li><?= pp('p1_1') ?></li>
                <li><?= pp('p1_2') ?>
                    <ul>
                        <li><?= pp('p1_2_1') ?></li>
                        <li><?= pp('p1_2_2') ?></li>
                        <li><?= pp('p1_2_3') ?></li>
                    </ul>
                </li>
                <li><?= pp('p1_3') ?></li>
            </ol>

            <h1><?= pp('chapter_2') ?></h1>
            <p><?= pp('p2_text') ?></p>
            <p><strong><?= pp('p2_admin') ?></strong></p>
            <p><?= pp('p2_contact') ?></p>
            <p><strong><?= pp('p2_email') ?></strong> <a href="mailto:admin@446dx.pl">admin@446dx.pl</a></p>

            <h1><?= pp('chapter_3') ?></h1>
            <p><?= pp('p3_text') ?></p>

            <h2><?= pp('p3_heading_1') ?></h2>
            <ul>
                <li><?= pp('p3_heading_1_1') ?></li>
                <li><?= pp('p3_heading_1_2') ?></li>
                <li><?= pp('p3_heading_1_3') ?></li>
            </ul>

            <h2><?= pp('p3_heading_2') ?></h2>
            <ul>
                <li><?= pp('p3_heading_2_1') ?></li>
                <li><?= pp('p3_heading_2_2') ?></li>
                <li><?= pp('p3_heading_2_3') ?></li>
                <li><?= pp('p3_heading_2_4') ?></li>
                <li><?= pp('p3_heading_2_5') ?></li>
                <li><?= pp('p3_heading_2_6') ?></li>
            </ul>

            <h2><?= pp('p3_heading_3') ?></h2>
            <ul>
                <li><?= pp('p3_heading_3_1') ?></li>
                <li><?= pp('p3_heading_3_2') ?></li>
                <li><?= pp('p3_heading_3_3') ?></li>
                <li><?= pp('p3_heading_3_4') ?></li>
            </ul>

            <h2><?= pp('p3_heading_4') ?></h2>
            <ul>
                <li><?= pp('p3_heading_4_1') ?></li>
                <li><?= pp('p3_heading_4_2') ?></li>
                <li><?= pp('p3_heading_4_3') ?></li>
            </ul>

            <p><?= pp('p3_footer') ?></p>

            <h1><?= pp('chapter_4') ?></h1>
            <p><?= pp('p4_text') ?></p>
            <ul>
                <li>utworzenia i prowadzenia konta użytkownika,</li>
                <li>umożliwienia logowania,</li>
                <li>publikowania spotów,</li>
                <li>publikowania komentarzy,</li>
                <li>zapewnienia bezpieczeństwa kont użytkowników,</li>
                <li>ochrony Serwisu przed nadużyciami,</li>
                <li>kontaktu z użytkownikiem w sprawach dotyczących jego konta,</li>
                <li>obsługi zgłoszeń i reklamacji,</li>
                <li>realizacji obowiązków wynikających z przepisów prawa.</li>
            </ul>

            <h1><?= pp('chapter_5') ?></h1>
            <p><?= pp('p5_text') ?></p>
            <ul>
                <li>art. 6 ust. 1 lit. b RODO – wykonanie umowy o świadczenie usług drogą elektroniczną,</li>
                <li>art. 6 ust. 1 lit. c RODO – wypełnienie obowiązków prawnych,</li>
                <li>art. 6 ust. 1 lit. f RODO – prawnie uzasadniony interes Administratora.</li>
            </ul>

            <h1><?= pp('chapter_6') ?></h1>
            <p><?= pp('p6_text') ?></p>
            <p><?= pp('p6_text_2') ?></p>
            <ul>
                <li>dostawcy hostingu,</li>
                <li>dostawcy poczty elektronicznej,</li>
                <li>podmiotom świadczącym usługi niezbędne do funkcjonowania Serwisu,</li>
                <li>organom publicznym, jeżeli obowiązek taki wynika z przepisów prawa.</li>
            </ul>

            <h1><?= pp('chapter_7') ?></h1>
            <p><?= pp('p7_text') ?></p>
            <ul>
                <li>przez okres posiadania konta użytkownika,</li>
                <li>do czasu usunięcia konta przez użytkownika lub Administratora,</li>
                <li>przez okres wymagany przepisami prawa,</li>
                <li>przez okres niezbędny do dochodzenia lub obrony przed roszczeniami.</li>
            </ul>

            <h1><?= pp('chapter_8') ?></h1>
            <p><?= pp('p8_text') ?></p>
            <ul>
                <li>dostępu do swoich danych,</li>
                <li>sprostowania danych,</li>
                <li>usunięcia danych,</li>
                <li>ograniczenia przetwarzania,</li>
                <li>wniesienia sprzeciwu,</li>
                <li>przenoszenia danych,</li>
                <li>cofnięcia zgody,</li>
                <li>wniesienia skargi do organu nadzorczego.</li>
            </ul>

            <h1><?= pp('chapter_9') ?></h1>
            <p><?= pp('p9_text') ?></p>
            <ul>
                <li>utratą,</li>
                <li>zniszczeniem,</li>
                <li>nieuprawnionym dostępem,</li>
                <li>ujawnieniem,</li>
                <li>zmianą,</li>
                <li>nieuprawnionym wykorzystaniem.</li>
            </ul>
            <p><?= pp('p9_text_2') ?></p>

            <h1><?= pp('chapter_10') ?></h1>
            <p><?= pp('p10_text') ?></p>
            <p><?= pp('p10_text_2') ?></p>

            <h1><?= pp('chapter_11') ?></h1>
            <p><?= pp('p11_text') ?></p>
            <p><?= pp('p11_text_2') ?></p>

            <h1><?= pp('chapter_12') ?></h1>
            <p><?= pp('p12_text') ?></p>
            <ul>
                <li>logowania użytkowników,</li>
                <li>utrzymania sesji,</li>
                <li>zapewnienia bezpieczeństwa,</li>
                <li>prawidłowego działania strony.</li>
            </ul>
            <p><?= pp('p12_text_2') ?></p>

            <h1><?= pp('chapter_13') ?></h1>
            <p><?= pp('p13_text') ?></p>
            <p><?= pp('p13_text_2') ?></p>

            <h1><?= pp('chapter_14') ?></h1>
            <p><?= pp('p14_text') ?></p>
            <ul>
                <li>zmian przepisów prawa,</li>
                <li>zmian funkcjonalności Serwisu,</li>
                <li>zmian organizacyjnych,</li>
                <li>zmian dotyczących sposobu przetwarzania danych.</li>
            </ul>
            <p><?= pp('p14_text_2') ?></p>

            <h1><?= pp('chapter_15') ?></h1>
            <p><?= pp('p15_text') ?></p>
            <p><strong><?= pp('p2_admin') ?></strong></p>
            <p><strong><?= pp('p2_email') ?></strong> <a href="mailto:admin@446dx.pl">admin@446dx.pl</a></p>

            <h1><?= pp('chapter_16') ?></h1>
            <ol>
                <li><?= pp('p16_text_1') ?></li>
                <li><?= pp('p16_text_2') ?></li>
                <li><?= pp('p16_text_3') ?></li>
            </ol>

            <hr class="divider">
            <p style="margin-top: 20px; text-align: center;"><strong>446DX.PL</strong></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>