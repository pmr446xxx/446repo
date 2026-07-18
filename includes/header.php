<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once 'includes/lang.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] === 'EN' ? 'en' : ($_SESSION['lang'] === 'DE' ? 'de' : ($_SESSION['lang'] === 'FR' ? 'fr' : 'pl')) ?>" translate="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <title>446DX.pl - PMR Communication Platform</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        html {
            background-color: #000000 !important;
        }
        
        body {
            background-color: #000000 !important;
            color: #e5e7eb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        main {
            background-color: #000000 !important;
            min-height: 100vh;
        }

        .container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
            background-color: #000000 !important;
        }

        section, article, aside, nav {
            background-color: inherit !important;
        }
        
        .btn-success {
            background-color: #10b981;
            border: none;
        }
        
        .btn-success:hover {
            background-color: #059669;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-warning {
            background-color: #f59e0b;
            border: none;
            color: #000;
        }
        
        .btn-warning:hover {
            background-color: #d97706;
            color: #fff;
        }
        
        .btn-danger {
            background-color: #ef4444;
            border: none;
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
        }
        
        .btn-secondary {
            background-color: #6b7280;
            border: none;
        }
        
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        
        .form-control {
            background-color: #1a1f3a;
            border: 1px solid #ff3b3b;
            color: #e5e7eb;
        }
        
        .form-control:focus {
            background-color: #1a1f3a;
            border-color: #ff3b3b;
            color: #e5e7eb;
            box-shadow: 0 0 10px rgba(255, 59, 59, 0.3);
        }
        
        .form-select {
            background-color: #1a1f3a;
            border: 1px solid #ff3b3b;
            color: #e5e7eb;
        }
        
        .form-select:focus {
            background-color: #1a1f3a;
            border-color: #ff3b3b;
            color: #e5e7eb;
            box-shadow: 0 0 10px rgba(255, 59, 59, 0.3);
        }
        
        .form-select option {
            background-color: #1a1f3a;
            color: #e5e7eb;
        }
        
        .form-label {
            color: #e5e7eb;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        a {
            color: #3b82f6;
            text-decoration: none;
        }
        
        a:hover {
            color: #2563eb;
            text-decoration: underline;
        }
        
        .text-secondary {
            color: #9ca3af !important;
        }
    </style>
</head>
<body>