# BLOC 1 CONCEPTION

Sommaire complété

1. Glossaire
2. Règles de gestion
3. Parcours utilisateur
4. CDC fonctionnel
5. Données front
6. MCD léger
7. MCT
8. CDC technique
9. Architecture fichiers
10. Tests conceptuels

 

## 1 GLOSSAIRE

Produit = élément commercial sélectionnable par le client, identifié, nommé, tarifé, rattaché à une catégorie, affiché avec une image, pouvant être commandé seul ou entrer dans la
composition d’un menu. Un produit peut proposer des variantes ou options selon les règles métier

Menu = offre commerciale composée de plusieurs produits sélectionnés selon des règles de composition. Dans ce projet, un menu comprend un burger, un accompagnement, une
boisson et une sauce optionnelle. Le menu peut ensuite exister en variantes, par exemple Best Of ou Maxi Best Of

Catégorie = regroupement logique de produits utilisé pour organiser l’affichage et la navigation dans l’interface. Une catégorie possède un identifiant, un nom et une image, et regroupe
un ou plusieurs produits. Ce n’est pas un objet physique, mais un concept de classement.

Panier = ensemble temporaire des sélections du client avant validation finale. Il contient une ou plusieurs lignes de commande correspondant à des produits simples ou à des menus
configurés, avec leurs quantités, options et prix calculés. Le panier ne contient pas de catégories.

Commande = ensemble final des articles sélectionnés et validés par le client à partir du panier, identifié par un numéro de retrait, et transmis à l’API fictive pour préparation. Elle
représente la commande physique à produire.

Ligne de panier = élément du panier correspondant à un produit simple ou à un menu configuré, avec sa quantité, ses choix éventuels, son prix calculé, et pouvant être supprimé ou modifié
avant validation.

Variante = déclinaison d’un produit ou d’un menu qui modifie certaines caractéristiques commerciales ou de composition, par exemple la taille ou le type de menu.

Option = choix proposé au client lors de la sélection ou de la configuration d’un produit ou d’un menu, pouvant être obligatoire ou facultatif, et pouvant influencer la composition ou le
prix

# 2 REGLE DE GESTION

### MENU

RG-MENU-001 — Un menu est une offre composée

Un menu est une offre commerciale composée de plusieurs produits sélectionnés selon une structure prédéfinie.

RG-MENU-002 — Un menu doit contenir un burger

Tout menu valide doit contenir un et un seul burger.

RG-MENU-003 — Un menu doit contenir un accompagnement

Tout menu valide doit contenir un et un seul accompagnement.

RG-MENU-004 — Un menu doit contenir une boisson

Tout menu valide doit contenir une et une seule boisson.

RG-MENU-005 — La sauce du menu est optionnelle et unique

Un menu contient zéro ou une seule sauce, selon le choix du client.

RG-MENU-006 — Un menu incomplet ne peut pas être validé

Un menu ne peut pas être ajouté définitivement à la commande tant que tous les choix obligatoires n’ont pas été renseignés.

RG-MENU-007 — Les choix du menu sont faits par le client

Les composants du menu sont déterminés par les sélections effectuées par le client parmi les produits disponibles.

RG-MENU-008 — Le type de menu détermine la taille des éléments concernés

Le type de menu sélectionné par le client détermine la taille de la boisson et de l’accompagnement lorsqu’une règle de taille s’applique.

RG-MENU-009 — Un Best Of correspond à la taille normale

Un menu de type Best Of associe une taille normale à la boisson et à l’accompagnement concernés.

RG-MENU-010 — Un Maxi Best Of correspond à la grande taille

Un menu de type Maxi Best Of associe une grande taille à la boisson et à l’accompagnement concernés.

RG-MENU-011 — Le supplément est lié au type de menu

Le supplément tarifaire applicable aux grandes tailles est intégré via le choix d’un menu Maxi Best Of.

### PRODUIT

RG-PROD-001 — Un produit est identifié de manière unique

Chaque produit doit posséder un identifiant unique.

RG-PROD-002 — Un produit possède les informations minimales d’affichage

Chaque produit doit posséder au minimum un nom, un prix, une catégorie et une image.

RG-PROD-003 — Un produit appartient à une seule catégorie

Un produit est rattaché à une et une seule catégorie d’affichage.

RG-PROD-004 — Un produit peut être vendu à l’unité

Un produit peut être proposé à la vente comme élément seul, selon sa nature commerciale.

RG-PROD-005 — Un produit peut aussi être utilisé dans un menu

Un produit peut être utilisé comme composant d’un menu si les règles métier du menu l’autorisent.

RG-PROD-006 — Le prix d’un produit simple est son prix propre

Lorsqu’un produit est commandé seul, le prix appliqué est celui défini pour ce produit, hors supplément éventuel explicitement prévu.

RG-PROD-007 — Les produits affichés sont chargés dynamiquement depuis les données fournies

La liste des produits affichés par l’application est déterminée à partir des données JSON fournies.

RG-PROD-008 — Un produit peut avoir des variantes

Un produit peut proposer une ou plusieurs variantes selon sa nature commerciale, sans que cela soit obligatoire pour tous les produits.

RG-PROD-009 — Une variante de produit peut modifier le prix du produit

Lorsqu’un produit propose des variantes, la variante sélectionnée peut entraîner une modification du prix applicable.

### CATEGORIES

RG-CAT-001 — Une catégorie est identifiée de manière unique

Chaque catégorie doit posséder un identifiant unique.

RG-CAT-002 — Une catégorie possède les informations minimales d’affichage

Chaque catégorie doit posséder au minimum un nom et une image.

RG-CAT-003 — Une catégorie regroupe un ou plusieurs produits

Une catégorie permet de regrouper un ou plusieurs produits pour l’affichage et la navigation dans l’interface.

RG-CAT-004 — Les catégories affichées sont chargées dynamiquement depuis les données fournies

La liste des catégories affichées par l’application est déterminée à partir des fichiers JSON fournis.

RG-CAT-005 — Une catégorie sert à filtrer ou organiser l’affichage des produits

La sélection d’une catégorie permet d’afficher les produits qui lui sont rattachés.

RG-CAT-006 — Une catégorie ne peut pas être ajoutée au panier

Une catégorie est un concept de classement et de navigation. Elle ne constitue pas un élément commandable.

### PANIER

RG-PAN-001 — Le panier contient des lignes de panier

Le panier est composé d’une ou plusieurs lignes de panier correspondant à des produits simples ou à des menus configurés.

RG-PAN-002 — Une ligne de panier correspond à une sélection client

Chaque ligne de panier représente une sélection effectuée par le client, avec les informations nécessaires à son identification et à son calcul.

RG-PAN-003 — Une ligne de panier peut être supprimée avant validation

Le client peut supprimer une ligne du panier tant que la commande finale n’a pas été validée.

RG-PAN-004 — Le panier peut contenir plusieurs lignes

Le panier n’est pas limité à une seule ligne et peut contenir plusieurs produits simples et/ou plusieurs menus.

RG-PAN-005 — Le panier peut contenir à la fois des produits simples et des menus

Le panier peut regrouper des sélections de nature différente, dès lors qu’elles sont commandables.

RG-PAN-006 — Le panier ne contient pas de catégories

Le panier ne peut contenir que des éléments commandables. Les catégories n’y ont pas leur place.

RG-PAN-007 — Le prix total du panier est la somme des lignes

Le montant total du panier correspond à la somme des prix calculés de toutes les lignes qu’il contient.

RG-PAN-008 — Une ligne de panier doit contenir un prix calculé

Chaque ligne du panier doit porter le prix final correspondant à la sélection du client, en tenant compte des règles métier applicables.

RG-PAN-09 — Un panier vide ne peut pas être validé

La validation finale est impossible tant que le panier ne contient aucune ligne.

RG-PAN-010 — Seules des sélections complètes peuvent être ajoutées au panier

Un produit ou un menu ne peut être ajouté au panier que si les informations obligatoires de sa sélection ont été renseignées.

RG-PAN-011 — Une panier validé ne peux être modifier

Quand on panier a été validé, on ne peux pas rajouter ou enlevé d’élément

### COMMANDE

RG-CMD-001 — Une commande provient uniquement de la validation d’un panier

Une commande ne peut être créée qu’à partir d’un panier validé par le client.

RG-CMD-002 — Une commande ne peut pas être vide

Une commande doit contenir au moins une ligne issue du panier.

RG-CMD-003 — Une commande contient les lignes validées du panier

La commande reprend les produits simples et menus configurés présents dans le panier au moment de la validation.

RG-CMD-004 — Une commande doit être associée à un numéro de retrait

Toute commande validée doit comporter un numéro saisi par le client pour permettre sa récupération au comptoir.

RG-CMD-005 — Le numéro de retrait est obligatoire avant validation finale

La validation finale de la commande est impossible tant que le numéro de retrait n’a pas été renseigné.

RG-CMD-006 — Une commande contient son prix total

Le montant total de la commande correspond à la somme des prix des lignes validées.

RG-CMD-007 — Une commande doit être transformée en données JSON

Après validation, la commande doit être structurée sous la forme d’un objet JSON conforme au format attendu.

RG-CMD-008 — Une commande validée est transmise à l’API fictive

Une fois validée, la commande est envoyée à l’API fictive prévue pour la préparation.

RG-CMD-009 — La commande transmise reflète l’état final du panier

Les données envoyées doivent correspondre exactement aux sélections, quantités, choix et prix validés par le client.

RG-CMD-010 — Une commande validée n’est plus modifiable comme un panier

Après validation finale, la commande n’est plus considérée comme un panier modifiable dans le parcours normal.

RG-CMD-011 — Une commande ne peux pas être à 0 euros

Le prix d’une commande ne peux pas être zero euros 

### LIGNE DE PANIER

RG-LIG-001 — Une ligne de panier correspond à une seule sélection

Une ligne de panier représente une seule sélection client : soit un produit simple, soit un menu configuré.

RG-LIG-002 — Une ligne de panier doit être rattachée à un élément commandable

Une ligne de panier doit référencer un produit ou un menu pouvant être commandé.

RG-LIG-003 — Une ligne de panier possède une quantité

Chaque ligne de panier doit comporter une quantité strictement positive.

RG-LIG-004 — Une ligne de panier possède un prix calculé

Chaque ligne de panier doit contenir le prix final correspondant à la sélection du client, en tenant compte des règles métier applicables.

RG-LIG-005 — Une ligne de panier peut contenir des choix de configuration

Une ligne de panier peut contenir les choix effectués par le client, comme des options, variantes ou composants sélectionnés.

RG-LIG-006 — Une ligne de panier issue d’un menu doit contenir une configuration complète

Lorsqu’une ligne de panier correspond à un menu, elle doit intégrer tous les choix obligatoires nécessaires à la validité du menu.

RG-LIG-007 — Une ligne de panier peut être supprimée avant validation finale

Une ligne de panier peut être retirée du panier tant que la commande finale n’a pas été validée.

RG-LIG-008 — Deux lignes distinctes peuvent référencer le même produit

Le panier peut contenir plusieurs lignes distinctes portant sur un même produit ou un même menu si les sélections diffèrent ou si le parcours utilisateur les ajoute séparément.

RG-LIG-009 — Une ligne de panier conserve l’état de la sélection au moment de l’ajout

Une ligne de panier doit refléter exactement la sélection du client au moment où elle est ajoutée au panier.

RG-LIG-010 — Une ligne de panier doit conserver la variante choisie

Lorsqu’un produit avec variantes est ajouté au panier, la ligne de panier doit mémoriser la variante sélectionnée.

### VARIANTE

RG-VAR-001 — Un produit peut avoir zéro, une ou plusieurs variantes

Un produit peut ne proposer aucune variante, ou proposer une ou plusieurs variantes selon sa nature commerciale.

RG-VAR-002 — Une variante possède les informations nécessaires à son identification

Chaque variante doit pouvoir être identifiée par un libellé ou une désignation permettant de la distinguer des autres variantes du même produit.

RG-VAR-003 — Une variante peut modifier le prix du produit

La sélection d’une variante peut entraîner une modification du prix applicable au produit.

RG-VAR-005 — Une variante ne peut être sélectionnée que si elle existe pour le produit concerné

Le client ne peut sélectionner qu’une variante effectivement définie pour le produit choisi.

RG-VAR-006 — Le prix calculé d’une ligne doit tenir compte de la variante choisie

Si une variante impacte le prix, cet impact doit être intégré dans le prix final de la ligne de panier.

RG-VAR-007 — Une variante ne remplace pas le produit

Une variante est une déclinaison d’un produit et ne constitue pas un produit totalement indépendant dans le modèle métier.

### OPTION

RG-OPT-001 — Une option correspond à un choix proposé au client

Une option représente un choix de configuration proposé au client lors de la sélection d’un produit ou d’un menu.

RG-OPT-002 — Une option peut être obligatoire ou facultative

Selon les règles métier, une option peut devoir être renseignée obligatoirement ou rester facultative.

RG-OPT-003 — Une option est définie dans un contexte précis

Une option ne peut être proposée que dans le cadre d’un produit ou d’un menu qui la prévoit.

RG-OPT-004 — Une option propose une ou plusieurs valeurs possibles

Chaque option doit permettre au client de choisir parmi une ou plusieurs valeurs définies.

RG-OPT-005 — Seules les valeurs autorisées peuvent être sélectionnées

Le client ne peut choisir qu’une valeur effectivement disponible pour l’option concernée.

RG-OPT-006 — Une option obligatoire doit être renseignée avant validation

Lorsqu’une option est obligatoire, la sélection ne peut pas être validée tant qu’aucune valeur n’a été choisie.

RG-OPT-007 — Une option facultative peut rester vide

Lorsqu’une option est facultative, l’absence de choix ne bloque pas la validation.

RG-OPT-008 — Une option sélectionnée doit être conservée dans la ligne de panier

Lorsqu’un choix est effectué, la valeur retenue doit être mémorisée dans la ligne de panier correspondante.

RG-OPT-009 — Une option peut influencer le prix

Selon les règles métier, le choix d’une valeur d’option peut modifier le prix final de la sélection.

# 3 PARCOURS UTILISATEURS

PU-001 — Entrée dans l’application

Le client arrive sur l’écran d’accueil de la borne avec le choix de sur place ou à emporter

PU-002 — Accès à l’offre

Le client accède aux catégories et aux produits proposés par l’application.

PU-003 — Consultation d’une catégorie

Le client sélectionne une catégorie pour afficher les produits correspondants.

PU-004 — Sélection d’un produit ou d’un menu

Le client choisit soit un produit simple, soit un menu.

PU-005 — Configuration d’un menu

Si le client choisit un menu, il renseigne les choix obligatoires :

- accompagnement
- boisson
et éventuellement :
- une sauce optionnelle

PU-006 — Configuration d’un produit simple

Si le client choisit un produit simple disposant d’options ou de variantes, il renseigne les choix nécessaires avant l’ajout au panier.

PU-007 — Validation de la sélection

Une fois la sélection complète, le client ajoute le produit ou le menu au panier.

PU-008 — Consultation du panier

Le client consulte le contenu du panier et voit :

- les lignes du panier
- le prix de chaque ligne
- le montant total

PU-009 — Modification du panier

Le client peut :

- supprimer une ligne

PU-010 — Validation du panier

Lorsque le panier est jugé correct, le client lance la validation finale.

PU-011 — Saisie du numéro de retrait

Le client renseigne le numéro demandé pour permettre la récupération de la commande.

PU-012 — Validation finale de la commande

Si les conditions sont réunies, le panier est transformé en commande.

PU-013 — Envoi de la commande

La commande est convertie en JSON puis envoyée à l’API fictive.

PU-014 — Fin de parcours

L’application affiche un état final confirmant la prise en compte de la commande.



# 4 CDC FONCTIONNEL

[CDC FONCTIONNEL](https://www.notion.so/CDC-FONCTIONNEL-3517e0f70e9f80daae12c2d004af810e?pvs=21)



# 5 DONNÉES FRONT
 
Les données front correspondent aux informations chargées, affichées, conservées temporairement et transformées par l’application côté navigateur.  
Dans ce projet, elles proviennent principalement des fichiers JSON fournis, des choix effectués par le client pendant le parcours de commande et des calculs réalisés par l’interface.
 
L’application ne s’appuie pas sur une base de données côté front. Les données sont chargées dynamiquement depuis les fichiers JSON, puis utilisées en mémoire pour afficher l’offre, 
constituer le panier et préparer la commande finale à transmettre à l’API fictive.
 
 ## 5.1 Sources de données
 
 ### Fichier `categories.json`
 
 Le fichier `categories.json` fournit la liste des catégories affichées dans l’interface.
 
 Chaque catégorie contient au minimum :
 
 - `id` : identifiant unique de la catégorie ;
 - `title` : nom de la catégorie affichée ;
 - `image` : chemin de l’image associée à la catégorie.
 
 Catégories présentes dans les données fournies :
 
 - menus ;
 - boissons ;
 - burgers ;
 - frites ;
 - encas ;
 - wraps ;
 - salades ;
 - desserts ;
 - sauces.
 
 Ces catégories servent à organiser la navigation et à filtrer les produits affichés au client.
 
 ### Fichier `produits.json`
 
 Le fichier `produits.json` fournit les produits et menus disponibles.  
 Les produits sont regroupés par type dans le JSON, par exemple :
 
 - `menus` ;
 - `burgers` ;
 - `boissons` ;
 - `frites` ;
 - `encas` ;
 - `desserts` ;
 - `sauces` ;
 - `salades` ;
 - `wraps`.
 
 Chaque produit contient au minimum :
 
 - `id` : identifiant unique du produit ;
 - `nom` : nom affiché du produit ;
 - `prix` : prix du produit ;
 - `image` : chemin de l’image associée.
 
Le type ou la catégorie du produit est déduit côté front à partir du groupe dans lequel il se trouve dans le fichier JSON.

Exemple : un produit présent dans le groupe `burgers` est interprété comme un burger, même si l’objet produit ne contient pas directement un champ `categorie`.

### Correspondance entre `categories.json` et `produits.json`

Dans les données fournies pour ce projet, la correspondance entre une catégorie affichée et un groupe de `produits.json` repose sur la valeur textuelle de `category.title`.

La règle de correspondance retenue est donc la suivante :

- `categories.title = "menus"` correspond au groupe `produits.menus` ;
- `categories.title = "boissons"` correspond au groupe `produits.boissons` ;
- `categories.title = "burgers"` correspond au groupe `produits.burgers` ;
- `categories.title = "frites"` correspond au groupe `produits.frites` ;
- `categories.title = "encas"` correspond au groupe `produits.encas` ;
- `categories.title = "wraps"` correspond au groupe `produits.wraps` ;
- `categories.title = "salades"` correspond au groupe `produits.salades` ;
- `categories.title = "desserts"` correspond au groupe `produits.desserts` ;
- `categories.title = "sauces"` correspond au groupe `produits.sauces`.

L’identifiant numérique de la catégorie n’est donc pas utilisé pour relier une catégorie à un groupe de produits.  
Dans ce projet, la clé de correspondance est le libellé `title`.
 
 ## 5.2 Données affichées dans l’interface
 
 L’application utilise les données chargées pour afficher :
 
 - les catégories disponibles ;
 - les produits associés à une catégorie sélectionnée ;
 - les menus disponibles ;
 - les images des produits et catégories ;
 - les prix des produits ;
 - les choix nécessaires à la configuration d’un menu ;
 - les lignes du panier ;
 - le montant total ;
 - le numéro de commande ou de retrait lorsque celui-ci est renseigné ;
 - l’écran final de confirmation.
 
 Les données affichées doivent rester cohérentes avec les données JSON fournies et avec la maquette.
 
 ## 5.3 Données de navigation
 
 Pendant le parcours, l’application doit conserver temporairement certaines informations de navigation :
 
 - catégorie actuellement sélectionnée ;
 - produit ou menu actuellement sélectionné ;
 - étape actuelle de configuration d’un menu ;
 - retour possible vers l’écran précédent ;
 - état du parcours : accueil, catalogue, configuration, panier, validation, confirmation.
 
 Ces données permettent à l’interface de savoir quel écran afficher et quelles actions proposer au client.
 
 ## 5.4 Données liées au type de commande
 
 Lorsque la maquette le prévoit, l’application manipule le type de commande choisi par le client :
 
 - sur place ;
 - à emporter.
 
 Cette donnée est conservée pendant le parcours et peut être intégrée à la commande finale transmise.
 
 ## 5.5 Données de configuration d’un menu
 
 Lorsqu’un client sélectionne un menu, l’application doit conserver les choix effectués pendant la configuration.
 
Une configuration de menu peut contenir :
 
 - le menu sélectionné ;
 - le type de menu choisi, par exemple Best Of ou Maxi Best Of ;
- le produit principal du menu ;
- l’accompagnement choisi ;
- la boisson choisie ;
- la sauce optionnelle choisie, si elle est sélectionnée ;
- la taille retenue pour l’accompagnement et la boisson ;
- le prix calculé du menu.

Un menu ne peut être ajouté au panier que si les choix obligatoires sont renseignés.

Dans les données fournies, un menu existe comme produit dans le groupe `menus`, mais sa composition détaillée n’est pas décrite dans `produits.json`.  
La composition d’un menu est donc déduite à partir des règles métier du sujet d’examen :

- un menu est composé d’un burger, d’un accompagnement, d’une boisson et d’une sauce optionnelle ;
- l’accompagnement provient des groupes `frites` et `salades` ;
- la boisson provient du groupe `boissons` ;
- la sauce provient du groupe `sauces` ;
- le choix Best Of / Maxi Best Of et la gestion des tailles ne sont pas portés par les objets JSON sources et doivent être gérés par l’application front selon les règles définies dans le sujet.
 
 ## 5.6 Données du panier
 
 Le panier est une donnée temporaire conservée côté front pendant le parcours utilisateur.
 
 Il contient une ou plusieurs lignes de panier.
 
 Chaque ligne de panier doit contenir :
 
 - un identifiant de ligne ;
 - le type de sélection : produit simple ou menu configuré ;
 - le nom de l’élément sélectionné ;
 - la quantité ;
 - les choix effectués par le client ;
 - le prix unitaire ;
 - le prix calculé de la ligne ;
 - l’image utilisée pour l’affichage, si nécessaire.
 
 Le panier doit permettre :
 
 - l’ajout d’un produit simple ;
 - l’ajout d’un menu configuré ;
 - la suppression d’une ligne ;
 - le recalcul du total après modification ;
 - l’interdiction de validation si le panier est vide.
 
 ## 5.7 Données calculées
 
Certaines données ne sont pas directement présentes dans les fichiers JSON. Elles sont calculées par l’application.

Données calculées principales :

- prix final d’une ligne de panier ;
- supplément éventuel lié au choix Maxi Best Of ;
- total du panier ;
- total de la commande ;
- état de validité d’une sélection ;
- état de validité du panier ;
- état de validité de la commande.

Le calcul du total doit toujours correspondre à la somme des lignes présentes dans le panier.

Pour éviter les imprécisions liées aux nombres décimaux en JavaScript, les calculs monétaires doivent être réalisés en centimes.

Le montant total n’est donc pas une donnée de référence stockée.  
La donnée de référence persistée côté front est le contenu du panier, à partir duquel le total est recalculé.

Règle d’arrondi monétaire :

- les prix issus de `produits.json` sont exprimés en euros décimaux à deux chiffres après la virgule ;
- au chargement, chaque prix est converti en centimes entiers en arrondissant au centime le plus proche, selon la formule équivalente à `Math.round(prix × 100)` ;
- tous les calculs intermédiaires (multiplication par la quantité, supplément Maxi Best Of, total ligne, total panier) restent exprimés en centimes entiers ;
- le format affiché à l’écran est ensuite reconstitué à partir des centimes au format `XX,XX €`, avec deux chiffres après la virgule.

Attention : si le prix d’un menu Maxi Best Of est déjà intégré dans les données fournies, l’application ne doit pas appliquer deux fois le supplément. La règle de calcul doit rester 
cohérente avec les données réellement exploitées.
 
 ## 5.8 Données de validation
 
Avant la validation finale, l’application doit manipuler les données suivantes :

- contenu final du panier ;
- montant total ;
- type de commande ;
- numéro de retrait saisi par le client ;
- état de validité de la commande.

La commande ne peut pas être validée si :

- le panier est vide ;
- une sélection est incomplète ;
 - le numéro de retrait n’est pas renseigné ;
 - les données nécessaires à la commande sont incohérentes.
 
 ## 5.9 Données JSON de sortie

Après validation finale, l’application doit générer un objet JSON représentant la commande à transmettre à l’API fictive.

### 5.9.1 Structure du payload

Le payload JSON transmis à l’API fictive est structuré comme suit :

| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| `numeroRetrait` | `string` | Oui | Numéro de trois chiffres exactement saisi par le client pour identifier sa commande au comptoir |
| `typeCommande` | `string` | Oui | Type de commande : `"surPlace"` ou `"aEmporter"` |
| `totalCentimes` | `number` (entier) | Oui | Montant total de la commande en centimes |
| `lignes` | `array` | Oui | Tableau des lignes de commande (produits simples et menus configurés) |

#### Structure d’une ligne de commande

Chaque élément du tableau `lignes` représente une ligne de panier validée.

Champs communs à toutes les lignes :

| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| `type` | `string` | Oui | Type de ligne : `"produit"` (produit simple) ou `"menu"` (menu configuré) |
| `quantite` | `number` (entier) | Oui | Quantité commandée de cette ligne (≥ 1) |
| `prixUnitaireCentimes` | `number` (entier) | Oui | Prix unitaire de la ligne en centimes |
| `prixTotalCentimes` | `number` (entier) | Oui | Prix total de la ligne (`quantite × prixUnitaireCentimes`) |

Champs spécifiques pour une ligne de type `"produit"` :

| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| `produit` | `object` | Oui | Objet décrivant le produit commandé |
| `produit.id` | `number` | Oui | Identifiant unique du produit |
| `produit.nom` | `string` | Oui | Nom du produit |

Champs spécifiques pour une ligne de type `"menu"` :

| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| `menu` | `object` | Oui | Objet décrivant la configuration complète du menu |
| `menu.id` | `number` | Oui | Identifiant unique du menu de base |
| `menu.nom` | `string` | Oui | Nom du menu de base |
| `menu.typeMenu` | `string` | Oui | Type de menu : `"bestOf"` ou `"maxiBestOf"` |
| `menu.burger` | `object` | Oui | Burger principal du menu |
| `menu.burger.id` | `number` | Oui | Identifiant du burger |
| `menu.burger.nom` | `string` | Oui | Nom du burger |
| `menu.accompagnement` | `object` | Oui | Accompagnement choisi |
| `menu.accompagnement.id` | `number` | Oui | Identifiant de l’accompagnement |
| `menu.accompagnement.nom` | `string` | Oui | Nom de l’accompagnement |
| `menu.accompagnement.taille` | `string` | Oui | Taille : `"normale"` (Best Of) ou `"grande"` (Maxi Best Of) |
| `menu.boisson` | `object` | Oui | Boisson choisie |
| `menu.boisson.id` | `number` | Oui | Identifiant de la boisson |
| `menu.boisson.nom` | `string` | Oui | Nom de la boisson |
| `menu.boisson.taille` | `string` | Oui | Taille : `"normale"` (Best Of) ou `"grande"` (Maxi Best Of) |
| `menu.sauce` | `object` ou `null` | Non | Sauce choisie (optionnelle) |
| `menu.sauce.id` | `number` | Oui (si sauce présente) | Identifiant de la sauce |
| `menu.sauce.nom` | `string` | Oui (si sauce présente) | Nom de la sauce |

### 5.9.2 Règles de construction du payload

- **RG-JSON-001** — Le payload est construit au moment de la validation finale, à partir de l’état final du panier et après saisie du numéro de retrait.
- **RG-JSON-002** — Tous les montants (`totalCentimes`, `prixUnitaireCentimes`, `prixTotalCentimes`) sont exprimés en centimes entiers.
- **RG-JSON-003** — Le champ `quantite` est un entier strictement positif (≥ 1).
- **RG-JSON-004** — Le champ `totalCentimes` est égal à la somme de tous les `prixTotalCentimes` des lignes.
- **RG-JSON-005** — Chaque ligne du payload reflète exactement la sélection conservée dans le panier.
- **RG-JSON-006** — Si `typeMenu = "bestOf"`, les `taille` de l’accompagnement et de la boisson valent `"normale"`.
- **RG-JSON-007** — Si `typeMenu = "maxiBestOf"`, les `taille` de l’accompagnement et de la boisson valent `"grande"`.
- **RG-JSON-008** — Le champ `menu.sauce` peut être `null` si aucune sauce n’a été sélectionnée.
- **RG-JSON-009** — Les `id` du payload correspondent aux identifiants définis dans `produits.json`.
- **RG-JSON-010** — Le `prixUnitaireCentimes` d’un menu Maxi Best Of intègre déjà le supplément lié à la grande taille. Le supplément ne doit pas être ajouté une seconde fois lors de la construction du payload.

### 5.9.3 Exemple de payload

```json
{
  "numeroRetrait": "042",
  "typeCommande": "surPlace",
  "totalCentimes": 1730,
  "lignes": [
    {
      "type": "produit",
      "quantite": 1,
      "prixUnitaireCentimes": 680,
      "prixTotalCentimes": 680,
      "produit": {
        "id": 14,
        "nom": "Le 280"
      }
    },
    {
      "type": "menu",
      "quantite": 1,
      "prixUnitaireCentimes": 1050,
      "prixTotalCentimes": 1050,
      "menu": {
        "id": 4,
        "nom": "Menu Big Mac",
        "typeMenu": "maxiBestOf",
        "burger": {
          "id": 17,
          "nom": "Big Mac"
        },
        "accompagnement": {
          "id": 37,
          "nom": "Grande Frite",
          "taille": "grande"
        },
        "boisson": {
          "id": 27,
          "nom": "Coca Cola",
          "taille": "grande"
        },
        "sauce": {
          "id": 53,
          "nom": "Classic Barbecue"
        }
      }
    }
  ]
}
```

Les données transmises doivent correspondre exactement au contenu validé par le client.
 
 ## 5.10 États techniques du front
 
 En plus des données métier, l’application front-end manipule des données d’état techniques permettant de piloter l’interface, les transitions d’écran et les actions disponibles pour
 l’utilisateur.
 
 Ces états ne proviennent pas directement des fichiers JSON métiers. Ils sont générés et maintenus côté front afin d’assurer le bon fonctionnement de l’application.
 
Pour éviter les chevauchements entre états métier et états techniques, la conception retient quatre familles d’états :

- **état du chargement du catalogue** : `idle`, `loading`, `ready`, `error` ;
- **état de configuration d’une sélection** : `incomplete`, `valid`, `blocked` ;
- **état du panier** : `empty`, `ready` ;
- **état de transmission finale** : `idle`, `submitting`, `submitted`, `error`.

Ces états sont temporaires, manipulés en mémoire par l’interface, et ne constituent pas des données métier persistées.

Le détail de leur orchestration, de leurs transitions et des composants qui les pilotent relève du CDC technique.
 
  ## 5.11 Stockage local des données côté front
 
 L’application utilise le `localStorage` comme mécanisme de persistance locale légère pour conserver temporairement l’état de la commande côté front.
 
 Ce stockage local ne constitue pas une base de données métier.  
 Il sert uniquement à mémoriser les données nécessaires au parcours utilisateur dans le navigateur de la borne.
 
### Clés de stockage local retenues

Les clés de stockage local utilisées dans le projet sont les suivantes :

- `wacdo.orderType` : type de commande choisi (`surPlace` ou `aEmporter`) ;
- `wacdo.cart` : contenu complet du panier ;
- `wacdo.withdrawNumber` : numéro de retrait saisi avant validation finale.

Le stockage local ne conserve pas les états techniques de l’interface (`loading`, `submitting`, `error`, etc.), ni les états de navigation intermédiaires (catégorie courante, étape affichée, brouillon temporaire de sélection).  
Ces éléments relèvent de l’état d’interface en mémoire, pas d’une donnée métier à persister.

### Règles de gestion du stockage local

Le stockage local du projet suit les règles suivantes :

- au démarrage de l’application, `wacdo.cart` est initialisé avec une structure vide s’il est absent ; les autres clés ne sont renseignées qu’au moment où la donnée existe réellement ;
- après chaque ajout, suppression ou modification d’une ligne, le contenu du panier est immédiatement réécrit dans le `localStorage` ;
- le montant total n’est pas stocké comme donnée de référence ; il est recalculé à partir du panier pour l’affichage puis recalculé de nouveau au moment de la validation finale ;
- en cas de rechargement de la page, l’application restaure les données persistées encore valides : type de commande, panier et numéro de retrait ;
- lors d’un abandon explicite du parcours, les données de commande en cours sont supprimées du `localStorage` ;
- après validation finale réussie de la commande, les données temporaires de commande sont supprimées du `localStorage` ;
- en cas de remise à zéro de la borne ou d’inactivité prolongée, les données temporaires de commande sont supprimées du `localStorage` ;
- au retour à l’écran d’accueil, les données de commande précédentes ne doivent plus rester actives dans le `localStorage`.
 
 ### Positionnement du stockage local dans l’architecture
 
 Le `localStorage` conserve uniquement les données temporaires du parcours utilisateur.  
 Les fichiers JSON fournis restent la source de vérité du catalogue.
 
On distingue donc :

- les données sources : catégories et produits chargés depuis les fichiers JSON ;
- les données d’état : informations techniques pilotant l’interface ;
- les données locales temporaires : type de commande, panier et numéro de retrait.
 
 ## 5.12 Synthèse des données front
 
 | Donnée | Origine | Utilisation |
 |---|---|---|
 | Catégories | `categories.json` | Navigation et filtrage de l’offre |
 | Produits | `produits.json` | Affichage et sélection des produits |
 | Menus | `produits.json` | Affichage et configuration des menus |
 | Images | Ressources fournies | Affichage visuel de l’interface |
 | Type de commande | Choix utilisateur | Sur place ou à emporter |
 | Configuration menu | Choix utilisateur | Construction d’un menu complet |
| États techniques | État temporaire front en mémoire | Pilotage de l’interface et des actions |
 | Panier | État temporaire front | Stockage des sélections avant validation |
 | Ligne de panier | État temporaire front | Détail d’un produit ou menu sélectionné |
 | Total | Calcul front | Affichage du montant à payer |
 | Numéro de retrait | Saisie utilisateur | Identification de la commande |
 | Stockage local | `localStorage` | Persistance locale légère du parcours |
 | Commande JSON | Génération front | Transmission à l’API fictive |


# 6 MCD léger

Le MCD léger ci-dessous a pour objectif de représenter les principales données métier du projet, sans entrer dans un niveau de détail trop technique.

Dans ce modèle, un **menu** est considéré comme un **type de produit**.  
Ce choix permet de garder un modèle simple et cohérent avec les données JSON fournies.

## 6.1 Entités retenues

### `CATEGORIE`

Représente les familles de produits affichées sur la borne.

Attributs principaux :

- `idCategorie`
- `nomCategorie`
- `imageCategorie`

### `PRODUIT`

Représente un élément sélectionnable sur la borne.

Un produit peut être :

- un produit simple ;
- un menu ;
- une boisson ;
- un accompagnement ;
- une sauce.

Attributs principaux :

- `idProduit`
- `nomProduit`
- `prixBase`
- `imageProduit`
- `typeProduit`

### `PANIER`

Représente la commande en cours avant validation finale.

Attributs principaux :

- `idPanier`
- `montantTotal`

### `LIGNE_PANIER`

Représente une sélection concrète ajoutée au panier.

Attributs principaux :

- `idLignePanier`
- `quantite`
- `prixUnitaire`
- `prixLigne`

### `CONFIGURATION_MENU`

Représente les choix complémentaires d’un menu configuré.

Attributs principaux :

- `idConfigurationMenu`
- `typeMenu`
- `tailleBoisson`
- `tailleAccompagnement`

### `COMMANDE`

Représente le panier une fois validé par le client.

Attributs principaux :

- `idCommande`
- `montantTotalCommande`
- `typeCommande`
- `numeroRetrait`

## 6.2 Relations retenues

### `CATEGORIE` — regroupe — `PRODUIT`

- une `CATEGORIE` regroupe `0,n` `PRODUITS`
- un `PRODUIT` appartient à `1,1` `CATEGORIE`

### `PANIER` — contient — `LIGNE_PANIER`

- un `PANIER` contient `1,n` `LIGNES_PANIER`
- une `LIGNE_PANIER` appartient à `1,1` `PANIER`

### `LIGNE_PANIER` — concerne — `PRODUIT`

- une `LIGNE_PANIER` concerne `1,1` `PRODUIT`
- un `PRODUIT` peut apparaître dans `0,n` `LIGNES_PANIER`

### `LIGNE_PANIER` — est complétée par — `CONFIGURATION_MENU`

- une `LIGNE_PANIER` peut être complétée par `0,1` `CONFIGURATION_MENU`
- une `CONFIGURATION_MENU` appartient à `1,1` `LIGNE_PANIER`

Cette relation permet de distinguer :

- une ligne simple sans configuration particulière ;
- une ligne correspondant à un menu configuré.

### `CONFIGURATION_MENU` — choisit comme accompagnement — `PRODUIT`

- une `CONFIGURATION_MENU` choisit `1,1` `PRODUIT` comme accompagnement
- un `PRODUIT` peut être choisi comme accompagnement dans `0,n` `CONFIGURATIONS_MENU`

### `CONFIGURATION_MENU` — choisit comme boisson — `PRODUIT`

- une `CONFIGURATION_MENU` choisit `1,1` `PRODUIT` comme boisson
- un `PRODUIT` peut être choisi comme boisson dans `0,n` `CONFIGURATIONS_MENU`

### `CONFIGURATION_MENU` — choisit comme sauce — `PRODUIT`

- une `CONFIGURATION_MENU` choisit `0,1` `PRODUIT` comme sauce
- un `PRODUIT` peut être choisi comme sauce dans `0,n` `CONFIGURATIONS_MENU`

### `COMMANDE` — est issue de — `PANIER`

- une `COMMANDE` est issue de `1,1` `PANIER`
- un `PANIER` peut donner `0,1` `COMMANDE`

Cette relation traduit le fait qu’une commande correspond au panier validé par le client.

## 6.3 Lecture simplifiée du modèle

Le modèle peut se lire simplement ainsi :

- une catégorie contient des produits ;
- le client remplit un panier ;
- le panier contient des lignes ;
- chaque ligne correspond à un produit ;
- si la ligne est un menu, elle possède une configuration ;
- cette configuration choisit un accompagnement, une boisson et éventuellement une sauce ;
- quand le panier est validé, il devient une commande.

## 6.4 Schéma textuel du MCD léger

```text
CATEGORIE (idCategorie, nomCategorie, imageCategorie)
    0,n
      |
      | regroupe
      |
    1,1
PRODUIT (idProduit, nomProduit, prixBase, imageProduit, typeProduit)

PANIER (idPanier, montantTotal)
    1,n
      |
      | contient
      |
    1,1
LIGNE_PANIER (idLignePanier, quantite, prixUnitaire, prixLigne)
    1,1 ---------------- concerne ---------------- 0,n PRODUIT

LIGNE_PANIER 0,1 -------- est complétée par -------- 1,1 CONFIGURATION_MENU
CONFIGURATION_MENU (idConfigurationMenu, typeMenu, tailleBoisson, tailleAccompagnement)

CONFIGURATION_MENU 1,1 ---- choisit accompagnement ---- 0,n PRODUIT
CONFIGURATION_MENU 1,1 ---- choisit boisson ----------- 0,n PRODUIT
CONFIGURATION_MENU 0,1 ---- choisit sauce ------------- 0,n PRODUIT

COMMANDE (idCommande, montantTotalCommande, typeCommande, numeroRetrait)
    1,1
      |
      | est issue de
      |
    0,1
PANIER
```

## 6.5 Choix de simplification retenus

Ce MCD est volontairement léger.

Les choix de simplification retenus sont les suivants :

- le **menu** n’est pas modélisé comme une entité séparée : il est traité comme un type de produit ;
- le **type de commande** et le **numéro de retrait** sont portés directement par l’entité `COMMANDE` ;
- les détails de transformation du panier en commande relèvent du traitement fonctionnel et n’ont pas été développés davantage ici.


# 7 CDC technique

## 7.1 Organisation générale de la logique front

La logique front est organisée autour de modules fonctionnels distincts, chacun responsable d’un rôle précis dans le parcours utilisateur.

Les composants logiques principaux sont les suivants :

- **chargeur de données** : récupère `categories.json` et `produits.json` via Ajax ;
- **normaliseur de catalogue** : transforme les données sources dans un format cohérent et exploitable côté front ;
- **gestionnaire de configuration de menu** : porte les choix Best Of / Maxi Best Of, accompagnement, boisson et sauce ;
- **gestionnaire de panier** : ajoute, supprime et met à jour les lignes de commande ;
- **service de calcul** : calcule les prix des lignes et le total du panier ;
- **service de stockage local** : persiste temporairement `orderType`, `cart` et `withdrawNumber` ;
- **constructeur de commande** : produit le JSON final à transmettre à l’API fictive ;
- **contrôleur d’interface** : pilote les écrans, les transitions et l’activation des actions utilisateur.

## 7.2 Chargement et normalisation des données

Au démarrage de l’application, le chargeur de données récupère les fichiers JSON fournis depuis leur URL directe sur le serveur, conformément au sujet.

Après chargement :

- les catégories sont lues depuis `categories.json` ;
- les produits et menus sont lus depuis `produits.json` ;
- la correspondance entre catégorie et groupe de produits est établie à partir de `categories.title` ;
- les prix décimaux des fichiers JSON sont convertis en centimes entiers, arrondis au centime le plus proche ;
- les chemins d’images sont conservés comme données sources à afficher.

Si le chargement échoue ou si le format JSON ne correspond pas à la structure attendue, l’interface doit passer dans un état d’erreur bloquant.

## 7.3 Logique de configuration des menus

La configuration d’un menu ne peut pas se limiter aux données brutes de `produits.json`, car la composition détaillée du menu n’est pas fournie telle quelle dans les objets JSON.

La logique front doit donc appliquer les règles suivantes :

- un menu sélectionné dans `produits.menus` ouvre un parcours de configuration dédié ;
- le burger principal du menu est déduit à partir du menu sélectionné, par convention de nommage entre le menu et le burger correspondant, ou via une table de correspondance dédiée si nécessaire ;
- l’accompagnement est choisi parmi les produits des groupes `frites` et `salades` ;
- la boisson est choisie dans le groupe `boissons` ;
- la sauce est choisie dans le groupe `sauces` ;
- le type de menu (`bestOf` ou `maxiBestOf`) détermine les tailles applicables et le supplément éventuel défini par les règles métier ;
- un menu ne peut être ajouté au panier qu’une fois sa configuration obligatoire entièrement renseignée.

## 7.4 Logique de calcul du panier

Le calcul des montants repose sur un service de calcul unique.

Ce service applique les règles suivantes :

- il travaille uniquement en centimes entiers ;
- il calcule le prix d’une ligne à partir de son contenu et de sa quantité ;
- il applique, si nécessaire, le supplément lié au type de menu retenu ;
- il produit le total du panier par somme des lignes ;
- il est utilisé après chaque modification du panier pour mettre à jour le montant affiché ;
- il est réutilisé lors de la validation finale pour produire le montant transmis dans la commande JSON.

Le montant total n’est jamais une donnée de référence stockée dans le `localStorage`.

## 7.5 Validation finale et construction de la commande

Lors de la validation finale, l’application doit :

- relire les données persistées utiles (`orderType`, `cart`, `withdrawNumber`) ;
- vérifier que le panier n’est pas vide ;
- vérifier que chaque ligne est complète et cohérente ;
- recalculer le total à partir du panier ;
- construire le payload JSON final conforme au format défini en section 5.9 ;
- transmettre ce payload à l’API fictive.

Si une incohérence est détectée, la validation finale est bloquée et l’interface doit afficher un état ou un message d’erreur approprié.

## 7.6 Persistance locale et remise à zéro

Le service de stockage local applique les règles suivantes :

- initialiser `wacdo.cart` avec une structure vide si la clé n’existe pas encore ;
- réécrire `wacdo.cart` après chaque modification du panier ;
- enregistrer `wacdo.orderType` et `wacdo.withdrawNumber` uniquement lorsque ces données existent ;
- restaurer ces données au rechargement si elles sont encore valides ;
- supprimer les données persistées lors d’un abandon explicite du parcours ;
- supprimer les données persistées après validation finale réussie ;
- supprimer les données persistées lors d’une remise à zéro de la borne ou d’une inactivité prolongée.

## 7.7 Gestion technique des états d’interface

Les états techniques sont pilotés par le contrôleur d’interface.

Ce contrôleur doit au minimum :

- distinguer le chargement du catalogue, la configuration d’une sélection, l’état du panier et la transmission finale ;
- activer ou désactiver les boutons selon l’état courant ;
- empêcher les actions invalides ;
- afficher les retours de succès, de blocage ou d’erreur ;
- garantir un retour propre à l’écran d’accueil après abandon, confirmation ou remise à zéro.

# 8 Architecture fichiers

## 8.1 Architecture retenue

La conception retient l’architecture simple définie dans le CDC technique. Cette architecture est la référence pour le développement du Bloc 1.

Elle repose sur quatre pages :

- `index.html` pour l’entrée dans le parcours ;
- `commande.html` pour la prise de commande complète ;
- `chevalet.html` pour la saisie du numéro de retrait ;
- `remerciement.html` pour l’écran final.

La page principale `commande.html` est pilotée par `commande.js`. Ce fichier orchestre les autres modules sans porter seul les règles métier, le stockage ou la génération du JSON final.

### Arborescence retenue

```text
index.html
commande.html
chevalet.html
remerciement.html

css/
├── components/
│   ├── categories.css
│   ├── products.css
│   ├── cart.css
│   └── modal.css
└── pages/
  ├── index.css
  ├── commande.css
  ├── chevalet.css
  └── remerciement.css

js/
├── accueil.js
├── commande.js
├── chevalet.js
├── catalog-service.js
├── modal.js
├── cart.js
├── storage.js
└── payload-builder.js
```

### Rôle des fichiers principaux

`accueil.js` gère le choix entre sur place et à emporter, vide les données d’une ancienne commande, enregistre le type de commande et ouvre `commande.html`.

`commande.js` pilote la page de commande. Il charge le catalogue, affiche les catégories et les produits, ouvre la modale, ajoute les sélections validées au panier, met à jour l’affichage et contrôle le passage vers `chevalet.html`.

`catalog-service.js` charge `categories.json` et `produits.json`, normalise les données utiles et fournit les catégories, produits, menus, boissons, accompagnements et sauces au reste de l’interface.

`modal.js` gère les modales de sélection. Il affiche les choix nécessaires, maintient le brouillon de sélection, bloque les sélections incomplètes et retourne une sélection complète à `commande.js`.

`cart.js` transforme une sélection complète en ligne de panier, ajoute ou supprime les lignes, calcule les montants en centimes et retourne l’état courant du panier.

`storage.js` gère uniquement le `localStorage` avec les clés `wacdo.orderType`, `wacdo.cart` et `wacdo.withdrawNumber`.

`chevalet.js` gère la saisie du numéro de retrait. Le numéro contient exactement trois chiffres. Après validation de la saisie, `chevalet.js` transmet le numéro à `payload-builder.js`.

`payload-builder.js` relit les données utiles, contrôle la cohérence du panier, construit le payload JSON final et déclenche l’envoi vers l’API fictive.

`remerciement.html` affiche la confirmation finale et permet de lancer une nouvelle commande. Cette page ne porte pas de logique métier dédiée.

### Principe de fonctionnement global

1. `index.html` enregistre le type de commande et ouvre `commande.html`.
2. `commande.html` charge les catégories et produits depuis les JSON.
3. Le client sélectionne un produit simple ou configure un menu.
4. Une sauce est optionnelle et limitée à une seule sélection par menu.
5. Le panier conserve les lignes validées et le total recalculé.
6. Le panier validé mène vers `chevalet.html`.
7. `chevalet.html` accepte uniquement un numéro de retrait de trois chiffres exactement.
8. `payload-builder.js` construit le JSON final et déclenche l’envoi fictif.
9. `remerciement.html` s’affiche uniquement après validation de la commande.

Cette architecture remplace les variantes plus découpées avec dossiers `domain`, `state`, `ui` ou `services`. Ces variantes ne sont pas retenues pour le développement du Bloc 1.

# 9 Tests conceptuels

 
