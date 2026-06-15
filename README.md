# Yahya Haroun - Portfolio & PWA

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-Black?style=for-the-badge&logo=inertia&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Supabase](https://img.shields.io/badge/Supabase-3ECF8E?style=for-the-badge&logo=supabase&logoColor=white)

Ce projet est une **Progressive Web App (PWA)** complète servant de portfolio professionnel et de plateforme de gestion d'opportunités (contacts, contrats, partenariats). Conçue avec un haut niveau d'exigence technique et esthétique, l'application garantit une expérience utilisateur premium, ultra-rapide et résiliente, même sans connexion internet.

## ✨ Fonctionnalités Clés

*   **PWA Hautes Performances** : Mode Offline interactif. Les visiteurs peuvent naviguer sur le site, consulter le portfolio, et soumettre des formulaires de contact/partenariat sans connexion. Une file d'attente locale (IndexedDB) synchronise les données en arrière-plan dès le retour du réseau.
*   **Web Push Notifications natives** : Alertes Push envoyées en temps réel (avec son/vibration) directement à l'administrateur lors du téléchargement d'un CV ou d'une proposition commerciale, même avec l'application fermée.
*   **Sécurité Passwordless & OTP** : Les visiteurs sensibles (téléchargement de CV, partenariats) vérifient leur identité via un code OTP envoyé par email avant d'accéder aux ressources.
*   **Génération PDF Dynamique ATS-Friendly** : Le téléchargement du CV génère à la volée un fichier PDF structuré à partir des dernières données de la base, via `barryvdh/laravel-dompdf`.
*   **Panel Administrateur Intégré** : Interface back-office sécurisée pour gérer dynamiquement :
    *   Compétences (Hard skills & Soft skills par catégories).
    *   Parcours et Expériences (`Timeline`).
    *   Portfolio Vidéo & Photo.
    *   Statistiques et Analytics (Visites, Téléchargements, Requêtes en temps réel).
*   **Base de Données Cloud Supabase** : Stockage PostgreSQL robuste et sécurisé, couplé au système de gestion de fichiers Cloud.

## 🔒 Architecture de Sécurité (SecOps)

La sécurité de cette plateforme est conçue selon les standards de l'OWASP :
1.  **Isolation des Identifiants Admin** : Les identifiants de l'administrateur (Email, Password) ne sont pas hardcodés. Ils sont gérés directement en base de données de production avec des algorithmes de hachage robustes (`Bcrypt`).
2.  **Protection des Variables d'Environnement** : Fichiers `.env` stictement exclus via `.gitignore` pour éviter toute fuite sur des repos publics.
3.  **Validation Stricte des Formulaires** : Middleware OTP limitant le spam bot et validant l'authenticité des requêtes entrantes.

## 🚀 Guide d'Installation Rapide

### Prérequis
- PHP >= 8.3
- Composer & NPM
- Un projet Supabase (PostgreSQL)

### 1. Cloner le dépôt et installer les dépendances
```bash
git clone https://github.com/Yahyaharoun/portfolio-pwa.git
cd portfolio-pwa
composer install
npm install
```

### 2. Configuration de l'environnement
Copiez le fichier d'exemple et générez la clé de chiffrement Laravel :
```bash
cp .env.example .env
php artisan key:generate
```
Renseignez vos clés `SUPABASE_DB_URL`, `MAIL_MAILER` (pour les envois OTP), et vos identifiants VAPID pour les notifications Push.

### 3. Migrations de la Base de Données
Connectez le projet à votre base de données Supabase, puis exécutez les migrations :
```bash
php artisan migrate --force
```

### 4. Génération des clés VAPID (Notifications Push)
```bash
php artisan webpush:vapid
```
*(Cela ajoutera automatiquement `VAPID_PUBLIC_KEY` et `VAPID_PRIVATE_KEY` dans votre `.env`)*

### 5. Compiler les assets PWA
```bash
npm run build
```

### 6. Lancer l'environnement local
```bash
php artisan serve
```

## 🛠️ Stack Technique Détaillée

- **Backend** : Laravel 11
- **Frontend** : Vue.js 3 (Composition API), Inertia.js, Tailwind CSS
- **Base de données** : Supabase (PostgreSQL)
- **Stockage** : Supabase Storage (disques s3-like configurés sur Laravel)
- **PDF Engine** : DomPDF
- **Service Worker / PWA** : IndexedDB (`offlineQueue.ts`), Web Push (`laravel-notification-channels/webpush`)

---
*Créé par Yahya Haroun - Tous droits réservés.*
