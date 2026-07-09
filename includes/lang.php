<?php

$lang = $_SESSION['lang'] ?? 'PL';

$translations = [
    'PL' => [
        'home' => 'Strona główna',
        'login' => 'Zaloguj się',
        'register' => 'Rejestracja',
        'dashboard' => 'Dashboard',
        'map' => 'Mapa',
        'logout' => 'Wyloguj się',
        'welcome' => 'Witaj na 446CLUSTER!',
        'pmr_platform' => 'Platforma komunikacji PMR dla Polskich operatorów',
        'live_traffic' => 'Live traffic',
        'add_spot' => 'Dodaj spot',
        'statistics' => 'Statystyka',
        'spots_today' => 'Spotów dzisiaj:',
        'this_month' => 'w tym miesiącu:',
        'operators_today' => 'Operatorów dzisiaj:',
        'channel_of_day' => 'Kanał dnia:',
        'last_spot' => 'Ostatni spot:',
        'quick_actions' => 'Szybkie akcje',
        'my_recent_spots' => 'Moje ostatnie spoty',
        'my_profile' => 'Mój profil',
    ],
    'EN' => [
        'home' => 'Home',
        'login' => 'Login',
        'register' => 'Register',
        'dashboard' => 'Dashboard',
        'map' => 'Map',
        'logout' => 'Logout',
        'welcome' => 'Welcome to 446CLUSTER!',
        'pmr_platform' => 'PMR communication platform for Polish operators',
        'live_traffic' => 'Live traffic',
        'add_spot' => 'Add spot',
        'statistics' => 'Statistics',
        'spots_today' => 'Spots today:',
        'this_month' => 'this month:',
        'operators_today' => 'Operators today:',
        'channel_of_day' => 'Channel of the day:',
        'last_spot' => 'Last spot:',
        'quick_actions' => 'Quick actions',
        'my_recent_spots' => 'My recent spots',
        'my_profile' => 'My profile',
    ],
];

function t($key) {
    global $lang, $translations;
    return $translations[$lang][$key] ?? $key;
}

?>
