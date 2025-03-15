
# QuizPHP

Ce projet vous fournit un environnement de développement PHP complet utilisant Docker, avec :

- ✅ PHP + Apache
- ✅ MySQL avec initialisation automatique de la base de données
- ✅ phpMyAdmin pour la gestion de la base de données
- ✅ Mailpit pour capturer les e-mails envoyés
- ✅ Composer (gestion des dépendances PHP)
- ✅ Xdebug (pour le débogage)

## 📁 Structure du projet
 - www/
	 - ├── admin/ # Interface d’administration 
	- ├── api/ # Routes API 
	- ├── assets/ # Fichiers JS/CSS/images 
	- ├── profil/ # Fonctions liées aux profils 
	- ├── quiz/ # Système de quiz 
	- ├── util/ # Fonctions et classes utilitaires 
	- ├── vendor/ # Dépendances Composer (auto-généré) 
	- ├── .env.example # Exemple de fichier .env 
	- ├── index.php # Point d’entrée de l’application 
	- ├── composer.json # Fichier des dépendances PHP 
- docker-compose.yml # Configuration Docker
- Dockerfile # Configuration PHP/Apache
- init-db.sql # Script SQL pour initier la base de données
- Makefile # Fichier avec des commandes utiles

# ⚙️ Utilisation sans Docker

Si vous ne souhaitez pas utiliser Docker, vous pouvez tout de même exécuter ce projet en local en suivant ces étapes :

1.  **Cloner le projet** :
    
    ```bash
    git clone https://github.com/alexistb2904/QuizPHPRedo.git
    cd votre-projet
    ```
    
2.  **Préparer l’environnement PHP** :
    
    -   PHP ≥ 8.0 avec les extensions : `pdo`, `pdo_mysql`, `mbstring`, `openssl`.
        
    -   Serveur Apache ou Nginx configuré pour pointer vers le dossier `www/`.
        
3.  **Installer Composer** (si ce n’est pas déjà fait) :
    
    -   Télécharger [Composer](https://getcomposer.org/)
        
    -   Installer les dépendances PHP :
        
         ```bash
        composer install 
        ```
        
4.  **Configurer les variables d’environnement** :
    
    -   Copier le fichier `.env.example` en `.env` :
        
         ```bash
        cp www/.env.example www/.env
        ```
        
    -   Modifier les informations de connexion à la base de données, par exemple :
        
         ```makefile
        DB_HOST=adresse_vers_mysql
        DB_NAME=nom_de_ta_base
        DB_USER=utilisateur_mysql
        DB_PASS=mdp_utilisateur_mysql
        ```
        
5.  **Créer la base de données** :
    
    -   Lancer MySQL/MariaDB localement.
        
    -   Importer le fichier SQL fourni :
        
        ```bash
        mysql -u nom_de_ta_base -p nom_de_ta_base < init-db.sql
        ```
        
6.  **Vérifier que le site est accessible** :
    
    -   Lancer ton serveur local.

7.  **Se connecter avec les utilisateurs de base** :
	- Utilisateur Admin :
	    -   Identifiant : admin
	    -  Mot de passe : admin
    - Utilisateur :
	    -   Identifiant : user
	    -  Mot de passe : user

   

# Utilisation avec Docker 🐳

## 🚀 Démarrage rapide

### 1. Cloner le projet

```bash
git clone https://github.com/votre-utilisateur/votre-projet.git
cd votre-projet
```
### 2. Copier le fichier .env
```bash
cp www/.env.example www/.env
```
Modifiez-le avec vos informations personnelles (BDD, mot de passe, etc.).

### 3. Lancer l’environnement
```bash
make build
```

### 4. Se connecter avec les utilisateurs de base :
  - Utilisateur Admin :
    -   Identifiant : admin
	  -  Mot de passe : admin
  - Utilisateur :
	  -   Identifiant : user
	   -  Mot de passe : user
### 🌐 Accès aux services

- Application	http://localhost:8000 
- phpMyAdmin	http://localhost:8080
- Mailpit	http://localhost:1080

## 🔧 Commandes utiles (make)
```bash
make build             # Lancer les conteneurs avec build
make stop              # Stopper les conteneurs
make restart           # Redémarrer tout
make php               # Ouvrir un shell bash dans le conteneur PHP
make composer-install  # Installer les dépendances PHP
make composer-update   # Mettre à jour les dépendances PHP
make db-reset          # Réinitialiser la base de données (⚠️ supprime les données)
make migrate           # Exécuter le script init-db.sql manuellement
make logs              # Voir les logs en direct
make open-app          # Ouvrir l'application dans le navigateur
make open-db           # Ouvrir phpMyAdmin dans le navigateur
make open-mailpit      # Ouvrir Mailpit dans le navigateur
```
### 🐘 Composer
Installer les dépendances PHP :

```bash
make composer-install
```
### ✉️ Test des e-mails

Tous les e-mails envoyés depuis l'application sont capturés dans Mailpit.

### 🐞 Xdebug
Déjà installé et configuré :

Port : 9003

Mode : debug

### 📦 Prérequis

| Système            | Docker seul suffit ? | Docker Desktop requis ? |
|--------------------|----------------------|--------------------------|
| 🐧 **Linux**        | ✅ Oui               | ❌ Non                   | 
| 🪟 **Windows**      | ⚠️ Non (complexe)    | ✅ Oui                   | 
| 🍏 **macOS**        | ⚠️ Non (complexe) | ✅ Oui                   | 

✅ Recommandations

- **Linux** : Installez Docker (`docker`, `docker-compose`, `make`) ou Docker Desktop
- **Windows/macOS** : Utilisez Docker Desktop pour éviter les compliquation de configuration réseau et VM.
- **GNU Make (make)** recommandé pour utiliser les raccourcis

### 🧹 Nettoyage
```bash
make stop        # Stopper tous les services
make db-reset    # Supprimer les volumes et redémarrer proprement
```
📄 Licence
MIT – Faites ce que vous voulez
