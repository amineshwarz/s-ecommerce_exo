
# Project Title
Bienvenue sur le dépôt de [eshop.com] !

## Desciption 
est une plateforme e-commerce permettant aux utilisateurs d’acheters produits en ligne facilement, rapidement et en toute sécurité.

## Fonctionnalités principales

### Catalogue produits
- Listing de produits avec filtres dynamiques (prix, couleur, marque, taille, etc.)
- Catégorisation hiérarchique (+ sous-catégories)
- Fiches produits individuelles : prix, variantes, disponibilité, images galeries.
- Produits personnalisables (exemple : shaussettes different taille et model).
- Gestion des stocks en temps réel et affichage du stock restant

### Recherche & navigation
- Barre de recherche avancée avec suggestions
- Mega menu / navigation rapide par catégories et sous-catégories

### Panier & achat
- Ajout rapide au panier depuis la fiche ou le listing produit
- Modification des quantités, suppression, retour fiche produit
- Calcul automatique des frais de livraison
- One page cart : tunnel d’achat rapide et simplifié

### Tunnel de commande (Checkout)
- Validation du panier
- Formulaires simplifiés pour infos client/livraison/facturation
- Sélection mode de livraison (en ligne, ou à la livraison)
- Paiement sécurisé : CB, Stripe.
- Génération automatique de facture électronique téléchargeable
- Notifications automatiques à chaque étape (confirmation, expédition, livraison, relance panier abandonné)

### Gestion & admin back office
- Tableaux de bord pour suivi commandes, clients, produits, stocks, ville de livraison
- Intégration marketplace, diffusion catalogue



## Utilisation

Accédez à Eshop, inscrivez-vous, explorez le catalogue, utilisez les filtres, ajoutez des produits au panier et suivez le tunnel de commande complet. Administrateurs : gérez produits, stocks, commandes et promotions directement via le back-office.

1. fichier (.env)(.env.local)
```bash
# .env.local

# Make sure APP_ENV is set to 'dev' for development
APP_ENV=dev

# Your local database connection string
DATABASE_URL="mysql://root:root@127.0.0.1:8889/S-ecommerce_exo"

# Your Stripe API keys
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_ENDPOINT_SECRET=whsec_...
```
2. Set up the Database:

#### Create the database
php bin/console doctrine:database:create

#### Run the database migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate
## Installation

### Prérequis
- Node.js => 16.x / PHP => 7.4 
- Base de données MySQL
- Environnement sécurisé (HTTPS obligatoire pour production)
- Bootstrap

### Procédure
1. Clone le project:

```bash
git clone <your-repository-url>
cd S-ecommerce_exo
```
2. install les dependences 
```bash
cd e-commerce_exo
composer install
```
3. Configurer les variables d’environnement dans `.env`,`.env.local`
4. Lancer l’application :
```bash
symfony server:start
symfony serve -d
php -S localhost:8000 -t public
```
Your application should now be running at 
HTTPS://127.0.0.1:80000. or 
HTTPS://localhost:8000

5. Stripe Webhook Setup (Local Development)
Pour tester le webhook de confirmation de paiement, vous devez transférer les événements Stripe vers votre serveur local.

#### Log in to the Stripe CLI:
```bash
stripe login
```
#### Start forwarding events to your webhook controller. Use http://localhost:8000
```bash
stripe listen --forward-to http://localhost:8000/stripe/notify
```
6. The CLI will output a webhook signing secret (whsec_...). Copy this and place it in your .env.local file for the STRIPE_WEBHOOK_SECRET variable.
## License

ce projet est pas licencier