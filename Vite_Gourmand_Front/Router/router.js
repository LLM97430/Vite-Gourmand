import Route from "./Route.js";
import { allRoutes, websiteName } from "./allRoutes.js";
import * as bootstrap from '../node_modules/bootstrap/dist/js/bootstrap.bundle.js';
window.bootstrap = bootstrap;

// Création d'une route pour la page 404 (page introuvable)
const route404 = new Route("404", "Page introuvable", "/Vite_Gourmand_Front/pages/404.html");

// Fonction pour récupérer la route correspondant à une URL donnée
const getRouteByUrl = (url) => {
   let currentRoute = null;
    // Parcours de toutes les routes pour trouver la correspondance
   allRoutes.forEach((element) => {
      if (element.url == url) {
            currentRoute = element;
      }
   });
    // Si aucune correspondance n'est trouvée, on retourne la route 404
   if (currentRoute != null) {
      return currentRoute;
   } else {
      return route404;
   }
};

// Fonction pour charger le contenu de la page
const LoadContentPage = async () => {
   const path = window.location.pathname;
    // Récupération de l'URL actuelle
   const actualRoute = getRouteByUrl(path);

   //Nettoyage de l'ancien script s'il existe
   const oldScript = document.getElementById('dynamic-script');
   if(oldScript) {
      oldScript.remove();
   }

    // Récupération du contenu HTML de la route
   const html = await fetch(actualRoute.pathHtml).then((data) => data.text());
    // Ajout du contenu HTML à l'élément avec l'ID "main-page"
   document.getElementById("main-page").innerHTML = html;

   //Réiinitialisation des composants Bootstrap
   initBootstrapComponents();

   // Injection du nouveau script s'il y en a un pour la route actuel
   if (actualRoute.pathJS != "") {
      var scriptTag=document.createElement("script");
      scriptTag.setAttribute("type", "text/javascript");
      scriptTag.setAttribute("src", actualRoute.pathJS);
      scriptTag.setAttribute("id", "dynamic-script");

      // Ajout de la balise script dans le body  
      document.querySelector("body").appendChild(scriptTag);
   }

    // Changement du titre de la page
   document.title = actualRoute.title + " - " + websiteName;
};

//Fonction pour initialiser les composants Bootstrap
const initBootstrapComponents = () => {
   //Dropdowns
   document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
      new bootstrap.Dropdown(el);
   });

   //Tooltips
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new bootstrap.Tooltip(el);
   });

   //Popovers
      document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
      new bootstrap.Popover(el);
   });

   //Collapse
      document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(el => {
      new bootstrap.Collapse(el);
   });

   //Offcanvas
      document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(el => {
      new bootstrap.Offcanvas(el);
   });
}

// Fonction pour gérer les événements de routage (clic sur les liens)
const routeEvent = (event) => {
   event = event || window.event;
   event.preventDefault();
    // Mise à jour de l'URL dans l'historique du navigateur
   window.history.pushState({}, "", event.target.href);
    // Chargement du contenu de la nouvelle page
   LoadContentPage();
};

// Gestion de l'événement de retour en arrière dans l'historique du navigateur
window.onpopstate = LoadContentPage;
// Assignation de la fonction routeEvent à la propriété route de la fenêtre
window.route = routeEvent;

// Chargement du contenu de la page au chargement initial
LoadContentPage();
