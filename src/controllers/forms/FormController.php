<?php

namespace Controller\forms;

use Repository\FormsRepo;
use Repository\FormsSectionsRepo;
use Repository\FormsQuestionsRepo;
use Repository\FormBuilderFieldTypesRepo;
use Tigress\Core;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class FormController
 *
 * @author Rudy Mas <rudy.mas@go-next.be>
 * @copyright 2025 GO! Next (https://www.go-next.be)
 * @license Proprietary
 * @version 2025.06.18.0
 * @package Controller\forms
 */
class FormController
{
    /**
     * @throws LoaderError
     */
    public function __construct()
    {
        TWIG->addPath('vendor/tigress/form-builder/src/views');
    }

    /**
     * Overview of the forms based on the provided form reference.
     *
     * @param array $args
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(array $args): void
    {
        $forms = new FormsRepo();
        $forms->loadByWhere([
            'form_reference' => $args['form_reference'],
            'active' => 1,
        ]);

        if ($forms->isEmpty()) {
            TWIG->redirect('/form/closed');
        }

        $form = $forms->current();

        $formsSections = new FormsSectionsRepo();
        $formsSections->loadByWhere([
            'form_id' => $form->id,
            'active' => 1,
        ], 'sort');

        $formsQuestions = new FormsQuestionsRepo();
        $formsQuestions->loadByWhereQuery("forms_section_id IN (SELECT id FROM forms_sections WHERE form_id = :form_id AND active = 1) AND active = 1", [
            'form_id' => $form->id,
        ], 'sort');

        $formBuilderFieldTypes = new FormBuilderFieldTypesRepo();
        $formBuilderFieldTypes->load();

        TWIG->render('forms/form_index.twig', [
            'numberOfSections' => $formsSections->count(),
            'form' => $form,
            'formsSections' => $formsSections->toArray(),
            'formsQuestions' => $formsQuestions->toArray(),
            'formBuilderFieldTypes' => $formBuilderFieldTypes,
            'loadMenu' => 'forms/form_menu.twig',
        ]);
    }

    /**
     * Render the closed page for forms.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function closed(): void
    {
        $message = match (substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'Het formulier is reeds gesloten of niet herkend.<br>Gelieve de beheerder van het formulier te contacteren.',
            'fr' => 'Le formulaire est déjà fermé ou non reconnu.<br>Veuillez contacter l\'administrateur du formulaire.',
            'de' => 'Das Formular ist bereits geschlossen oder nicht erkannt.<br>Bitte kontaktieren Sie den Formularadministrator.',
            'es' => 'El formulario ya está cerrado o no se reconoce.<br>Por favor, póngase en contacto con el administrador del formulario.',
            'it' => 'Il modulo è già chiuso o non riconosciuto.<br>Si prega di contattare l\'amministratore del modulo.',
            default => 'The form is already closed or not recognized.<br>Please contact the form administrator.',
        };

        TWIG->render('forms/message.twig', [
            'message' => $message,
            'loadMenu' => 'forms/form_menu.twig',
        ]);
    }

    /**
     * Renders the success page after saving the form.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function success(): void
    {
        $message = match (substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'Het formulier is succesvol opgeslagen.<br>Je hoeft verder niets te doen – je kunt nu uw browser sluiten.',
            'fr' => 'Le formulaire a été enregistré avec succès.<br>Vous n\'avez rien d\'autre à faire – vous pouvez maintenant fermer votre navigateur.',
            'de' => 'Das Formular wurde erfolgreich gespeichert.<br>Sie müssen nichts weiter tun – Sie können jetzt Ihren Browser schließen.',
            'es' => 'El formulario se ha guardado correctamente.<br>No necesita hacer nada más: ahora puede cerrar su navegador.',
            'it' => 'Il modulo è stato salvato con successo.<br>Non devi fare altro – puoi ora chiudere il tuo browser.',
            default => 'The form has been successfully saved.<br>You do not need to do anything further – you may now close your browser.',
        };

        TWIG->render('forms/message.twig', [
            'message' => $message,
            'loadMenu' => 'forms/form_menu.twig',
        ]);
    }
}