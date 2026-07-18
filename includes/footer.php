<?php
?>

<style>
.footer-wrapper {
    width: 100%;
    background: #000;
    padding: 30px 20px;
    margin-top: 40px;
    border-top: none !important;
    text-align: center;
}

.donate-button-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.donate-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    border: none;
    border-radius: 6px;
    color: #000;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
}

.donate-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(251, 191, 36, 0.5);
    text-decoration: none;
    color: #000;
}

.donate-button i {
    font-size: 1.1rem;
}

.donate-icon {
    width: 24px;
    height: 24px;
    display: inline-block;
}

.payment-methods {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.payment-methods svg {
    width: 28px;
    height: 18px;
    opacity: 0.7;
    transition: opacity 0.3s;
}

.payment-methods svg:hover {
    opacity: 1;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}

.donate-button {
    animation: pulse 2s ease-in-out infinite;
}

.donate-button:hover {
    animation: none;
}
</style>

<div class="footer-wrapper">
    <div class="donate-button-container">
        <a href="https://www.paypal.com/donate/?hosted_button_id=VUHVVY82QSLHS" target="_blank" class="donate-button">
            <i class="fa-solid fa-heart"></i>
            <span><?php echo t('donate'); ?></span>
        </a>
    </div>
    
    <div class="payment-methods">
        <svg viewBox="0 0 48 30" xmlns="http://www.w3.org/2000/svg">
            <!-- PayPal -->
            <rect width="48" height="30" rx="3" fill="#003087"/>
            <text x="24" y="20" font-size="12" fill="#fff" text-anchor="middle" font-weight="bold">PayPal</text>
        </svg>
        <svg viewBox="0 0 48 30" xmlns="http://www.w3.org/2000/svg">
            <!-- Visa -->
            <rect width="48" height="30" rx="3" fill="#1434CB"/>
            <text x="24" y="20" font-size="10" fill="#fff" text-anchor="middle" font-weight="bold">VISA</text>
        </svg>
        <svg viewBox="0 0 48 30" xmlns="http://www.w3.org/2000/svg">
            <!-- Mastercard -->
            <rect width="48" height="30" rx="3" fill="#EB001B"/>
            <circle cx="20" cy="15" r="8" fill="#FF5F00"/>
            <circle cx="28" cy="15" r="8" fill="#FFB81C"/>
        </svg>
        <svg viewBox="0 0 48 30" xmlns="http://www.w3.org/2000/svg">
            <!-- American Express -->
            <rect width="48" height="30" rx="3" fill="#006FCF"/>
            <text x="24" y="20" font-size="10" fill="#fff" text-anchor="middle" font-weight="bold">AMEX</text>
        </svg>
    </div>
</div>
