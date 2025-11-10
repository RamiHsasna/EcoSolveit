<?php

namespace Controllers;

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../models/Donate.php';

use Models\Donate;

class DonationController
{
    public function donate()
    {
        session_start();
        $successMessage = '';
        $errorMessage = '';

        // simulate user session for testing (you can remove later)
        if (!isset($_SESSION['username'])) {
            $_SESSION['username'] = 'JohnDoe';
        }

        $user_identifier = $_SESSION['username'];
        $username = $_SESSION['username'];

        $pdo = \Database::getInstance()->getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donate'])) {
            $amount = trim($_POST['amount']);
            $payment_method = $_POST['payment_method'] ?? '';

            if ($amount > 0 && $payment_method) {

                $donation = new Donate(
                    null,
                    $user_identifier,
                    (float)$amount,
                    $payment_method,
                    date('Y-m-d H:i:s')
                );

                $stmt = $pdo->prepare("
                    INSERT INTO donations (user_identifier, amount, payment_method, donation_date, status)
                    VALUES (?, ?, ?, ?, 'pending')
                ");

                $success = $stmt->execute([
                    $donation->getUserIdentifier(),
                    $donation->getAmount(),
                    $donation->getPaymentMethod(),
                    $donation->getDonationDate()
                ]);

                if ($success) {
                    $successMessage = "✅ Merci $username ! Votre don de $amount € via $payment_method a été enregistré.";
                } else {
                    $errorMessage = "❌ Erreur lors du traitement du don.";
                }
            } else {
                $errorMessage = "⚠️ Veuillez entrer un montant valide et choisir un mode de paiement.";
            }
        }

        include __DIR__ . '/../../views/FrontOffice/donation.php';
    }
}
