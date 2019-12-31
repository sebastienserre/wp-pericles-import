=== WP Pericles Import ===
Plugin Name: WP Pericles Import
Plugin URI: https://www.thivinfo.com
Contributors: sebastienserre
Tags: Real Estate, Pericles
Requires at least: 4.6
Requires PHP: 5.6
Tested up to: 5.3
Stable tag: 1.3.0
License: GPL V2 or later

# WP Pericles Import
## Introduction  
Cette extension WordPress a été créée suite à mon tutoriel [Exporter des biens immobilers de Péricles 5  vers WordPress.](https://thivinfo.com/blog/tutoriels/exporter-des-biens-immobiliers-de-pericles-5-vers-wordpress)  
Suite à plusieurs demandes, j'ai créé cette extension qui va synchroniser via une tache planifié les biens entre Péricles et WordPress.  

## Comment ca fonctionne?  
Cette extension crée un type de contenu "real-estate-property" et va décompresser et lire l'export Péricles pour créer ou mettre à jour les biens immobilier dans WordPress.  
Ensuite, libre à vous de créer les templates de pages en respectant la [wphierarchy](https://wphierarchy.com/)  

## Astuces  
Vous pouvez renommer le slug du CPT créé via les réglages de l'extension. Il faudra bien veiller à regénérer les permaliens en visitant la page Réglages > Permaliens de WordPress.  

## Hooks  
Il y a des hooks d'actions et de filtres un peu partout dans l'extension afin de permettre aux développeurs d'étendre les fonctionnalités de cette extension.  
S'il vous en manque, n'hésitez pas à me [contacter par mail](mailto:support@thivinfo.com)

## Mise en place  
Dans un premier temps, il faut que Péricles exporte les données vers votre serveur Web.  
* Pour cela, créer un accès FTP dédié pointant vers le répertoire ```/wp-content/uploads/import```. C'est ici que Péricles doit déposer son fichier zip contenant les photos des biens ainsi que le fichier xml contenant les caractéristiques des biens.  
* Suivez la 1ère partie de mon tutoriel sur le paramétrage de Péricles 5.  
* Renseignez les réglages de WP Pericles Import le non du fichier zip.
* Attendez que le Cron passe... vos biens s'importent...

## WP-Cli  
Si vous avez beaucoup de biens Immobiliers, il se peut que le cron de WordPress subisse des arrêts du a des temps d'exécution trop long.  
Pour contrer cela, un commande WP-CLI est disponible: `wp wp_pericles_import` 

## Disclaimer  
Cette extension embarque ACF Pro 5.8.0 afin de faciliter la gestion des champs additionnels.  
Vous pouvez donc créer vos templates en utilisant les fonctions d'ACF ( get_field(), the_field()... )
Il est interdit de réutiliser la verison d'ACF embarquée à d'autres fins, dans une autre extension.
Aucune clés API n'est fourni, la version d'ACF sera mise a jour, seulement si  cette extension en a besoin ou en cas de faille de sécurité découverte dans ACF..  
Enfin, Si vous avez ACF disponible dans votre projet, il n'y aura pas de conflit entre les 2 versions.