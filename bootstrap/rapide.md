# Create project : composer create-project laravel/laravel e-learning
# install route(api.php) php artisan install:api
# Modify .env file
# run this commande php artisan key:generate
# run this commande php artisan l5-swagger:generate
# open this url http://localhost:8000/api/documentation#/
# Configurer la fonctionalité login with google avec CLIENT_ID Google
# Errore when using form data for put request : change to POST request and add ` ?_method=PUT` to the url like ` http://localhost:8000/api/lessons/1?_method=PUT`
    
# Create migration : php artisan make:migration create_user_quiz_table

# Etape 1 pour commencer le 1er cours:
` Débloquer automatiquement la première leçon (celle avec order = 1) pour chaque nouvel utilisateur qui n'a pas encore de progression enregistrée. course/{id}/start`  
# NOte : Modification apporter sur la methode submit dans quizController, Faire une migration avant de continuer
# 
# Payload creation quiz : 
  {
  "title": "Quiz Avancé Programmation",
  "description": "Évaluation approfondie des connaissances en PHP, JS, SQL, Laravel, Git et développement web",
  "questions": [
    {
      "type": "single_choice",
      "text": "Quel est le mot-clé pour définir une fonction en PHP ?",
      "answers": [
        { "text": "function", "is_correct": true },
        { "text": "def", "is_correct": false },
        { "text": "fn", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quelles bases de données sont compatibles avec Laravel ?",
      "answers": [
        { "text": "MySQL", "is_correct": true },
        { "text": "PostgreSQL", "is_correct": true },
        { "text": "MongoDB", "is_correct": false },
        { "text": "SQLite", "is_correct": true }
      ]
    },
    {
      "type": "text",
      "text": "Quelle commande Git permet de vérifier l'état des fichiers ?",
      "answers": [
        { "text": "git status", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quelle est la sortie de console.log(typeof null) en JS ?",
      "answers": [
        { "text": "object", "is_correct": true },
        { "text": "null", "is_correct": false },
        { "text": "undefined", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quels sont des frameworks PHP ?",
      "answers": [
        { "text": "Symfony", "is_correct": true },
        { "text": "Laravel", "is_correct": true },
        { "text": "Django", "is_correct": false },
        { "text": "CodeIgniter", "is_correct": true }
      ]
    },
    {
      "type": "text",
      "text": "Donnez la commande artisan pour créer un contrôleur en Laravel.",
      "answers": [
        { "text": "php artisan make:controller NomController", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quel langage est exécuté côté serveur ?",
      "answers": [
        { "text": "PHP", "is_correct": true },
        { "text": "JavaScript", "is_correct": false },
        { "text": "HTML", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quels protocoles sont utilisés pour les API REST ?",
      "answers": [
        { "text": "HTTP", "is_correct": true },
        { "text": "HTTPS", "is_correct": true },
        { "text": "FTP", "is_correct": false },
        { "text": "SMTP", "is_correct": false }
      ]
    },
    {
      "type": "text",
      "text": "Quelle commande SQL permet de sélectionner toutes les colonnes d'une table ?",
      "answers": [
        { "text": "SELECT * FROM table;", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quel est le framework JavaScript côté client le plus utilisé ?",
      "answers": [
        { "text": "React", "is_correct": true },
        { "text": "Laravel", "is_correct": false },
        { "text": "Spring", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quels sont les types de relations en Laravel Eloquent ?",
      "answers": [
        { "text": "One To One", "is_correct": true },
        { "text": "One To Many", "is_correct": true },
        { "text": "Many To Many", "is_correct": true },
        { "text": "Has Many Through", "is_correct": true }
      ]
    },
    {
      "type": "text",
      "text": "Donnez la commande pour installer un package en Laravel via Composer.",
      "answers": [
        { "text": "composer require vendor/package", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quelle méthode démarre une session en PHP ?",
      "answers": [
        { "text": "session_start()", "is_correct": true },
        { "text": "start()", "is_correct": false },
        { "text": "init_session()", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quels sont des types primitifs en JavaScript ?",
      "answers": [
        { "text": "string", "is_correct": true },
        { "text": "number", "is_correct": true },
        { "text": "boolean", "is_correct": true },
        { "text": "char", "is_correct": false }
      ]
    },
    {
      "type": "text",
      "text": "Quelle est la commande artisan pour lancer le serveur Laravel ?",
      "answers": [
        { "text": "php artisan serve", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quelle balise HTML est utilisée pour importer un fichier CSS ?",
      "answers": [
        { "text": "<link>", "is_correct": true },
        { "text": "<style>", "is_correct": false },
        { "text": "<css>", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quels sont des systèmes de gestion de version ?",
      "answers": [
        { "text": "Git", "is_correct": true },
        { "text": "SVN", "is_correct": true },
        { "text": "MySQL", "is_correct": false },
        { "text": "Mercurial", "is_correct": true }
      ]
    },
    {
      "type": "text",
      "text": "En PHP, quelle fonction permet d'inclure un fichier une seule fois ?",
      "answers": [
        { "text": "include_once", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quelle commande Git permet de créer une nouvelle branche ?",
      "answers": [
        { "text": "git branch nom_branche", "is_correct": true },
        { "text": "git new branch", "is_correct": false },
        { "text": "git init-branch", "is_correct": false }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quel langage est interprété par les navigateurs web ?",
      "answers": [
        { "text": "JavaScript", "is_correct": true },
        { "text": "PHP", "is_correct": false },
        { "text": "Python", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quels sont des systèmes de gestion de base de données relationnelle ?",
      "answers": [
        { "text": "MySQL", "is_correct": true },
        { "text": "PostgreSQL", "is_correct": true },
        { "text": "SQLite", "is_correct": true },
        { "text": "Redis", "is_correct": false }
      ]
    },
    {
      "type": "text",
      "text": "Quelle est la commande Git pour fusionner une branche ?",
      "answers": [
        { "text": "git merge nom_branche", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quelle directive Blade est utilisée pour afficher une variable en Laravel ?",
      "answers": [
        { "text": "{{ }}", "is_correct": true },
        { "text": "{!! !!}", "is_correct": false },
        { "text": "<?php ?>", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quels sont des langages compilés ?",
      "answers": [
        { "text": "C", "is_correct": true },
        { "text": "C++", "is_correct": true },
        { "text": "JavaScript", "is_correct": false },
        { "text": "Go", "is_correct": true }
      ]
    },
    {
      "type": "text",
      "text": "En SQL, quelle clause permet de filtrer les résultats ?",
      "answers": [
        { "text": "WHERE", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quelle commande installe les dépendances PHP via Composer ?",
      "answers": [
        { "text": "composer install", "is_correct": true },
        { "text": "composer run", "is_correct": false },
        { "text": "php install", "is_correct": false }
      ]
    },
    {
      "type": "multiple_choice",
      "text": "Quels frameworks front-end utilisent JavaScript ?",
      "answers": [
        { "text": "React", "is_correct": true },
        { "text": "Vue.js", "is_correct": true },
        { "text": "Angular", "is_correct": true },
        { "text": "Laravel", "is_correct": false }
      ]
    },
    {
      "type": "text",
      "text": "Quelle commande artisan génère une migration ?",
      "answers": [
        { "text": "php artisan make:migration", "is_correct": true }
      ]
    },
    {
      "type": "single_choice",
      "text": "Quel est le créateur de Laravel ?",
      "answers": [
        { "text": "Taylor Otwell", "is_correct": true },
        { "text": "Rasmus Lerdorf", "is_correct": false },
        { "text": "Brendan Eich", "is_correct": false }
      ]
    }
  ]
}

# Payload pour submit quiz
{
  "quiz_id": 1,
  "answers": [
    {
      "question_id": 1,
      "answer_ids": [1]
    },
    {
      "question_id": 2,
      "answer_ids": [4,5,7]
    },
    {
      "question_id": 3,
      "answer_ids": [8]
    },
    {
      "question_id": 4,
      "answer_ids": [9]
    },
    {
      "question_id": 5,
      "answer_ids": [12,13,15]
    },
    {
      "question_id": 6,
      "answer_ids": [16]
    },
    {
      "question_id": 7,
      "answer_ids": [17]
    },
    {
      "question_id": 8,
      "answer_ids": [20,21]
    },
    {
      "question_id": 9,
      "answer_ids": [24]
    },
    {
      "question_id": 10,
      "answer_ids": [25]
    },
    {
      "question_id": 11,
      "answer_ids": [28,29,31]
    },
    {
      "question_id": 12,
      "answer_ids": [32]
    }
  ]
}