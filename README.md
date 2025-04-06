
# YORSIL - Plateforme de Services en Ligne

Ce projet est une adaptation React de la plateforme YORSIL, initialement développée en PHP/Symfony.

## Fonctionnalités

- Interface d'administration
- Interface utilisateur pour les boutiques
- Gestion des recharges mobile
- Transferts de crédit
- Paiement de factures
- Billeterie
- Journal des transactions

## Technologies utilisées

- React 18 avec TypeScript
- React Router pour la navigation
- Zustand pour la gestion d'état
- React Query pour la gestion des requêtes
- Tailwind CSS pour le styling

## Démarrage

### Prérequis

- Node.js 16.x ou supérieur
- npm ou yarn

### Installation

1. Clonez le dépôt
```bash
git clone https://github.com/votre-nom/yorsil-react.git
cd yorsil-react
```

2. Installez les dépendances
```bash
npm install
# ou
yarn install
```

3. Configurez les variables d'environnement
Créez un fichier `.env.local` à la racine du projet et ajoutez :
```
VITE_API_URL=http://localhost:8000/api
```

4. Lancez le serveur de développement
```bash
npm run dev
# ou
yarn dev
```

Le projet sera accessible à l'adresse [http://localhost:5173](http://localhost:5173)

## Structure du projet

- `/src/components` - Composants réutilisables
- `/src/pages` - Pages de l'application
- `/src/contexts` - Contextes React (authentification, etc.)
- `/src/services` - Services (API, etc.)
- `/src/stores` - Stores Zustand pour la gestion d'état
- `/src/types` - Types TypeScript

## Déploiement

Pour construire l'application pour la production:

```bash
npm run build
# ou
yarn build
```

Les fichiers statiques seront générés dans le dossier `dist`.

## Licence

Ce projet est sous licence privée. Tous droits réservés.
