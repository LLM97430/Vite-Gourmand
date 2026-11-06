// Vérification de la validité des champs du formulaire d'inscription
(() => {
   'use strict'

   const forms = document.querySelectorAll('form[data-bs-validate]')

   for (const form of forms) {
      form.addEventListener('submit', event => {
         if (!form.checkValidity()) {
         event.preventDefault()
         event.stopPropagation()
         }

         for (const control of form.elements) {
         if (control.willValidate) {
            control.setAttribute('aria-invalid', String(!control.validity.valid))
         }
         }
      })

      // Nettoyage de l'attribut aria-invalid lorsque les utilisateurs corrigent les champs individuels
      form.addEventListener('input', event => {
         const control = event.target
         if (control.willValidate && control.hasAttribute('aria-invalid')) {
         control.setAttribute('aria-invalid', String(!control.validity.valid))
         }
      })
   }
})()


//Vérification de la force du mot de passe
const element = document.querySelector('.strength')
const strength = new bootstrap.Strength(element, {

   minLength: 10,
   messages: {
      weak: 'Trop faible',
      fair: 'Pourrait être meilleur',
      good: 'Bon mot de passe',
      strong: 'Excellent!'
   }
})