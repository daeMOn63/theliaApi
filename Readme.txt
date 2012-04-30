Plugin TheliaApi permettantde rajouter une api au CMS e-commerce Thelia.

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


