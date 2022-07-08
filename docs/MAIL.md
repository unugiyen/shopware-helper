# Mail

- Create a mail service and extend from `src/Mail/AbstractMailService.php`
- Make sure to validate templates with `validateAndGetTemplates()`, if these templates are going used to sent at the same time. The templates are also available after validation.
- Get a specific template with `getMailTemplate()`

---

Examples: `src/examples/src/Mail/CustomMailService.php`

---

### Misc


## Import local templates
- Place all locales in `src/Resources/views/email/<LOCALE>`, with files:`html.twig`, `plain.twig`, `subject.twig`
- `bin/console wdt:mail-template-import`
- Import per locale: `bin/console wdt:mail-template-import --locale=<LOCALE>` 
