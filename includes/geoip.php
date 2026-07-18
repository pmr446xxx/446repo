<?php
declare(strict_types=1);

function getOperatorCountry(string $operatorName, \PDO $pdo): array {
    try {
        $stmt = $pdo->prepare("SELECT country_code FROM operators WHERE operator = ? LIMIT 1");
        $stmt->execute([$operatorName]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $countryCode = strtoupper($result['country_code'] ?? 'PL');
        
        $flags = [
            'PL' => '🇵🇱', 'DE' => '🇩🇪', 'CZ' => '🇨🇿', 'SK' => '🇸🇰',
            'FR' => '🇫🇷', 'NL' => '🇳🇱', 'BE' => '🇧🇪', 'LU' => '🇱🇺',
            'AT' => '🇦🇹', 'CH' => '🇨🇭', 'IT' => '🇮🇹', 'ES' => '🇪🇸',
            'PT' => '🇵🇹', 'GB' => '🇬🇧', 'IE' => '🇮🇪', 'DK' => '🇩🇰',
            'SE' => '🇸🇪', 'NO' => '🇳🇴', 'FI' => '🇫🇮', 'RU' => '🇷🇺',
            'UA' => '🇺🇦', 'BY' => '🇧🇾', 'LT' => '🇱🇹', 'LV' => '🇱🇻',
            'EE' => '🇪🇪', 'RO' => '🇷🇴', 'BG' => '🇧🇬', 'GR' => '🇬🇷',
            'HR' => '🇭🇷', 'SI' => '🇸🇮', 'HU' => '🇭🇺', 'RS' => '🇷🇸',
        ];
        
        $flag = $flags[$countryCode] ?? '🌍';
        
        return [
            'country' => $countryCode,
            'country_code' => $countryCode,
            'flag' => $flag
        ];
    } catch (Throwable $e) {
        return [
            'country' => 'Unknown',
            'country_code' => 'UN',
            'flag' => '🌍'
        ];
    }
}
?>