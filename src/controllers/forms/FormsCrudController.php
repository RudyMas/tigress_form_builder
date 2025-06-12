<?php

namespace Controller\forms;

use JetBrains\PhpStorm\NoReturn;
use Repository\FormsRepo;
use Repository\FormsSectionsRepo;
use Repository\FormsQuestionsRepo;
use Tigress\Core;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class FormsCrudController
 *
 * @author Rudy Mas <rudy.mas@go-next.be>
 * @copyright 2025 GO! Next (https://www.go-next.be)
 * @license Proprietary
 * @version 2025.06.12.0
 * @package Controller\forms
 */
class FormsCrudController
{
    /**
     * @throws LoaderError
     */
    public function __construct()
    {
        TWIG->addPath('vendor/tigress/form-builder/src/views');
    }

    /**
     * Delete a form.
     *
     * @return void
     */
    #[NoReturn] public function deleteForm(): void
    {
        SECURITY->checkAccess();

        if (RIGHTS->checkRights() === false) {
            $_SESSION['error'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'U heeft niet de vereiste rechten om deze pagina te bekijken.',
                'fr' => 'Vous n\'avez pas les droits requis pour voir cette page.',
                default => 'You do not have the required permissions to view this page.'
            };
            TWIG->redirect('/login');
        }

        $forms = new FormsRepo();
        $forms->deleteById((int)$_POST['DeleteForm']);
        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => "Het formulier werd succesvol verwijderd.",
            'fr' => "Le formulaire a été supprimé avec succès.",
            default => "The form was successfully deleted."
        };
        TWIG->redirect('/forms');
    }

    /**
     * Remove a section from a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn] public function deleteSection(array $args): void
    {
        SECURITY->checkAccess();

        if (RIGHTS->checkRights() === false) {
            $_SESSION['error'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'U heeft niet de vereiste rechten om deze pagina te bekijken.',
                'fr' => 'Vous n\'avez pas les droits requis pour voir cette page.',
                default => 'You do not have the required permissions to view this page.'
            };
            TWIG->redirect('/login');
        }

        $formsSections = new FormsSectionsRepo();
        $formsSections->deleteById((int)$args['forms_section_id']);

        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'De sectie werd succesvol verwijderd.',
            'fr' => 'La section a été supprimée avec succès.',
            default => 'The section was successfully deleted.'
        };
        TWIG->redirect('/forms/questions/' . $args['form_id']);
    }

    /**
     * Delete a question from a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn] public function deleteQuestion(array $args): void
    {
        SECURITY->checkAccess();

        if (RIGHTS->checkRights() === false) {
            $_SESSION['error'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'U heeft niet de vereiste rechten om deze pagina te bekijken.',
                'fr' => 'Vous n\'avez pas les droits requis pour voir cette page.',
                default => 'You do not have the required permissions to view this page.'
            };
            TWIG->redirect('/login');
        }

        $formsQuestions = new FormsQuestionsRepo();
        $formsQuestions->deleteById((int)$args['forms_question_id']);

        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => "De vraag werd succesvol verwijderd.",
            'fr' => "La question a été supprimée avec succès.",
            default => "The question was successfully deleted."
        };
        TWIG->redirect('/forms/questions/' . $args['form_id']);
    }

    /**
     * Get all forms, either active or inactive based on the 'show' argument.
     *
     * @param array $args
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getAll(array $args): void
    {
        if (RIGHTS->checkRights() === false) {
            TWIG->render(null, [], 'DT');
            return;
        }

        $forms = new FormsRepo();
        if ($args['show'] == 'active') {
            $forms->loadAllActive();
        } else {
            $forms->loadAllInactive();
        }

        TWIG->render(null, $forms->toArray(), 'DT');
    }

    /**
     * Show the questions of a form.
     *
     * @param array $args
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getQuestions(array $args): void
    {
        if (RIGHTS->checkRights() === false) {
            $_SESSION['error'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'U heeft niet de vereiste rechten om deze pagina te bekijken.',
                'fr' => 'Vous n\'avez pas les droits requis pour voir cette page.',
                default => 'You do not have the required permissions to view this page.'
            };
            TWIG->redirect('/login');
        }

        $forms = new FormsRepo();
        $data = $forms->getAllQuestions($args);

        TWIG->render(null, $data, 'DT');
    }

    /**
     * Restore a deleted form.
     *
     * @return void
     */
    #[NoReturn] public function restoreForm(): void
    {
        SECURITY->checkAccess();

        if (RIGHTS->checkRights() === false) {
            $_SESSION['error'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'U heeft niet de vereiste rechten om deze pagina te bekijken.',
                'fr' => 'Vous n\'avez pas les droits requis pour voir cette page.',
                default => 'You do not have the required permissions to view this page.'
            };
            TWIG->redirect('/login');
        }

        $forms = new FormsRepo();
        $forms->undeleteById((int)$_POST['RestoreForm']);
        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'Het formulier werd succesvol hersteld.',
            'fr' => 'Le formulaire a été restauré avec succès.',
            default => 'The form was successfully restored.'
        };
        TWIG->redirect('/forms?show=archive');
    }

    /**
     * Add or edit a form.
     *
     * @return void
     */
    #[NoReturn] public function saveForm(): void
    {
        SECURITY->checkAccess();

        $forms = new FormsRepo();
        if ($_POST['id'] > 0) {
            $forms->loadById($_POST['id']);
        } else {
            $forms->new();
        }
        $form = $forms->current();
        $form->updateByPost($_POST);
        $forms->save($form);

        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'Het formulier werd succesvol opgeslagen.',
            'fr' => 'Le formulaire a été enregistré avec succès.',
            default => 'The form was successfully saved.'
        };
        TWIG->redirect('/forms');
    }

    /**
     * Save a question within a form.
     *
     * @return void
     */
    #[NoReturn] public function saveVraag(): void
    {
        SECURITY->checkAccess();

        $formsQuestions = new FormsQuestionsRepo();
        $maxSort = $formsQuestions->getMaxSort($_POST['forms_section_id']);
        $formsQuestions->reset();

        if ($_POST['forms_question_id'] > 0) {
            $formsQuestions->loadById($_POST['forms_question_id']);
        } else {
            $formsQuestions->new();
        }
        $formsQuestion = $formsQuestions->current();
        $formsQuestion->updateByPost($_POST);
        $formsQuestion->sort = $maxSort + 1; // Volgende sortering
        $formsQuestions->save($formsQuestion);

        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'De vraag werd succesvol opgeslagen.',
            'fr' => 'La question a été enregistrée avec succès.',
            default => 'The question was successfully saved.'
        };
        TWIG->redirect('/forms/questions/' . $_POST['form_id']);
    }

    /**
     * Save the changes for the questions within a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn] public function saveQuestions(array $args): void
    {
        SECURITY->checkAccess();

        $formsSections = new FormsSectionsRepo();
        foreach ($_POST['formsSections'] as $sectionId => $data) {
            $formsSections->reset();
            $formsSections->loadById($sectionId);
            $formsSection = $formsSections->current();
            $formsSection->updateByPost($data);
            $formsSections->save($formsSection);
        }

        $formsQuestions = new FormsQuestionsRepo();
        foreach ($_POST['formsQuestions'] as $questionId => $data) {
            $formsQuestions->reset();
            $formsQuestions->loadById($questionId);
            $formsQuestion = $formsQuestions->current();
            $formsQuestion->updateByPost($data);
            $formsQuestions->save($formsQuestion);
        }

        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'De wijzigingen werden succesvol opgeslagen.',
            'fr' => 'Les modifications ont été enregistrées avec succès.',
            default => 'The changes were successfully saved.'
        };
        TWIG->redirect('/forms/questions/' . $args['id']);
    }

    /**
     * Adds a new section to a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn] public function addSection(array $args): void
    {
        SECURITY->checkAccess();

        $formsSections = new FormsSectionsRepo();
        $maxSort = $formsSections->getMaxSort($args['form_id']);
        $formsSections->new();
        $formsSection = $formsSections->current();
        $formsSection->form_id = (int)$args['form_id'];
        $formsSection->name = $_POST['name'];
        $formsSection->description = $_POST['description'];
        $formsSection->sort = $maxSort + 1;
        $formsSections->save($formsSection);

        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'De sectie werd succesvol toegevoegd.',
            'fr' => 'La section a été ajoutée avec succès.',
            default => 'The section was successfully added.'
        };
        TWIG->redirect('/forms/questions/' . $args['form_id']);
    }

    /**
     * Add a new question to a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn] public function addQuestion(array $args): void
    {
        SECURITY->checkAccess();

        $formsQuestions = new FormsQuestionsRepo();
        $formsQuestions->new();
        $formsQuestion = $formsQuestions->current();
        $formsQuestion->updateByPost($_POST);
        $formsQuestion->forms_section_id = (int)$args['forms_section_id'];
        $formsQuestions->save($formsQuestion);

        $_SESSION['success'] = match(substr(CONFIG->website->html_lang, 0, 2)) {
            'nl' => 'De vraag werd succesvol toegevoegd.',
            'fr' => 'La question a été ajoutée avec succès.',
            default => 'The question was successfully added.'
        };
        TWIG->redirect('/forms/questions/' . $args['form_id']);
    }
}