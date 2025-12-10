<?php

namespace Controller\forms;

use Repository\FormsAnswersRepo;
use Repository\FormsRepo;
use Repository\FormsSectionsRepo;
use Repository\FormsQuestionsRepo;
use Repository\FormBuilderFieldTypesRepo;
use Repository\FormViewerFormAccessRepo;
use Service\FormViewerService;
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
 * @version 2025.09.25.0
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
        TRANSLATIONS->load(SYSTEM_ROOT . '/vendor/tigress/form-builder/translations/translations.json');
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
            TWIG->redirect('/form/closed?ref=' . $args['form_reference']);
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
        $message = __('The form is already closed or not recognized.<br>Please contact the form administrator.');

        TWIG->render('forms/message.twig', [
            'message' => $message,
            'loadMenu' => 'forms/form_menu.twig',
            'urlForm' => '/form/' . $_GET['ref'],
        ]);
    }

    /**
     * Renders an overview of a external form based on the provided reference.
     *
     * @param array $args
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showExternal(array $args): void
    {
        $forms = new FormsRepo();
        $forms->loadByWhere([
            'form_reference_external' => $args['form_reference_external'],
            'active' => 1,
        ]);
        $form = $forms->current();

        TWIG->render('forms/index_form_external.twig', [
            'form' => $form,
            'formReferenceExternal' => $args['form_reference_external'],
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
        if ($_GET['ref'] == 'null') {
            $message = __('The form has been successfully saved.<br>You do not need to do anything further – you may now close your browser.');
            $showButton = false;
        } else {
            $message = __('The form has been successfully saved.<br>Press the button below to return to the form.');
            $showButton = true;
        }

        TWIG->render('forms/message.twig', [
            'message' => $message,
            'loadMenu' => 'forms/form_menu.twig',
            'showButton' => $showButton,
            'urlForm' => '/form/' . $_GET['ref'],
        ]);
    }

    /**
     * Render the answer view
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function viewAnswer(array $args): void
    {
        $data = $this->getFormAnswers($args);

        TWIG->render('forms/show_form_external.twig', [
            'form' => $data['form'],
            'formsAnswers' => $data['formsAnswers'],
            'formsSections' => $data['formsSections'],
            'uniq_code' => $args['uniq_code'],
            'loadMenu' => 'forms/form_menu.twig',
        ]);
    }

    /**
     * Get the form answers
     *
     * @param array $args
     * @return array
     */
    public function getFormAnswers(array $args): array
    {
        $formViewerFromAccess = new FormViewerFormAccessRepo();
        $formViewerFromAccess->loadByWhere(['user_id' => $_SESSION['user']['id']]);
        $formIds = array_map(fn($item) => $item['form_id'], $formViewerFromAccess->toArray());

        $forms = new FormsRepo();
        $forms->loadById($args['form_id']);
        $form = $forms->current();

        $formsSections = new FormsSectionsRepo();
        $formsSections->loadByWhere([
            'form_id' => $form->id,
            'active' => 1,
        ], 'sort');

        if (!empty($form->db_table)) {
            $repositoryClass = 'Repository\\' . $this->tableNameToClass($form->db_table);
            $formsAnswers = new $repositoryClass();
            $formsAnswers->loadById($args['uniq_code']);
            $formsAnswer = $formsAnswers->current();

            $allAnswers = [];
            if (class_exists($repositoryClass)) {
                foreach ($formsSections as $formsSection) {
                    $formsQuestions = new FormsQuestionsRepo();
                    $formsQuestions->loadByWhere([
                        'forms_section_id' => $formsSection->id,
                        'active' => 1,
                    ], 'sort');

                    foreach ($formsQuestions as $formsQuestion) {
                        $answer = new stdClass();
                        $answer->answer = $formsAnswer->{$formsQuestion->db_field} ?? '';
                        $answer->question__question = $formsQuestion->question;
                        $answer->question__field_type_id = $formsQuestion->field_type_id;
                        $answer->section__id = $formsSection->id;

                        $allAnswers[] = $answer;
                    }
                }
            } else {
                $_SESSION['error'] = __('The answers for this form are not available.');
                TWIG->redirect('/form-viewer/' . $form->id . '/answers/');
            }
        } else {
            $formsAnswers = new FormsAnswersRepo();
            $allAnswers = $formsAnswers->getAnswersByUniqCode($args['uniq_code']);
        }

        return [
            'form' => $form,
            'formsAnswers' => $allAnswers,
            'formsSections' => $formsSections,
        ];
    }
}