# CDC FONCTIONNEL

## Sommaire

1. Contexte
2. Objectif
3. Périmètre
4. Acteurs
5. Glossaire
6. Règles de gestion
7. Parcours utilisateur
8. Cas alternatifs
9. Exigences fonctionnelles
10. Contraintes fonctionnelles
11. Contraintes non fonctionnelles
12. Données manipulées

## Contexte

Le projet consiste à concevoir et réaliser la partie front-end d’une application de prise de commande destinée aux bornes numériques des restaurants Wacdo. Cette application a pour
objectif de permettre à un client de consulter l’offre disponible, de sélectionner des produits ou des menus, de constituer un panier, puis de valider sa commande directement depuis
l’interface de la borne.

Le projet s’inscrit dans un contexte de commande autonome sur borne. Il interagit uniquement avec une interface visuelle conçue pour être simple, claire, fluide et adaptée à un usage tactile. L’application doit donc reproduire une expérience de commande complète, depuis l’écran d’accueil jusqu’à la validation finale, en respectant la logique métier d’une restauration rapide.

Les éléments nécessaires à la réalisation du projet sont fournis en amont. Ils comprennent :

- une maquette servant de référence visuelle et fonctionnelle ;
- des ressources graphiques (photos de produits, icônes, visuels d’interface) ;
- des fichiers JSON contenant les données structurées de base, notamment les catégories et les produits.

Ces données ne doivent pas être codées en dur dans l’application. Elles doivent être placées sur le serveur mis à disposition puis récupérées dynamiquement côté front via une logique Ajax. Dans le cadre du projet, l’URL d’accès aux données correspond directement à l’URL des fichiers JSON fournis. L’application doit donc être capable de charger, interpréter et afficher dynamiquement ces données afin d’alimenter les différentes vues de l’interface.

Sur le plan fonctionnel, l’application doit permettre au client de :

- choisir entre différents types de commande, notamment sur place ou à emporter si ce choix est présent dans la maquette ;
- naviguer entre les catégories de produits ;
- consulter les produits proposés ;
- sélectionner un produit simple ou un menu ;
- configurer les choix nécessaires à un menu ;
- constituer et consulter un panier ;
- supprimer une sélection du panier ;
- valider sa commande en renseignant un numéro de retrait.

Les menus constituent une partie importante du besoin fonctionnel. Un menu est composé d’un produit principal déjà déterminé par le type de menu choisi, auquel s’ajoutent des choix
complémentaires réalisés par le client, notamment un accompagnement, une boisson et une sauce optionnelle. Certaines sélections, en particulier les boissons et accompagnements,
peuvent exister sous différentes tailles selon les règles métier définies. Le comportement attendu doit être cohérent avec la logique de l’offre commerciale représentée dans les données
et dans la maquette.

La validation finale se fait par la saisie d’un numéro, associé à la commande afin de
permettre son identification au comptoir. Une fois la commande validée, l’application doit produire un objet JSON représentant fidèlement le contenu final de la commande à préparer, puis
transmettre cet objet à une API fictive. seule la partie front-end de l’échange doit être prise en charge.

Le projet doit être réalisé avec les technologies HTML, CSS et JavaScript. L’interface est pensée pour une résolution de référence de 1920x1080, mais elle doit rester responsive et
s’adapter correctement à d’autres résolutions. Au-delà du simple affichage, l’application doit répondre à des exigences de qualité attendues dans un contexte professionnel et
d’évaluation : respect de la maquette, compatibilité avec les navigateurs récents, accessibilité, sémantique HTML, conformité aux bonnes pratiques du web et comportement fonctionnel
cohérent.



## Objectif

L’objectif du projet est de concevoir une borne de commande numérique permettant aux clients des restaurants Wacdo de consulter l’offre disponible, de sélectionner des produits ou des
menus, de personnaliser leur commande selon les choix proposés, de constituer un panier, puis de valider cette commande directement depuis l’interface.

Voici les détails fonctionnel de l’objectif de la borne : 

- permettre au client de consulter l’offre disponible ;
- permettre la sélection de produits simples ou de menus ;
- permettre la configuration des choix liés aux menus ;
- permettre la constitution, la consultation et la modification d’un panier ;
- permettre la validation finale d’une commande via un numéro de retrait ;
- transmettre la commande validée sous forme de JSON à une API fictive.

Objectifs techniques associés

- charger dynamiquement les données du catalogue depuis des fichiers JSON ;
- garantir une interface responsive et adaptée à différentes résolutions ;
- respecter les exigences d’accessibilité, de sémantique HTML et de compatibilité navigateurs récents ;
- produire une interface cohérente avec la maquette fournie.



## Périmètre

Inclus dans le périmètre

- affichage de l’écran d’accueil de la borne ;
- choix du mode de commande ;
- chargement dynamique des catégories et produits depuis les fichiers JSON ;
- affichage des catégories et des produits ;
- sélection de produits simples et de menus ;
- configuration des menus selon les règles métier ;
- ajout au panier ;
- consultation et suppression des lignes du panier ;
- affichage du total ;
- saisie du numéro de retrait ;
- transformation de la commande en JSON ;
- envoi de la commande à une API fictive ;
- affichage d’un écran final de confirmation.

Exclus du périmètre

- développement de l’API serveur ;
- gestion du paiement réel ;
- gestion d’un compte client ;
- authentification ;
- gestion des stocks ;
- historique des commandes ;
- administration du catalogue ;
- persistance serveur ou base de données ;
- traitement logistique après réception de la commande.


## Acteurs

**Acteur principal : Client**
Le client est l’utilisateur principal de la borne. Il consulte l’offre, navigue dans les catégories, sélectionne des produits ou des menus, configure ses choix, gère son panier et valide
sa commande en renseignant un numéro de retrait.

**Système externe : fichiers JSON de données**
Les fichiers JSON fournis constituent une source de données externe au front-end. Ils alimentent dynamiquement l’application en catégories, produits et informations nécessaires à
l’affichage de l’offre.

**Système externe : API fictive de traitement des commandes**
L'application envoie, après validation finale, un fichier JSON contenant le détail de la commande à cette API fictive. 


## Glossaire

Produit = élément commercial sélectionnable par le client, identifié, nommé, tarifé, rattaché à une catégorie, affiché avec une image, pouvant être commandé seul ou entrer dans la composition d’un menu. Un produit peut proposer des variantes ou options selon les règles métier

Menu = offre commerciale composée de plusieurs éléments sélectionnés selon des règles de composition. Dans ce projet, un menu comprend un burger, un accompagnement, une boisson et une sauce optionnelle. Le menu peut ensuite exister en variantes, par exemple Best Of ou Maxi Best Of

Catégorie = regroupement logique de produits utilisé pour organiser l’affichage et la navigation dans l’interface. Une catégorie possède un identifiant, un nom et une image, et regroupe un ou plusieurs éléments. Ce n’est pas un objet physique, mais un concept de classement.

Panier = ensemble temporaire des sélections du client avant validation finale. Il contient une ou plusieurs lignes de panier correspondant à des produits simples ou à des menus
configurés, avec leurs quantités, options et prix calculés. Le panier ne contient pas de catégories.

Commande = ensemble final des articles sélectionnés et validés par le client à partir du panier, identifié par un numéro de retrait, et transmis à l’API fictive pour préparation. Elle
représente la commande physique à produire.

Ligne de panier = élément du panier correspondant à un produit simple ou à un menu configuré, avec sa quantité, ses choix éventuels, son prix calculé, et pouvant être supprimé ou modifié avant validation.

Variante = déclinaison d’un produit ou d’un menu modifiant certaines caractéristiques commerciales, comme la taille ou le type d’offre.

Option = choix proposé au client lors de la sélection ou de la configuration d’un produit ou d’un menu. Une option peut être obligatoire ou facultative et peut influencer la composition ou le prix

Numéro de retrait = numéro de trois chiffres exactement, renseigné par le client au moment de la validation finale afin d’identifier la commande lors de sa récupération.


## Règles de gestion

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

RG-MENU-006 — Un menu incomplet ne peut pas être ajouté au panier.

Un menu incomplet ne peut pas être ajouté au panier tant que tous les choix obligatoires n’ont pas été renseignés.

RG-MENU-007 — Les choix du menu sont faits par le client

Les choix configurables du menu sont déterminés par les sélections effectuées par le client parmi les options disponibles.

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

RG-PAN-009 — Un panier vide ne peut pas être validé

La validation finale est impossible tant que le panier ne contient aucune ligne.

RG-PAN-010 — Seules des sélections complètes peuvent être ajoutées au panier

Un produit ou un menu ne peut être ajouté au panier que si les informations obligatoires de sa sélection ont été renseignées.

RG-PAN-011 — Un panier validé ne peut plus être modifié.

Une fois validé, aucun élément ne peut être ajouté ou supprimé du panier.

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

RG-VAR-004 — Une variante ne peut être sélectionnée que si elle existe pour le produit concerné

Le client ne peut sélectionner qu’une variante effectivement définie pour le produit choisi.

RG-VAR-005 — Le prix calculé d’une ligne doit tenir compte de la variante choisie

Si une variante impacte le prix, cet impact doit être intégré dans le prix final de la ligne de panier.

RG-VAR-006 — Une variante ne remplace pas le produit

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


## Parcours utilisateur

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



## Cas alternatifs

CA-001 — Menu incomplet
 
Si le client sélectionne un menu sans renseigner tous les choix obligatoires, le menu ne peut pas être ajouté au panier.
 
 CA-002 — Produit avec choix obligatoire incomplet
 
 Si le client sélectionne un produit simple nécessitant un choix obligatoire sans finaliser ce choix, le produit ne peut pas être ajouté au panier.
 
 CA-003 — Panier vide
 
 Si le client tente de lancer la validation finale alors que le panier est vide, la validation est refusée.
 
 CA-004 — Suppression de la dernière ligne du panier
 
 Si le client supprime la dernière ligne du panier, celui-ci redevient vide et la validation finale devient impossible.
 
 CA-005 — Abandon de la configuration en cours
 
 Si le client revient en arrière ou quitte l’écran de configuration d’un produit ou d’un menu avant validation, la sélection en cours est abandonnée et aucun élément incomplet n’est ajouté au panier.
 
 CA-006 — Numéro de retrait absent
 
 Après validation du panier, le client est dirigé vers la page de saisie du numéro de retrait. Tant que ce numéro n'est pas renseigné, la commande ne peut pas être envoyée à l'API fictive.
 
 - **Cas d’exception / erreurs**
 
 CE-001 — Données catalogue indisponibles
 
 Si les fichiers JSON nécessaires au chargement des catégories ou des produits ne peuvent pas être récupérés, l’application ne peut pas afficher correctement l’offre et doit empêcher la 
poursuite normale du parcours.
 
 CE-002 — Données incohérentes ou incomplètes
 
 Si un produit, une catégorie ou une ressource attendue est incomplet(e) dans les données chargées, l’interface ne doit pas permettre une sélection incohérente.
 
 CE-003 — Commande non transmise
 
 Si l’envoi du JSON final à l’API fictive échoue, la commande ne peut pas être considérée comme transmise.



## Exigences fonctionnelles

EF-001 — Accueil de la borne

L’application doit afficher un écran d’accueil permettant au client de démarrer son parcours de commande.

EF-002 — Choix du mode de commande

L’application doit permettre au client de choisir entre sur place et à emporter, lorsque ce choix est prévu par la maquette.

EF-003 — Consultation des catégories

L’application doit permettre au client de consulter les catégories de produits disponibles.

EF-004 — Consultation des produits et menus

L’application doit permettre au client d’afficher les produits et menus associés à une catégorie.

EF-005 — Sélection d’un produit simple

L’application doit permettre au client de sélectionner un produit simple et, si nécessaire, de renseigner ses options ou variantes avant ajout au panier.

EF-006 — Sélection d’un menu

L’application doit permettre au client de sélectionner un menu et de renseigner les choix nécessaires à sa configuration.

EF-007 — Ajout au panier

L’application doit permettre d’ajouter au panier un produit simple ou un menu correctement configuré.

EF-008 — Consultation du panier

L’application doit permettre au client de consulter le contenu de son panier, le détail des lignes et le montant total.

EF-009 — Suppression d’une ligne du panier

L’application doit permettre au client de supprimer une ligne de panier avant validation finale.

EF-010 — Calcul du total

L’application doit calculer et afficher le montant total du panier à partir des lignes sélectionnées.

EF-011 — Validation de la commande

L’application doit permettre au client de lancer la validation finale de sa commande.

EF-012 — Saisie du numéro de retrait

L’application doit permettre au client de renseigner un numéro de retrait composé exactement de trois chiffres avant validation finale.

EF-013 — Génération des données de commande

L’application doit générer un objet JSON représentant fidèlement la commande validée.

EF-014 — Envoi de la commande

L’application doit transmettre la commande validée à une API fictive.

EF-015 — Confirmation de prise en compte

L’application doit afficher un écran final confirmant la prise en compte de la commande.
EF-016 — Sélection d'une variante de produit

Lorsqu'un produit propose des variantes, l'application doit permettre au client de sélectionner la variante souhaitée avant l'ajout au panier. La variante choisie doit être mémorisée dans la ligne de panier et son impact tarifaire doit être répercuté sur le prix calculé de la ligne.

## Contraintes fonctionnelles

CF-001 — Menu incomplet interdit

Un menu ne peut pas être ajouté au panier tant que tous les choix obligatoires n’ont pas été renseignés.

CF-002 — Produit simple incomplet interdit

Un produit simple nécessitant une option ou un choix obligatoire ne peut pas être ajouté au panier tant que cette sélection n’est pas complète.

CF-003 — Panier vide non validable

Un panier vide ne peut pas être validé.

CF-004 — Numéro de retrait obligatoire

La commande ne peut pas être validée tant qu’un numéro de retrait composé exactement de trois chiffres n’a pas été renseigné.

CF-005 — Catégorie non commandable

Une catégorie sert uniquement à organiser l’affichage ; elle ne peut pas être ajoutée au panier.

CF-006 — Cohérence des lignes du panier

Chaque ligne du panier doit refléter exactement la sélection effectuée par le client, avec ses choix, variantes et prix calculé.

CF-007 — Total cohérent

Le montant total du panier et de la commande doit correspondre à la somme des lignes validées.

CF-008 — Panier validé non modifiable

Une fois la validation finale effectuée, le contenu du panier ne peut plus être modifié dans le parcours normal.

CF-009 — Taille liée au type de menu

Lorsque la règle métier s’applique, le type de menu sélectionné détermine la taille de la boisson et de l’accompagnement.

CF-010 — Commande fidèle au panier validé

Les données transmises à l’API fictive doivent correspondre exactement au contenu final du panier validé.


## Contraintes non fonctionnelles**

CNF-001 — Responsive

L’interface doit s’adapter à différentes résolutions d’écran.

CNF-002 — Résolution de référence

L’interface doit être conçue en prenant comme référence un affichage en 1920x1080.

CNF-003 — Accessibilité

L’intégration doit respecter les exigences d’accessibilité applicables au projet.

CNF-004 — Conformité web

L’application doit respecter les normes W3C et les bonnes pratiques du web.

CNF-005 — Compatibilité navigateurs

Le code doit être compatible avec les navigateurs récents.

CNF-006 — Sémantique HTML

L’interface doit utiliser une structure HTML sémantique.

CNF-007 — Chargement dynamique des données

Les catégories et produits doivent être chargés dynamiquement à partir des fichiers JSON fournis.

CNF-008 — Technologies imposées

Le projet doit être réalisé en HTML, CSS et JavaScript.

CNF-009 — Référencement naturel
 
L’application doit appliquer les bonnes pratiques relatives à la sémantique HTML et au référencement naturel dans la mesure permise par le projet front-end.
 
CNF-010 — Tests et validation
 
Avant le déploiement, une série de tests doit être réalisée afin de vérifier que l’application répond aux spécifications fonctionnelles et de qualité attendues.


## Données manipulées**

- Données d’entrée 

1. Catégories

Les catégories permettent d’organiser et filtrer l’affichage des produits dans l’interface.
Chaque catégorie est identifiée et possède au minimum :

 - un identifiant ;
 - un nom ;
 - une image.

2. Produits

Les produits représentent les éléments commercialisés par la borne.
Ils peuvent être commandés seuls ou utilisés dans la composition d’un menu.
Chaque produit possède au minimum :

 - un identifiant ;
 - un nom ;
 - un prix ;
 - une catégorie ;
 - une image.

3. Menus

Les menus sont des offres composées intégrant plusieurs éléments sélectionnés selon des règles métier.
Ils regroupent un produit principal déjà déterminé par le type de menu choisi, auquel s’ajoutent des choix complémentaires comme l’accompagnement, la boisson et une sauce optionnelle.

4. Variantes

Certaines sélections peuvent proposer des variantes modifiant certaines caractéristiques commerciales, notamment le prix ou la forme de l’offre.
Les variantes sont utilisées lorsque le produit concerné le permet.

5. Options

Les options correspondent aux choix proposés au client lors de la configuration d’un produit ou d’un menu.
Elles peuvent être obligatoires ou facultatives et influencent la composition finale de la sélection.

- Données de gestion de commande


6. Panier

Le panier représente l’ensemble temporaire des sélections effectuées par le client avant validation finale.
Il contient les lignes de panier, le détail des choix effectués et le montant total calculé.

7. Lignes de panier

Chaque ligne de panier correspond à une sélection concrète du client :

 - produit simple ;
 - ou menu configuré.
Une ligne conserve les informations nécessaires à l’affichage, au calcul du prix, ainsi qu’aux choix réalisés par le client.

8. Commande

La commande représente l’état final validé du panier.
Elle regroupe l’ensemble des lignes retenues, le montant total et les informations nécessaires à sa transmission.

9. Numéro de retrait

Le numéro de retrait est une donnée saisie par le client au moment de la validation finale.
Il contient exactement trois chiffres et permet d’associer la commande à une identification côté comptoir.

10. Type de commande

Lorsque la maquette le prévoit, l’application manipule également le type de commande choisi par le client :

 - sur place ;
 - à emporter.


- Données de sortie


11. Données JSON de sortie

Après validation, l’application génère un objet JSON représentant fidèlement la commande finale à transmettre à l’API fictive.


- **Livrables attendus**

Le projet doit aboutir à la fourniture d’une application front-end complète, fonctionnelle et exploitable sur le serveur mis à disposition. Les livrables attendus couvrent l’interface
utilisateur, les fichiers nécessaires à son exécution et la capacité de l’application à exploiter les données fournies.

Livrable principal

 - une application front-end de borne de commande Wacdo, complète et fonctionnelle.

Éléments attendus dans ce livrable

 - l’intégration de la maquette fournie ;
 - les fichiers HTML, CSS et JavaScript nécessaires au fonctionnement de l’application ;
 - l’exploitation dynamique des fichiers JSON fournis ;
 - l’affichage correct des catégories, produits et menus ;
 - la gestion du panier ;
 - la validation finale via un numéro de retrait ;
 - la génération du JSON de commande ;
 - l’envoi de la commande à l’API fictive ;
 - une interface responsive et compatible avec les contraintes du projet.
 
 - l’ensemble des fichiers du projet déployables sur le serveur ;
 - les ressources graphiques nécessaires au fonctionnement de l’interface ;
 - les fichiers JSON placés et exploitables depuis le serveur ;
 - le code source front-end présentable et exploitable.



