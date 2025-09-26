<?php

namespace Controller\forms;

use JetBrains\PhpStorm\NoReturn;
use Repository\FormsAnswersRepo;
use Repository\FormsRepo;
use Repository\FormsSectionsRepo;
use Repository\FormsQuestionsRepo;
use Tigress\Controller;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class FormsCrudController
 *
 * @author Rudy Mas <rudy.mas@go-next.be>
 * @copyright 2025 GO! Next (https://www.go-next.be)
 * @license Proprietary
 * @version 2025.09.26.0
 * @package Controller\forms
 */
class FormsCrudController extends Controller
{
    /**
     * @throws LoaderError
     */
    public function __construct()
    {
        TWIG->addPath('vendor/tigress/form-builder/src/views');
        TRANSLATIONS->load(SYSTEM_ROOT . '/vendor/tigress/form-builder/translations/translations.json');
    }

    /**
     * Delete a form.
     *
     * @return void
     */
    #[NoReturn]
    public function deleteForm(): void
    {
        SECURITY->checkAccess();
        $this->checkRights();

        $forms = new FormsRepo();
        $forms->deleteById((int)$_POST['DeleteForm']);
        $_SESSION['success'] = __('The form was successfully deleted.');
        TWIG->redirect('/forms');
    }

    /**
     * Remove a section from a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn]
    public function deleteSection(array $args): void
    {
        SECURITY->checkAccess();
        $this->checkRights();

        $formsSections = new FormsSectionsRepo();
        $formsSections->deleteById((int)$args['forms_section_id']);

        $_SESSION['success'] = __('The section was successfully deleted.');
        TWIG->redirect('/forms/questions/' . $args['form_id']);
    }

    /**
     * Delete a question from a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn]
    public function deleteQuestion(array $args): void
    {
        SECURITY->checkAccess();
        $this->checkRights();

        $formsQuestions = new FormsQuestionsRepo();
        $formsQuestions->deleteById((int)$args['forms_question_id']);

        $_SESSION['success'] = __('The question was successfully deleted.');
        TWIG->redirect('/forms/questions/' . $args['form_id']);
    }

    /**
     * Duplicate a form.
     *
     * @return void
     */
    #[NoReturn]
    public function duplicateForm(): void
    {
        SECURITY->checkAccess();
        $this->checkRights();

        $forms = new FormsRepo();
        $forms->loadById((int)$_POST['form_id']);
        $oldForm = $forms->current();
        $forms->reset();
        $forms->new();
        $newForm = $forms->current();
        $newForm->form_reference = $_POST['form_reference'];
        $newForm->name = $_POST['name'];
        $newForm->type_id = $oldForm->type_id;
        $newForm->repeat_button = (int)$_POST['repeat_button'];
        $newForm->db_table = $_POST['db_table'];
        $forms->save($newForm);

        $formsSections = new FormsSectionsRepo();
        $formsSections->loadByWhere(['form_id' => $oldForm->id]);
        $oldFormsSections = clone $formsSections;
        foreach ($oldFormsSections as $formSection) {
            $formsSections->reset();
            $formsSections->new();
            $newFormSection = $formsSections->current();
            $newFormSection->form_id = $newForm->id;
            $newFormSection->name = $formSection->name;
            $newFormSection->description = $formSection->description;
            $newFormSection->sort = $formSection->sort;
            $formsSections->save($newFormSection);

            $formsQuestions = new FormsQuestionsRepo();
            $formsQuestions->reset();
            $formsQuestions->loadByWhere(['forms_section_id' => $formSection->id]);
            $oldFormsQuestions = clone $formsQuestions;
            foreach ($oldFormsQuestions as $formQuestion) {
                $formsQuestions->reset();
                $formsQuestions->new();
                $newFormQuestion = $formsQuestions->current();
                $newFormQuestion->forms_section_id = $newFormSection->id;
                $newFormQuestion->question = $formQuestion->question;
                $newFormQuestion->field_type_id = $formQuestion->field_type_id;
                $newFormQuestion->length = $formQuestion->length;
                $newFormQuestion->required = $formQuestion->required;
                $newFormQuestion->disabled = $formQuestion->disabled;
                $newFormQuestion->extra_info = $formQuestion->extra_info;
                $newFormQuestion->extra_input = $formQuestion->extra_input;
                $newFormQuestion->sort = $formQuestion->sort;
                $newFormQuestion->db_field = $formQuestion->db_field;
                $formsQuestions->save($newFormQuestion);
            }
        }

        $_SESSION['success'] = __('The form was successfully duplicated.');
        TWIG->redirect('/forms');
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
     * @param array $args
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getAnswersFromForm(array $args): void
    {
        SECURITY->checkAccess();

        $formsAnswers = new FormsAnswersRepo();
        $data = $formsAnswers->getAnswersByFormId($args['id']);

        TWIG->render(null, $data, 'DT');
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
        $this->checkRights();

        $forms = new FormsRepo();
        $data = $forms->getAllQuestions($args);

        TWIG->render(null, $data, 'DT');
    }

    /**
     * Restore a deleted form.
     *
     * @return void
     */
    #[NoReturn]
    public function restoreForm(): void
    {
        SECURITY->checkAccess();
        $this->checkRights();

        $forms = new FormsRepo();
        $forms->undeleteById((int)$_POST['RestoreForm']);
        $_SESSION['success'] = __('The form was successfully restored.');
        TWIG->redirect('/forms?show=archive');
    }

    /**
     * Add or edit a form.
     *
     * @return void
     */
    #[NoReturn]
    public function saveForm(): void
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

        $_SESSION['success'] = __('The form was successfully saved.');
        TWIG->redirect('/forms');
    }

    /**
     * Save a question within a form.
     *
     * @return void
     */
    #[NoReturn]
    public function saveVraag(): void
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

        $_SESSION['success'] = __('The question was successfully saved.');
        TWIG->redirect('/forms/questions/' . $_POST['form_id']);
    }

    /**
     * Save the changes for the questions within a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn]
    public function saveQuestions(array $args): void
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

        $_SESSION['success'] = __('The changes were successfully saved.');
        TWIG->redirect('/forms/questions/' . $args['id']);
    }

    /**
     * Adds a new section to a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn]
    public function addSection(array $args): void
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

        $_SESSION['success'] = __('The section was successfully added.');
        TWIG->redirect('/forms/questions/' . $args['form_id']);
    }

    /**
     * Add a new question to a form.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn]
    public function addQuestion(array $args): void
    {
        SECURITY->checkAccess();

        $formsQuestions = new FormsQuestionsRepo();
        $formsQuestions->new();
        $formsQuestion = $formsQuestions->current();
        $formsQuestion->updateByPost($_POST);
        $formsQuestion->forms_section_id = (int)$args['forms_section_id'];
        $formsQuestions->save($formsQuestion);

        $_SESSION['success'] = __('The question was successfully added.');
        TWIG->redirect('/forms/questions/' . $args['form_id']);
    }
}