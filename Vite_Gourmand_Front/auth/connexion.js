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