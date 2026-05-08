Sommaire complété

1. Glossaire
2. Règles de gestion
3. Parcours utilisateurs (multi-acteurs)
4. CDC fonctionnel  ← rôles, permissions, API fonctionnelle, exigences sécu
5. Modèle de données (MCD complet)
6. MPD
7. MCT
8. CDC technique  ← implémentation sécu, MVC, classes, archi API
9. Architecture fichiers / MVC
10. Tests conceptuels


## 1 GLOSSAIRE

**Utilisateur** = compte interne de l'application back-office, identifié par un identifiant et un mot de passe, associé à un rôle déterminant ses droits d'accès. Un utilisateur interne n'est pas un client de la borne.

**Rôle** = niveau d'autorisation attribué à un utilisateur interne, définissant les actions qu'il est autorisé à effectuer dans le back-office. Trois rôles existent : Administration, Accueil et Préparation.

**Administration** = rôle permettant la gestion complète des données de l'application (produits, menus, catégories) ainsi que la gestion des comptes utilisateurs internes.

**Accueil** = rôle permettant la saisie manuelle d'une commande (au comptoir ou par téléphone) et la remise d'une commande préparée à un client.

**Préparation** = rôle permettant la consultation de la liste des commandes à préparer et la déclaration d'une commande comme préparée.

**Produit** = élément du catalogue géré en back-office, identifié, nommé, décrit, tarifé, rattaché à une catégorie, associé à une image et doté d'un indicateur de disponibilité.

**Menu** = offre commerciale composée de plusieurs produits selon une structure prédéfinie, géré en back-office avec ses options disponibles.

**Catégorie** = regroupement logique de produits, géré en back-office, servant à organiser l'affichage du catalogue sur la borne cliente.

**Disponibilité** = état d'un produit indiquant s'il peut être proposé ou non à la commande. Un produit indisponible ne doit pas être présenté au client ou commandable.

**Commande** = ensemble de lignes de commande validé, associé à un numéro de retrait, un type (sur place ou à emporter) et un statut évolutif. Une commande peut être saisie par la borne cliente via l'API ou manuellement par un équipier Accueil.

**Ligne de commande** = élément constitutif d'une commande, correspondant à un produit simple ou un menu configuré, avec sa quantité et son prix.

**Statut de commande** = état courant d'une commande dans son cycle de vie opérationnel : `à préparer`, `préparée`, `livrée`.

**Numéro de retrait** = identifiant saisi par le client ou l'équipier Accueil, permettant d'associer une commande à un client au moment de la remise.

**Application** = logiciel web composé d'un back-office (accessible aux utilisateurs internes) et d'une API (exposée à la borne cliente). C'est le système décrit dans ce document : il reçoit les requêtes, applique les règles de gestion, interagit avec la base de données et retourne les réponses.

**Authentification** = mécanisme de vérification de l'identité d'un utilisateur interne avant l'accès au back-office, reposant sur des sessions sécurisées côté serveur.

**Session** = données temporaires conservées côté serveur permettant de maintenir l'état authentifié d'un utilisateur entre ses requêtes, sans retransmettre les identifiants à chaque appel.



## 2 Règles de gestion

Les règles de gestion décrivent les contraintes métier que le back-office Wacdo doit respecter. Elles servent de base au MCD, au MCT, au CDC fonctionnel et aux tests conceptuels.

### UTILISATEURS ET RÔLES

RG-USER-001 — Un utilisateur est un compte interne

Un utilisateur correspond à un compte interne du back-office. Il ne représente pas un client de la borne de commande.

RG-USER-002 — Un utilisateur doit être identifié de manière unique

Chaque utilisateur doit posséder un identifiant unique permettant de le distinguer des autres comptes internes.

RG-USER-003 — Un utilisateur doit avoir un rôle

Chaque utilisateur interne doit être rattaché à un et un seul rôle.

RG-USER-004 — Les rôles disponibles sont limités

Le back-office distingue trois rôles : Administration, Préparation et Accueil.

RG-USER-005 — Seul un administrateur peut gérer les utilisateurs

La création, la modification ou la suppression d'un compte utilisateur est réservée aux utilisateurs ayant le rôle Administration.

RG-USER-006 — Un utilisateur doit être authentifié pour accéder au back-office

Aucune fonctionnalité du back-office ne doit être accessible à un utilisateur interne non authentifié.

RG-USER-007 — Les droits dépendent du rôle de l'utilisateur connecté

Les actions accessibles dans le back-office doivent être déterminées par le rôle de l'utilisateur connecté.

RG-USER-008 — Un utilisateur ne peut pas réaliser une action hors de son rôle

Une action non autorisée pour le rôle courant doit être refusée par l'application.

### RÔLE ADMINISTRATION

RG-ADM-001 — L'administration gère les données du catalogue

Un utilisateur ayant le rôle Administration peut créer, modifier ou supprimer les données liées aux produits, aux catégories, aux menus et à leur composition.

RG-ADM-002 — L'administration gère la disponibilité des produits

Un utilisateur ayant le rôle Administration peut modifier l'état de disponibilité d'un produit.

RG-ADM-003 — L'administration gère les comptes internes

Un utilisateur ayant le rôle Administration peut créer, modifier ou supprimer les comptes internes du back-office.

### RÔLE PRÉPARATION

RG-PREP-001 — La préparation consulte les commandes à préparer

Un utilisateur ayant le rôle Préparation peut consulter la liste des commandes qui doivent être préparées.

RG-PREP-002 — Les commandes à préparer sont triées par heure prévue croissante

La liste des commandes à préparer doit afficher en priorité les commandes dont l'heure de livraison ou de retrait est la plus proche.

RG-PREP-003 — La préparation peut déclarer une commande préparée

Un utilisateur ayant le rôle Préparation peut changer le statut d'une commande lorsque sa préparation est terminée.

RG-PREP-004 — La préparation ne remet pas une commande au client

La remise d'une commande au client relève du rôle Accueil, pas du rôle Préparation.

RG-PREP-005 — La préparation ne gère pas le catalogue

Un utilisateur ayant le rôle Préparation ne peut pas créer, modifier ou supprimer les produits, les menus, les catégories ou les utilisateurs.

### RÔLE ACCUEIL

RG-ACC-001 — L'accueil peut saisir une commande

Un utilisateur ayant le rôle Accueil peut saisir une commande pour un client au comptoir ou par téléphone.

RG-ACC-002 — L'accueil peut remettre une commande préparée

Un utilisateur ayant le rôle Accueil peut déclarer livrée une commande qui a déjà été déclarée préparée.

RG-ACC-003 — L'accueil ne prépare pas une commande

Un utilisateur ayant le rôle Accueil ne peut pas déclarer une commande comme préparée.

RG-ACC-004 — L'accueil ne gère pas les données d'administration

Un utilisateur ayant le rôle Accueil ne peut pas créer, modifier ou supprimer les produits, les menus, les catégories ou les utilisateurs.

### CATALOGUE ET PRODUITS

RG-CAT-001 — Une catégorie est identifiée de manière unique

Chaque catégorie doit posséder un identifiant unique.

RG-CAT-002 — Une catégorie possède un nom

Chaque catégorie doit posséder un nom exploitable dans le back-office et dans l'affichage du catalogue côté borne.

RG-CAT-003 — Une catégorie peut regrouper plusieurs produits

Une catégorie permet d'organiser les produits du catalogue et peut être associée à plusieurs produits.

RG-CAT-004 — Les suppressions du catalogue sont contrôlées

Une catégorie, un produit ou un menu déjà utilisé dans une commande ne doit pas être supprimé physiquement si cette suppression empêche la lecture historique de la commande. Il doit être désactivé, rendu indisponible ou archivé.

RG-PROD-001 — Un produit est identifié de manière unique

Chaque produit doit posséder un identifiant unique.

RG-PROD-002 — Un produit possède les informations minimales nécessaires

Chaque produit doit posséder au minimum un nom, une description, un prix, une image, une catégorie et un état de disponibilité.

RG-PROD-003 — Un produit appartient à une catégorie

Chaque produit doit être rattaché à une et une seule catégorie.

RG-PROD-004 — Le prix d'un produit doit être strictement positif

Un produit commandable ne peut pas avoir un prix nul ou négatif.

RG-PROD-005 — La disponibilité d'un produit détermine sa possibilité de commande

Un produit indisponible ne doit pas pouvoir être commandé par la borne ou par un utilisateur Accueil.

RG-PROD-006 — Le catalogue exposé au front doit refléter les données actives du back-office

Les produits et menus fournis à la borne via l'API doivent correspondre aux données enregistrées et disponibles dans le back-office.

RG-PROD-007 — La modification d'un produit ne modifie pas les commandes déjà enregistrées

Une commande doit conserver les informations nécessaires à sa lecture même si un produit est modifié après la validation de cette commande.

RG-PROD-008 — Un produit indisponible n'est plus proposé comme choix de menu

Un produit indisponible ne doit pas être proposé seul ni comme option disponible dans la composition d'un menu.

### MENUS ET COMPOSITION

RG-MENU-001 — Un menu est une offre composée

Un menu est une offre commerciale composée de plusieurs produits selon une composition définie dans le back-office.

RG-MENU-002 — Un menu doit être identifié de manière unique

Chaque menu doit posséder un identifiant unique.

RG-MENU-003 — Un menu possède les informations minimales d'affichage

Chaque menu doit posséder au minimum un nom, une description, un prix, une image et un état de disponibilité.

RG-MENU-004 — Un menu doit posséder une composition

Un menu commandable doit préciser les produits ou types de produits qui le composent.

RG-MENU-005 — La composition d'un menu utilise uniquement des produits existants

Un menu ne peut pas référencer un produit absent du catalogue.

RG-MENU-006 — Les options disponibles d'un menu sont définies dans le back-office

Les choix proposés au client pour un menu doivent provenir des options enregistrées dans le back-office.

RG-MENU-007 — Un menu incomplet ne peut pas être commandé

Un menu dont la composition obligatoire n'est pas définie ne doit pas être exposé comme commandable.

RG-MENU-008 — Un menu indisponible ne peut pas être commandé

Un menu déclaré indisponible dans le back-office ne doit pas pouvoir être commandé.

RG-MENU-009 — Une option de menu doit être compatible avec le menu concerné

Une option ne peut être proposée que si elle est rattachée au menu ou à la règle de composition qui la prévoit.

RG-MENU-010 — La composition d'un menu est découpée en règles de composition

Un menu commandable doit être composé d'une ou plusieurs règles de composition définissant les parties attendues du menu, par exemple plat, boisson, accompagnement ou sauce.

RG-MENU-011 — Une règle de composition définit ses choix autorisés

Chaque règle de composition doit définir les produits ou options qui peuvent être choisis pour cette partie du menu.

RG-MENU-012 — Une règle de composition précise si le choix est obligatoire

Chaque règle de composition doit indiquer si le choix est obligatoire ou facultatif, ainsi que le nombre minimal et maximal de choix autorisés.

RG-MENU-013 — La disponibilité réelle d'un menu dépend de sa composition

Un menu ne peut être commandé que s'il est lui-même disponible et si chacune de ses règles obligatoires possède au moins un choix disponible.

### COMMANDES

RG-CMD-001 — Une commande peut provenir de deux sources

Une commande peut être créée par le front de la borne via l'API ou saisie manuellement dans le back-office par un utilisateur Accueil.

RG-CMD-002 — Une commande doit contenir au moins une ligne

Une commande vide ne peut pas être enregistrée.

RG-CMD-003 — Une commande doit être associée à un numéro de retrait

Chaque commande doit posséder un numéro d'identification permettant sa remise au client.

RG-CMD-004 — Une commande ne déclenche pas de paiement

Le back-office ne gère pas le paiement. Le suivi d'une commande repose sur son numéro de retrait et son statut.

RG-CMD-005 — Une commande possède un statut métier unique

Chaque commande possède un seul statut parmi `à préparer`, `préparée` et `livrée`.

RG-CMD-006 — Une commande nouvellement créée est à préparer

Lorsqu'une commande est enregistrée, elle reçoit le statut `à préparer` et entre dans la liste des commandes à préparer.

RG-CMD-007 — Seule la préparation peut déclarer une commande préparée

Un utilisateur Préparation peut faire passer une commande du statut `à préparer` au statut `préparée`.

RG-CMD-008 — Seul l'accueil peut déclarer une commande livrée

Un utilisateur Accueil peut faire passer une commande du statut `préparée` au statut `livrée` lors de la remise au client.

RG-CMD-009 — Une commande livrée termine son cycle normal

Une commande déclarée livrée ne doit plus apparaître dans la liste des commandes à préparer ou à remettre.

RG-CMD-010 — Les transitions de statut doivent respecter le cycle de commande

Toute transition sautée, inverse ou non autorisée doit être refusée.

RG-CMD-011 — Les commandes à préparer doivent être consultables par les préparateurs

Les utilisateurs Préparation doivent accéder aux informations nécessaires à la confection des commandes.

RG-CMD-012 — Les commandes préparées doivent être consultables par l'accueil

Les utilisateurs Accueil doivent accéder aux informations nécessaires à la remise des commandes aux clients.

RG-CMD-013 — Une commande possède une date et une heure de référence pour le tri

Chaque commande doit conserver sa date et son heure de création. Si une heure de retrait ou de livraison est renseignée, elle sert au tri des commandes à préparer ; sinon le tri se fait par date de création croissante.

RG-CMD-014 — Le numéro de retrait doit permettre une remise sans ambiguïté

Chaque commande possède un identifiant technique unique et un numéro de retrait non vide. Le numéro de retrait doit permettre à l'accueil d'identifier sans ambiguïté une commande non livrée, au minimum sur une même journée d'activité.

RG-CMD-015 — Une commande conserve son origine

Chaque commande doit conserver sa source : borne via l'API ou saisie back-office par l'Accueil. Lorsqu'elle est saisie par l'Accueil, l'utilisateur interne ayant créé la commande doit être conservé.

RG-CMD-016 — Les changements de statut sont tracés

Chaque passage à un nouveau statut doit conserver la date, le statut atteint et, lorsqu'il s'agit d'une action back-office, l'utilisateur interne responsable de l'action.

RG-CMD-017 — Le total d'une commande est figé à la validation

Le montant total d'une commande doit être calculé côté serveur et conservé au moment de l'enregistrement, même si les prix du catalogue changent ensuite.

### LIGNES DE COMMANDE

RG-LCMD-001 — Une ligne de commande appartient à une commande

Chaque ligne de commande doit être rattachée à une et une seule commande.

RG-LCMD-002 — Une ligne de commande concerne un élément commandé

Une ligne de commande correspond à un produit simple ou à un menu commandé.

RG-LCMD-003 — Une ligne de commande possède une quantité

Chaque ligne de commande doit avoir une quantité strictement positive.

RG-LCMD-004 — Une ligne de commande conserve le prix appliqué

Chaque ligne de commande doit conserver le prix appliqué au moment de la validation de la commande.

RG-LCMD-005 — Une ligne de commande conserve les choix utiles à la préparation

Lorsqu'une ligne correspond à un menu ou à une sélection avec options, les choix nécessaires à la préparation doivent être conservés.

RG-LCMD-006 — Une ligne de commande conserve les informations affichables de l'article

Chaque ligne de commande doit conserver le type d'article commandé, son nom, son prix unitaire appliqué et son total de ligne au moment de la validation.

RG-LCMD-007 — Une ligne de menu conserve les choix sélectionnés

Lorsqu'une ligne correspond à un menu, les produits ou options sélectionnés pour chaque partie du menu doivent être conservés avec leur nom, leur rôle dans le menu et leur éventuel supplément de prix.

RG-LCMD-008 — Une ligne de commande conserve la quantité validée

La quantité enregistrée sur une ligne de commande doit rester celle validée au moment de la création de la commande.

### API ET COMMUNICATION AVEC LE FRONT

RG-API-001 — L'API fournit le catalogue commandable nécessaire à la borne

L'API destinée à la borne expose les catégories, produits et menus commandables avec les champs nécessaires à l'affichage : identifiant, nom, description, prix affiché, image, catégorie et disponibilité utile au front.

RG-API-002 — L'API fournit la configuration des menus

Pour chaque menu commandable, l'API expose les choix nécessaires à sa composition : groupes d'options, caractère obligatoire ou facultatif, nombre minimal et maximal de sélections, options autorisées, produits associés et éventuel supplément de prix.

RG-API-003 — L'API reçoit le détail d'une commande

Le back-end doit permettre au front de transmettre une commande complète.

RG-API-004 — L'API contrôle et valorise la commande côté serveur

À la réception d'une commande, le back-end vérifie l'existence des produits et menus, leur disponibilité, la validité des quantités, la complétude des menus, la compatibilité des options et la présence du numéro de retrait. Les prix transmis par le front ne font pas foi : le back-end recalcule les prix depuis le catalogue actif et conserve dans les lignes les libellés, choix et prix appliqués au moment de la validation.

RG-API-005 — L'API ne doit pas accepter une commande incohérente

Une commande sans numéro de retrait, sans ligne, avec quantité invalide, avec produit indisponible ou avec menu incomplet doit être refusée.

RG-API-006 — Les données exposées par l'API doivent être limitées au besoin du front

L'API destinée à la borne ne doit pas exposer les informations internes inutiles, notamment les données d'authentification des utilisateurs.

RG-API-007 — Les endpoints borne et back-office sont distingués

Les endpoints destinés à la borne permettent uniquement de consulter le catalogue commandable et de créer une commande. Les actions d'administration, de préparation, d'accueil et de changement de statut nécessitent une session authentifiée et les droits du rôle correspondant.

### SÉCURITÉ

RG-SEC-001 — Les accès au back-office sont protégés

Les pages et actions du back-office doivent nécessiter une authentification.

RG-SEC-002 — Les mots de passe ne doivent pas être stockés en clair

Les mots de passe des utilisateurs internes doivent être conservés avec un hachage adapté aux mots de passe, jamais en clair.

RG-SEC-003 — Les sessions doivent être sécurisées

La session d'un utilisateur authentifié doit permettre d'identifier l'utilisateur connecté sans exposer son mot de passe.

RG-SEC-004 — Les autorisations doivent être vérifiées côté serveur

Le contrôle des droits ne doit pas reposer uniquement sur l'affichage ou le masquage d'éléments dans l'interface.

RG-SEC-005 — Les données reçues doivent être validées

Les données provenant du front, des formulaires du back-office ou de l'API doivent être contrôlées avant traitement.

RG-SEC-006 — Les données sensibles des utilisateurs internes doivent être protégées

Les informations liées aux comptes internes ne doivent pas être exposées aux utilisateurs non autorisés.

RG-SEC-007 — Les sessions expirées ou fermées ne donnent plus accès au back-office

Une session expirée ou explicitement fermée lors d'une déconnexion doit empêcher tout nouvel accès aux fonctionnalités protégées.

RG-SEC-008 — Les tentatives de connexion répétées doivent être limitées

L'application doit prévoir une protection contre les tentatives répétées d'authentification échouées.

RG-SEC-009 — Les images envoyées dans le back-office doivent être contrôlées

Les images associées aux produits ou menus doivent être validées avant enregistrement afin d'éviter l'envoi de fichiers non autorisés.

## 3 Parcours utilisateurs

Les parcours utilisateurs décrivent l'utilisation du back-office par les trois rôles internes prévus dans le sujet : Administration, Préparation et Accueil.

Chaque parcours commence par une authentification, car le back-office ne doit pas être accessible à un utilisateur non connecté.

Les commandes peuvent entrer dans le back-office par deux sources : la borne via l'API ou la saisie manuelle par l'Accueil. Dans les deux cas, elles rejoignent le même cycle métier : `à préparer`, `préparée`, `livrée`.

### Principes communs aux parcours

PU-COM-001 — Authentification obligatoire

Un utilisateur interne doit être authentifié avant d'accéder aux fonctionnalités du back-office.

PU-COM-002 — Refus d'authentification invalide

Si les identifiants sont invalides, si le compte est désactivé ou si la session est expirée, l'accès au back-office est refusé.

PU-COM-003 — Contrôle des autorisations

Avant chaque action protégée, l'application vérifie côté serveur que le rôle de l'utilisateur connecté possède le droit nécessaire.

PU-COM-004 — Refus des actions non autorisées

Si le rôle connecté ne possède pas le droit requis, l'action est refusée et aucune donnée métier n'est modifiée.

PU-COM-005 — Traçabilité des actions sensibles

Les actions sensibles sont tracées : authentification réussie ou échouée, création, modification, désactivation, création de commande, changement de statut et refus d'action non autorisée.

### PU-ADM — Parcours Administration

**Acteur principal** : utilisateur ayant le rôle Administration.

**Objectif** : gérer les données du catalogue et les comptes utilisateurs internes.

**Précondition** : l'utilisateur possède un compte actif avec le rôle Administration.

PU-ADM-001 — Accès au back-office

L'administrateur ouvre l'application back-office et accède à l'écran de connexion.

PU-ADM-002 — Authentification

L'administrateur saisit ses identifiants. Si les identifiants sont valides, une session sécurisée est ouverte.

PU-ADM-003 — Accès au tableau de bord d'administration

Après connexion, l'administrateur accède aux fonctionnalités de gestion autorisées pour son rôle.

PU-ADM-004 — Gestion du catalogue

L'administrateur peut créer, consulter, modifier ou désactiver les catégories, produits, menus, compositions de menus et options disponibles.

PU-ADM-005 — Gestion de la disponibilité

L'administrateur peut rendre un produit ou un menu disponible ou indisponible selon les besoins opérationnels.

PU-ADM-006 — Gestion des utilisateurs internes

L'administrateur peut créer, consulter, modifier ou désactiver les comptes internes et leur rôle. Chaque compte interne possède un identifiant unique, un mot de passe sécurisé, un rôle et un état actif ou inactif.

PU-ADM-007 — Contrôle des données avant validation

Avant enregistrement, l'application vérifie que les données saisies sont complètes, cohérentes et autorisées.

PU-ADM-008 — Contrôle des images envoyées

Lors de l'ajout ou de la modification d'une image produit ou menu, l'application contrôle le type, la taille et le format du fichier avant enregistrement.

PU-ADM-009 — Enregistrement des modifications

Si les données sont valides, les modifications sont enregistrées en base de données, tracées avec l'utilisateur auteur, la date, l'action réalisée et l'objet concerné. Les produits, menus, compositions, options, prix, images et disponibilités deviennent exploitables par le back-office et exposables à la borne via l'API.

PU-ADM-010 — Conservation de l'historique

Si une catégorie, un produit ou un menu est déjà utilisé dans une commande, l'application empêche une suppression physique qui rendrait l'historique illisible. L'élément doit être désactivé ou archivé.

PU-ADM-011 — Déconnexion

L'administrateur peut se déconnecter. La session est fermée et l'accès aux pages protégées redevient impossible sans nouvelle authentification.

### PU-PREP — Parcours Préparation

**Acteur principal** : utilisateur ayant le rôle Préparation.

**Objectif** : consulter les commandes à préparer et déclarer une commande préparée.

**Précondition** : l'utilisateur possède un compte actif avec le rôle Préparation.

PU-PREP-001 — Accès au back-office

Le préparateur ouvre l'application back-office et accède à l'écran de connexion.

PU-PREP-002 — Authentification

Le préparateur saisit ses identifiants. Si les identifiants sont valides, une session sécurisée est ouverte.

PU-PREP-003 — Accès à la liste des commandes à préparer

Après connexion, le préparateur accède à la liste des commandes ayant le statut `à préparer`, qu'elles proviennent de la borne via l'API ou d'une saisie manuelle par un utilisateur Accueil.

PU-PREP-004 — Consultation des commandes triées

Les commandes à préparer sont affichées par heure de retrait ou de livraison prévue croissante. À défaut d'heure renseignée, elles sont triées par date de création croissante.

PU-PREP-005 — Consultation du détail d'une commande

Le préparateur ouvre une commande pour consulter les lignes, les produits, les menus, les options et les informations utiles à la confection.

PU-PREP-006 — Préparation physique de la commande

Le préparateur réalise la commande à partir des informations affichées dans le back-office.

PU-PREP-007 — Déclaration de la commande comme préparée

Lorsque la commande est terminée, le préparateur la déclare `préparée`. Seule une commande actuellement au statut `à préparer` peut être déclarée `préparée`.

PU-PREP-008 — Mise à jour du statut

L'application enregistre le passage du statut `à préparer` au statut `préparée`, trace l'action avec l'utilisateur auteur, la date, l'ancien statut et le nouveau statut, puis retire la commande de la liste des commandes à préparer.

PU-PREP-009 — Déconnexion

Le préparateur peut se déconnecter. La session est fermée et l'accès aux pages protégées redevient impossible sans nouvelle authentification.

### PU-ACC — Parcours Accueil

**Acteur principal** : utilisateur ayant le rôle Accueil.

**Objectif** : saisir une commande au comptoir ou par téléphone, puis remettre une commande préparée au client.

**Précondition** : l'utilisateur possède un compte actif avec le rôle Accueil.

PU-ACC-001 — Accès au back-office

L'équipier d'accueil ouvre l'application back-office et accède à l'écran de connexion.

PU-ACC-002 — Authentification

L'équipier d'accueil saisit ses identifiants. Si les identifiants sont valides, une session sécurisée est ouverte.

PU-ACC-003 — Accès aux fonctions d'accueil

Après connexion, l'équipier d'accueil accède aux fonctionnalités de saisie de commande et de remise client.

PU-ACC-004 — Saisie d'une commande

L'équipier d'accueil peut créer une commande pour un client au comptoir ou par téléphone en sélectionnant les produits, menus, quantités et options demandés. Cette saisie utilise les mêmes règles métier qu'une commande reçue depuis la borne via l'API.

PU-ACC-005 — Validation de la commande saisie

Avant enregistrement, l'application vérifie que la commande contient au moins une ligne, que les produits et menus sont disponibles, que les compositions de menus et options sont valides, que les quantités sont correctes et qu'un numéro de retrait est renseigné ou généré. Les prix affichés ou transmis ne font pas foi : le serveur recalcule le total depuis le catalogue actif.

PU-ACC-006 — Enregistrement de la commande

Si la commande est valide, elle est enregistrée avec le statut `à préparer`, la création est tracée et la commande devient visible par les utilisateurs Préparation.

PU-ACC-007 — Consultation des commandes préparées

L'équipier d'accueil peut consulter les commandes ayant le statut `préparée`, afin de les remettre aux clients.

PU-ACC-008 — Identification de la commande à remettre

L'équipier d'accueil retrouve la commande à partir du numéro de retrait communiqué par le client. Si le numéro est inexistant, ambigu pour la journée d'activité ou associé à une commande déjà livrée, la remise est refusée.

PU-ACC-009 — Remise de la commande au client

Lorsque la commande est remise au client, l'équipier d'accueil la déclare `livrée`. Seule une commande actuellement au statut `préparée` peut être déclarée `livrée`.

PU-ACC-010 — Fin du cycle de commande

L'application enregistre le passage du statut `préparée` au statut `livrée`, trace l'action avec l'utilisateur auteur, la date, l'ancien statut et le nouveau statut, puis retire la commande des commandes à remettre.

PU-ACC-011 — Déconnexion

L'équipier d'accueil peut se déconnecter. La session est fermée et l'accès aux pages protégées redevient impossible sans nouvelle authentification.







## 4 CDC Fonctionelles  

## 5 Modèle de données 

## 6 MPD

## 7 MCT

## 8 CDC technique

## 9 Architecture fichiers

## 10 Tests conceptuels