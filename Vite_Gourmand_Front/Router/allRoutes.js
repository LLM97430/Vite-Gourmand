import Route from "./Route.js";

//Définition des routes
export const allRoutes = [
   new Route("/Vite_Gourmand_Front/", "Accueil", "/Vite_Gourmand_Front/pages/home.html"),
   new Route("/Vite_Gourmand_Front/menus", "Nos menus", "/Vite_Gourmand_Front/pages/menus.html"),
   new Route("/Vite_Gourmand_Front/inscription", "Inscription", "/Vite_Gourmand_Front/auth/inscription.html", "/Vite_Gourmand_Front/auth/inscription.js"),
   new Route("/Vite_Gourmand_Front/connexion", "Connexion", "/Vite_Gourmand_Front/auth/connexion.html", "/Vite_Gourmand_Front/auth/connexion.js"),

]

export const websiteName = "Vite & Gourmand";