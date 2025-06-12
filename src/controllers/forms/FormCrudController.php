<?php

namespace Controller\forms;

use JetBrains\PhpStorm\NoReturn;
use Repository\FormsAnswersRepo;
use Tigress\Core;
use Tigress\Repository;

/**
 * Class FormCrudController
 *
 * @author Rudy Mas <rudy.mas@go-next.be>
 * @copyright 2025 GO! Next (https://www.go-next.be)
 * @license Proprietary
 * @version 2025.06.12.0
 * @package Controller\forms
 */
class FormCrudController
{
    /**
     * Save the form answers.
     *
     * @param array $args
     * @return void
     */
    #[NoReturn] public function saveForm(array $args): void
    {
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

        TWIG->redirect('/form/success');
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
                $formAnswers->answer = match(substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'Geen bestand ingezonden',
                    'fr' => 'Aucun fichier soumis',
                    default => 'No file submitted',
                };
                $formsAnswers->save($formAnswers);
                return;
            } elseif (!isset($error) || $error !== UPLOAD_ERR_OK) {
                $formAnswers->answer = match(substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'ERROR: uploaden van het bestand mislukte',
                    'fr' => 'ERREUR : l\'envoi du fichier a échoué',
                    default => 'ERROR: file upload failed',
                };
                $formsAnswers->save($formAnswers);
                return;
            }

            if (move_uploaded_file($tmpPath, $destination)) {
                $formAnswers->answer = '/private/forms/' . $safeName;
            } else {
                $formAnswers->answer = match(substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'ERROR: verplaatsen van het bestand is mislukt',
                    'fr' => 'ERREUR : le déplacement du fichier a échoué',
                    default => 'ERROR: moving the file failed',
                };
            }

            $formsAnswers->save($formAnswers);
        }
    }
}