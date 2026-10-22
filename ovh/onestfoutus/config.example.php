<?php
// Copie ce fichier en config.php sur OVH et remplis-le. Ne le mets JAMAIS sur GitHub.
return [
    // Clé Stripe restreinte du compte « On est foutus » : permission « Checkout Sessions : Lecture » uniquement.
    'stripe_key'      => 'rk_live_A_REMPLACER',

    // Identifiant du lien de paiement (commence par plink_). Laisse vide pour ne pas vérifier.
    'payment_link'    => '',

    // Montant minimum en centimes (2 € = 200).
    'min_amount'      => 200,

    // Sites autorisés à appeler la vérification.
    'allowed_origins' => ['https://onestfoutus.strongfamily.fr'],
];
