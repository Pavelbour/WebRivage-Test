# Test technique "Développeur WEB"

Le test consiste à développer un petit projet et le mettre à disposition sur une plate-forme d'hébergement Git (Github, Bitbucket, Gitlab, ...) pour que le recruteur puisse l'analyser.

# Le projet

Le projet se place dans le cadre de la gestion de prix d'un catalogue de produit. On veut pouvoir définir des règles de réduction du prix pour les périodes de solde qui prenne en compte la catégorie et le prix initial du produit.

ex: appliquer, en période de solde, 30% de réduction sur les produits de la catégorie Electro-ménager qui ont un prix supérieur à 100 Euros.

Pour arriver à ce but, il va falloir développer les fonctionnalités suivantes.

## Listing des règles de réduction

Une page web doit permettre de lister toutes les règles de réduction actuellement définies.

## Formulaire de création d'une règle

L'application doit fournir une formulaire de création d'une règle.
Les données devront être validées :
* pourcentage de réduction compris entre 1 et 50%
* expression de la règle valide

> Pas de formulaire d'édition d'une règle.

## Commande de calcul des pourcentages de réduction sur chaque produit

Chaque matin, une tâche planifiée (CRON) devra être lancé pour remettre à jour les prix réduits de la totalité du catalogue de produits. Cette commande devra envoyer un email récapitulant tous les tarifs modifiés.

# Les technologies/méthodologies à utiliser

## Imposées

Le test impose d'utiliser le framework Symfony dans un de ces dernières versions (4.2/4.3).
Le test impose d'utiliser le composant Symfony [ExpressionLanguage](https://symfony.com/doc/current/components/expression_language.html).
Le test impose de **ne pas utiliser** la fonction mail() pour envoyer l'email de récapitulatif de la commande.

## Bonus / conseil

Ne pas hésitez à faire plusieurs commits.
Liste de mots clés correspondant à des technologies/méthodologies/autre qui sont utilisés chez WebRivage : Flex, CQRS, Symfony Messenger, DIC, fixtures, tests, ...

# Annexes

## Schema de tables de la base de données

Des colonnes peuvent être ajouté pour le besoin du développement.

discount_rules|
-------------|
id|
rule_expression|
discount_percent|


products|
-------------|
id|
name|
price|
discounted_price|
type (Electro-ménager, HiFi, ...)|

## Exemples de produits / règles

### Produits
id|name|price|discounted_price|type|
-------------|-------------|-------------:|-------------:|-------------|
1|Cafetière|15.50|NULL|Electro-ménager|
2|Enceinte Bluetooth|100.00|NULL|HiFi|

### Règles

id|rule_expression|discount_percent|
-------------|-------------|-------------|
1|product.type = 'sometype' and product.price >= 100 |20|
2|product.type = 'sometype' and product.price < 100 |10|
3|product.type = 'sometype'|10|


