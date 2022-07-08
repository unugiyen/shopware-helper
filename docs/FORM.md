# Form

## Controller

- Extend from `src/Form/AbstractFormController.php`
- Set snippet prefix with `getSnippetNameSpacePrefix()`
- Define validation with `getDataValidationDefinition()`
- Handle form with `handleForm()`

---

Examples: `src/Form/Controller/CustomFormController.php`

---

## Twig

- Create a template and extend from `src/Resources/views/storefront/component/wdt-form.html.twig`
- Create fields in `{% block wdt_form_content %}`
- Create hidden fields in `{% block wdt_form_hidden_fields %}`

---

Examples: `src/Resources/views/storefront/component/wdt-form.html.twig`

---
