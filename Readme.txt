Plugin TheliaApi permettant de rajouter une api au CMS e-commerce Thelia.

En cours de développement, à ne pas utiliser sur un environnement de production.

INSTALLATION: 

    via git : 
        Cloner le repo dans votre répertoire plugins : 

        cd client/plugins
        git clone https://github.com/lunika/theliaApi.git theliaApi

    ou bien en téléchargement classique: 

        télécharger le plugin à l'adresse suivante : https://github.com/lunika/theliaApi/downloads
        extraire l'archive obtenu dans le répertoir client/plugins

    Une fois le plugin installé, il vous faut l'activer dans l'interface d'administration de thelia : configuration/activation des plugins

    Une fois activé, se rendre dans modules/theliApi et créer un utilisateur en renseignant son nom, son prénom, son login, ainsi que son mot de passe et sa confirmation.
    Retenez le login et le mot de passe, il faudra les renseigner lorsque vous utiliserez l'API

    Une fois l'utilisateur crée, vous pourrez régler les accès en lecture et écriture de cet utilisateur aux fonctionnalités de l'API. Cliquez sur le lien "Modifier les droits" correpondant à l'utilisateur.

UTILISATION DE L'API
    
    - Il s'agit d'une API rest.
    - Le résultat est pour l'instant retourné au format JSON
    - L'authentification est de type basic http auth
    - Il est fortement conseillé d'utiliser l'API via https

    l'API est joignable via l'action api ex : www.domaine.tld/?action=api
    les méthodes de l'api soit joignables via le paramètre subaction. Exemple pour la création d'un compte client : 
        https://www.domaine.tld/?action=api&subaction=create_account

VALEUR DE RETOUR DE L'API

    L'API retourne tout le temps un message sous forme de JSON
    Le premier paramètre est le status qui peut prendre 2 valeurs : ok ou ko
        ok : l'action s'est effectué avec succès. D'autres paramètres seront passés suivant l'action réalisé.
        ko : un problème est survenu

GESTION DES ERREURS : 

    Si une erreur survient, la réponse sera ko, accompagné d'un code et d'un message d'erreur.
    ex : {"status":"ko",
         "error":"create_account",
         "errorMessage":"unavailable Resource",
         "errorCode":10000004}
         
         error = méthode concerné (ici create_account permettant la création d'un compte client)
         errorMessage = Message d'erreur. Ici l'utilisateur de l'API n'a pas les droits nécessaire pour créer un compte client
         errorCode = Code correspondant à l'erreur

LISTE DES MÉTHODES : 
    La liste des méthodes est disponible sur le wiki du plugin : https://github.com/lunika/theliaApi/wiki

