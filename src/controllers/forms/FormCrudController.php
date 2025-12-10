<?php

namespace Controller\forms;

use JetBrains\PhpStorm\NoReturn;
use Repository\FormsAnswersRepo;
use Repository\FormsRepo;
use Tigress\Controller;
use Tigress\Repository;

/**
 * Class FormCrudController
 *
 * @author Rudy Mas <rudy.mas@go-next.be>
 * @copyright 2025 GO! Next (https://www.go-next.be)
 * @license Proprietary
 * @version 2025.09.25.0
 * @package Controller\forms
 */
class FormCrudController extends Controller
{
    public function __construct()
    {
        TRANSLATIONS->load(SYSTEM_ROOT . '/vendor/tigress/form-builder/translations/translations.json');
    }

    public function getAnswers(array $args): void
    {
        $forms = new FormsRepo();
        $forms->loadByWhere([
            'form_reference_external' => $args['form_reference_external'],
        ]);
        $form = $forms->current();

        $formsAnswers = new FormsAnswersRepo();
        $data = $formsAnswers->getAnswersByFormId($form->id);

        TWIG->render(null, $data, 'DT');
    }

    /**
     * Save the form answers.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn] public function saveForm(array $args): void
    {
        if (!empty($_POST['db_table'])) {
            $repoName = 'Repository\\' . $this->tableNameToClass($_POST['db_table']);
            if (class_exists($repoName)) {
                $formsAnswers = new $repoName();
                $formsAnswers->new();
                $data = $formsAnswers->current();
                $data->updateByPost($_POST);
                $formsAnswers->save($data);

                if (!empty($_FILES)) {
                    foreach ($_FILES as $key => $value) {
                        $this->handleDBFileUpload($key, $formsAnswers);
                    }
                }
            } else {
                $error = __('STOP! The class ') . $repoName . __(' does not exist.');
                die($error);
            }
        } else {
            $uniqId = uniqid($args['form_reference'],true);
            $formsAnswers = new FormsAnswersRepo();
            foreach ($_POST as $key => $value) {
                $formsAnswers->reset();
                $formsAnswers->new();
                $formsAnswer = $formsAnswers->current();
                $formsAnswer->uniq_code = $uniqId;
                $formsAnswer->forms_question_id = (int)$key;
                $formsAnswer->answer = $value;
                $formsAnswers->save($formsAnswer);
            }

            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $value) {
                    $this->handleFileUpload($key, $formsAnswers, $uniqId);
                }
            }
        }

        ($_POST['repeat_button']) ? $ref = '/form/success?ref=' . $args['form_reference'] : $ref = '/form/success?ref=null';

        TWIG->redirect($ref);
    }

    /**
     * Handle file upload for form answers.
     * This method processes file uploads, saves them to a designated folder,
     *
     * @param int|string $key
     * @param Repository $formsAnswers
     * @param string $uniqCode
     * @return void
     */
    private function handleFileUpload(int|string $key, Repository $formsAnswers, string $uniqCode): void
    {
        $uploadFolder = SYSTEM_ROOT . '/public/files/forms';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0775, true);
        }

        $isMultiple = is_array($_FILES[$key]['name']);
        $totalFiles = $isMultiple ? count($_FILES[$key]['name']) : 1;

        for ($i = 0; $i < $totalFiles; $i++) {
            $name = $isMultiple ? $_FILES[$key]['name'][$i] : $_FILES[$key]['name'];
            $tmp_name = $isMultiple ? $_FILES[$key]['tmp_name'][$i] : $_FILES[$key]['tmp_name'];
            $error = $isMultiple ? $_FILES[$key]['error'][$i] : $_FILES[$key]['error'];
            $size = $isMultiple ? $_FILES[$key]['size'][$i] : $_FILES[$key]['size'];

            $tmpPath = $tmp_name;
            $originalName = $name;
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeName = date('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $destination = $uploadFolder . '/' . $safeName;

            $formsAnswers->reset();
            $formsAnswers->new();
            $formAnswers = $formsAnswers->current();
            $formAnswers->uniq_code = $uniqCode;
            $formAnswers->forms_question_id = (int)$key;

            if (empty($name)) {
                $formAnswers->answer = __('No file submitted');
                $formsAnswers->save($formAnswers);
                return;
            } elseif (!isset($error) || $error !== UPLOAD_ERR_OK) {
                $formAnswers->answer = __('ERROR: file upload failed');
                $formsAnswers->save($formAnswers);
                return;
            }

            if (move_uploaded_file($tmpPath, $destination)) {
                $formAnswers->answer = '/public/files/forms/' . $safeName;
            } else {
                $formAnswers->answer = __('ERROR: moving the file failed');
            }

            $formsAnswers->save($formAnswers);
        }
    }

    /**
     * Handle file upload for database forms.
     *
     * @param int|string $key
     * @param mixed $formsAnswers
     * @return void
     */
    private function handleDBFileUpload(int|string $key, mixed $formsAnswers): void
    {
        $uploadFolder = SYSTEM_ROOT . '/public/files/forms';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0775, true);
        }

        $isMultiple = is_array($_FILES[$key]['name']);
        $totalFiles = $isMultiple ? count($_FILES[$key]['name']) : 1;

        if ($totalFiles > 1) {
            $error = __('STOP! Multiple file uploads are not supported for database forms yet.');
            die($error);
        }

        for ($i = 0; $i < $totalFiles; $i++) {
            $name = $isMultiple ? $_FILES[$key]['name'][$i] : $_FILES[$key]['name'];
            $tmp_name = $isMultiple ? $_FILES[$key]['tmp_name'][$i] : $_FILES[$key]['tmp_name'];
            $error = $isMultiple ? $_FILES[$key]['error'][$i] : $_FILES[$key]['error'];
            $size = $isMultiple ? $_FILES[$key]['size'][$i] : $_FILES[$key]['size'];

            $tmpPath = $tmp_name;
            $originalName = $name;
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeName = date('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $destination = $uploadFolder . '/' . $safeName;

            $formAnswers = $formsAnswers->current();

            if (empty($name)) {
                $formAnswers->$key = __('No file submitted');
                $formsAnswers->save($formAnswers);
                return;
            } elseif (!isset($error) || $error !== UPLOAD_ERR_OK) {
                $formsAnswers->$key = __('ERROR: file upload failed');
                $formsAnswers->save($formAnswers);
                return;
            }

            if (move_uploaded_file($tmpPath, $destination)) {
                $formAnswers->$key = '/public/files/forms/' . $safeName;
            } else {
                $formsAnswers->$key = __('ERROR: moving the file failed');
            }

            $formsAnswers->save($formAnswers);
        }
    }
}