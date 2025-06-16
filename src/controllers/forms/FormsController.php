<?php

namespace Controller\forms;

use chillerlan\QRCode\Common\EccLevel;
use Repository\FormBuilderTilesRepo;
use Repository\FormsAnswersRepo;
use Repository\FormsRepo;
use Repository\FormsSectionsRepo;
use Repository\FormsQuestionsRepo;
use Repository\FormBuilderFieldTypesRepo;
use Tigress\Core;
use Tigress\QrCodeGenerator;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class FormsController
 *
 * @author Rudy Mas <rudy.mas@go-next.be>
 * @copyright 2025 GO! Next (https://www.go-next.be)
 * @license Proprietary
 * @version 2025.06.10.0
 * @package Controller\forms
 */
class FormsController
{
    /**
     * @throws LoaderError
     */
    public function __construct()
    {
        TWIG->addPath('vendor/tigress/form-builder/src/views');
    }

    /**
     * Overview of the forms.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): void
    {
        if (RIGHTS->checkRights() === false) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            TWIG->redirect('/login');
        }

        TWIG->render('forms/index.twig');
    }

    /**
     * Add/Edit a form.
     *
     * @param array $args
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(array $args): void
    {
        SECURITY->checkAccess();

        if (RIGHTS->checkRights() === false) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            TWIG->redirect('/login');
        }

        $forms = new FormsRepo();
        if ($args['id'] == 0) {
            $forms->new();
            $actionButton = match(substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'Toevoegen',
                'fr' => 'Ajouter',
                default => 'Add',
            };
        } else {
            $forms->loadById($args['id']);
            $actionButton = match(substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'Aanpassen',
                'fr' => 'Modifier',
                default => 'Update',
            };
        }

        $tiles = new FormBuilderTilesRepo();
        $tiles->setData([
            [
                'id' => 1,
                'tile_name' => 'Voorstel tot aankoop (VTA)',
            ],
            [
                'id' => 2,
                'tile_name' => 'nog te bepalen ...',
            ],
        ]);
        $tiles->load();

        TWIG->render('forms/edit.twig', [
            'actionButton' => $actionButton,
            'form' => $forms->current(),
            'tiles' => $tiles,
        ]);
    }

    /**
     * Add/Edit questions for a form.
     *
     * @param array $args
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function editQuestions(array $args): void
    {
        SECURITY->checkAccess();

        if (RIGHTS->checkRights() === false) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            TWIG->redirect('/login');
        }

        $forms = new FormsRepo();
        $forms->loadById($args['id']);
        $form = $forms->current();

        $formsSections = new FormsSectionsRepo();
        $formsSections->loadByWhere([
            'form_id' => $args['id'],
            'active' => 1,
        ], 'sort');

        $formsQuestions = new FormsQuestionsRepo();
        $formsQuestions->loadByWhereQuery("forms_section_id IN (SELECT id FROM forms_sections WHERE form_id = :form_id AND active = 1) AND active = 1", [
            'form_id' => $args['id'],
        ], 'sort');

        $formBuilderFieldTypes = new FormBuilderFieldTypesRepo();
        $formBuilderFieldTypes->load();

        TWIG->render('forms/edit_questions.twig', [
            'form' => $form,
            'formsSections' => $formsSections,
            'formsQuestions' => $formsQuestions,
            'formBuilderFieldTypes' => $formBuilderFieldTypes,
        ]);
    }

    /**
     * Create and show a QR code for a form.
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function showQr(array $args): void
    {
        SECURITY->checkAccess();

        if (RIGHTS->checkRights() === false) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            TWIG->redirect('/login');
        }

        $forms = new FormsRepo();
        $forms->loadById($args['id']);
        $form = $forms->current();

        $url = 'https://gunax.go-next.be/form/' . $form->form_reference;

        $qrCodeDir = SYSTEM_ROOT . '/public/images/forms/qr-code';
        if (!is_dir($qrCodeDir)) {
            mkdir($qrCodeDir, 0755, true);
        }

        $qr = new QrCodeGenerator([
            'addLogoSpace' => true,
            'eccLevel' => EccLevel::H,
            'logoSpaceWidth' => 16,
        ]);

        $image = $qr->renderWithLogo(
            $url,
            SYSTEM_ROOT . '/public/images/GoNext_black.png',
            $qrCodeDir . '/' . $form->form_reference . '_logo.png'
        );

        TWIG->render('forms/show_qr.twig', [
            'form' => $form,
            'url' => $url,
            'qrImage' => $image,
        ]);
    }

    public function answersIndex(array $args): void
    {
        SECURITY->checkAccess();

        if (RIGHTS->checkRights() === false) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            TWIG->redirect('/login');
        }

        $forms = new FormsRepo();
        $forms->loadById($args['id']);
        $form = $forms->current();

        TWIG->render('forms/answers_index.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Check if the user has the necessary rights to view the page.
     *
     * @return void
     */
    private function checkRights(): void
    {
        if (RIGHTS->checkRights() === false) {
            $_SESSION['error'] = match (substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'U heeft niet de juiste rechten om deze pagina te bekijken.',
                'fr' => 'Vous n\'avez pas les droits nécessaires pour voir cette page.',
                default => 'You do not have the necessary rights to view this page.',
            };
            TWIG->redirect('/login');
        }
    }
}