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
