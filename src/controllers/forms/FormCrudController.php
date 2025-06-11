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
 * @version 2025.06.11.0
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
                $this->handleFileUpload($key, $value, $formsAnswers, $uniqId);
            }
        }

        TWIG->redirect('/form/success');
    }

    /**
     * @param int|string $key
     * @param array $value
     * @param Repository $formsAnswers
     * @param string $uniqCode
     * @return void
     */
    private function handleFileUpload(int|string $key, array $value, Repository $formsAnswers, string $uniqCode): void
    {
        $uploadFolder = SYSTEM_ROOT . '/public/files/forms';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0775, true);
        }

        $tmpPath = $_FILES[$key]['tmp_name'];
        $originalName = $_FILES[$key]['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = date('Ymd_His') . '_' . uniqid() . '.' . $extension;
        $destination = $uploadFolder . '/' . $safeName;

        $formsAnswers->reset();
        $formsAnswers->new();
        $formAnswers = $formsAnswers->current();
        $formAnswers->uniq_code = $uniqCode;
        $formAnswers->forms_question_id = (int)$key;

        if (!isset($_FILES[$key]['name']) || empty($_FILES[$key]['name'])) {
            if (CONFIG->website->html_lang === 'nl-BE' || CONFIG->website->html_lang === 'nl') {
                $formAnswers->answer = 'ERROR: geen bestand geselecteerd';
            } elseif (CONFIG->website->html_lang === 'fr-BE' || CONFIG->website->html_lang === 'fr') {
                $formAnswers->answer = 'ERREUR : aucun fichier sélectionné';
            } else {
                $formAnswers->answer = 'ERROR: no file selected';
            }
            $formsAnswers->save($formAnswers);
            return;
        } elseif (!isset($_FILES[$key]['error']) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK) {
            if (CONFIG->website->html_lang === 'nl-BE' || CONFIG->website->html_lang === 'nl') {
                $formAnswers->answer = 'ERROR: uploaden van het bestand mislukte';
            } elseif (CONFIG->website->html_lang === 'fr-BE' || CONFIG->website->html_lang === 'fr') {
                $formAnswers->answer = 'ERREUR : l\'envoi du fichier a échoué';
            } else {
                $formAnswers->answer = 'ERROR: file upload failed';
            }
            $formsAnswers->save($formAnswers);
            return;
        }

        if (move_uploaded_file($tmpPath, $destination)) {
            $formAnswers->answer = '/private/forms/' . $safeName;
        } else {
            if (CONFIG->website->html_lang === 'nl-BE' || CONFIG->website->html_lang === 'nl') {
                $formAnswers->answer = 'ERROR: verplaatsen van het bestand is mislukt';
            } elseif (CONFIG->website->html_lang === 'fr-BE' || CONFIG->website->html_lang === 'fr') {
                $formAnswers->answer = 'ERREUR : le déplacement du fichier a échoué';
            } else {
                $formAnswers->answer = 'ERROR: moving the file failed';
            }
        }
        $formsAnswers->save($formAnswers);
    }
}