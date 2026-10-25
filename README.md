# On est foutus ?

Site satirique : la barre de progression de l'apocalypse IA, mise à jour chaque jour.
En ligne sur https://onestfoutus.strongfamily.fr (GitHub Pages).

## Mise à jour quotidienne

Seul `data.json` change chaque jour :

1. Ajouter une entrée **en tête** de `entries` :
   `date` (AAAA-MM-JJ), `delta` (entre -1.5 et +1.5), `value` (= `current` précédent + `delta`, arrondi à 0,1),
   `kind` (`real` ou `joke`), `text` (une ou deux phrases en français, ton pince-sans-rire),
   `source` `{name, url}` obligatoire pour `real`.
2. Mettre `current` = la nouvelle `value` et `updated` = la date du jour.
3. Régénérer l'image de partage : `python3 scripts/make_og.py`.

Règles : une info `real` doit venir d'un article vérifié, avec un lien qui marche et des faits rapportés sans déformation.
Ne jamais attribuer à une personne réelle une phrase ou un chiffre qu'elle n'a pas dit.
Les blagues ne visent personne de réel et restent marquées `joke`.
La valeur reste entre 5 et 40.

Variété :
- Ne jamais traiter deux jours de suite le même type d'histoire (par exemple « une IA échappe à sa surveillance ») ni la même entreprise.
- Si les 3 dernières entrées montent toutes, l'entrée du jour descend (info rassurante ou blague).
- Au moins 2 blagues par semaine glissante.
- Au-dessus de 22, privilégier la baisse. En dessous de 12, privilégier la hausse.
- Deltas habituels : +0,2 à +0,9 ou −0,1 à −0,8. Garder +1,0 à +1,5 pour une actu majeure, au plus une fois par semaine.

## Paiement du certificat

Le bouton d'achat mène à un lien de paiement Stripe, qui redirige vers `merci.html?session_id={CHECKOUT_SESSION_ID}`.
`merci.html` vérifie le paiement auprès de `ovh/onestfoutus/verify.php`, hébergé sur OVH (api.strongfamily.fr), qui garde la clé Stripe.
Le dossier `ovh/` n'est pas servi par le site : il sert de copie de référence. Le vrai `config.php` n'est jamais mis dans ce dépôt.
