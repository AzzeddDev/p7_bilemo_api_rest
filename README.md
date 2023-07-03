# p7_API_REST
Projet 7 de mon parcours Développeur d'application PHP/Symfony à OpenClassrooms

# BileMo
### Documentation
Une interface pour documenter l'API et teser les différentes méthodes a été réalisée à l'aide de NelmioApiDocBundle.

### Diagrammes UML
* Diagramme de classe
* Diagramme de cas d'utilsation
* Diagramme de séquence

### Installation
* Symfony 5.2.*
* PHP 7.3.*
* MySql 8

### Suivre les étapes suivantes :
* Etape 1.1 : Cloner le repository suivant depuis votre terminal :
```
git clone https://github.com/AzzeddDev/p7_API_REST.git
```

* Etape 1.2 : Executer la commande suivante :
```
composer install
```

* Etape 2 : Editer le fichier .env
> pour renseigner vos paramètres de connexion à votre base de donnée dans la variable DATABASE_URL

* Etape 3 : Démarrer votre environnement local (Par exemple : Wamp Server OU Mamp)

* Etape 4 : Exécuter les commandes symfony suivantes depuis votre terminal
```
symfony console doctrine:database:create (ou php bin/console d:d:c si vous n'avez pas installé le client symfony)
symfony console doctrine:migrations:migrate
symfony console doctrine:fictures:load
```

* Etape 5.1 : Générer vos clés pour l'utilisation de JWT Token
```
php bin/console lexik:jwt:generate-keypair
```

* Etape 5.2 : Renseigner vos paramètres de configuration dans votre ficher .env
```
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=VotrePassePhrase
###< lexik/jwt-authentication-bundle ###
```

* Etape 6 : Lancer le serveur
```
symfony server:start
```

* Etape 7 : Générer un Token pour pouvoir tester l'API
   - Visiter /api/doc ou dans Postman a l'adresse ``` https://127.0.0.1:8000/api/login_check ```


### Vous êtes fin prêt pour tester votre API!
Pour afficher la doucmentation en ligne et tester l'APIrendez-vous à l'adresse suivante votre navigateur : 
