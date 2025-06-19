<?php

namespace Controller\forms;

use JetBrains\PhpStorm\NoReturn;
use Repository\FormsAnswersRepo;
use Tigress\Controller;
use Tigress\Repository;

/**
 * Class FormCrudController
 *
 * @author Rudy Mas <rudy.mas@go-next.be>
 * @copyright 2025 GO! Next (https://www.go-next.be)
 * @license Proprietary
 * @version 2025.06.19.0
 * @package Controller\forms
 */
class FormCrudController extends Controller
{
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
                $error = match (substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'STOP! De klasse ' . $repoName . ' bestaat niet.',
                    'fr' => 'STOP! La classe ' . $repoName . ' n\'existe pas.',
                    'de' => 'STOP! Die Klasse ' . $repoName . ' existiert nicht.',
                    'es' => '¡ALTO! La clase ' . $repoName . ' no existe.',
                    'it' => 'STOP! La classe ' . $repoName . ' non esiste.',
                    default => 'STOP! The class ' . $repoName . ' does not exist.',
                };
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
                    'de' => 'Keine Datei eingereicht',
                    'es' => 'Ningún archivo enviado',
                    'it' => 'Nessun file inviato',
                    default => 'No file submitted',
                };
                $formsAnswers->save($formAnswers);
                return;
            } elseif (!isset($error) || $error !== UPLOAD_ERR_OK) {
                $formAnswers->answer = match(substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'ERROR: uploaden van het bestand mislukte',
                    'fr' => 'ERREUR : l\'envoi du fichier a échoué',
                    'de' => 'FEHLER: Datei-Upload fehlgeschlagen',
                    'es' => 'ERROR: la carga del archivo falló',
                    'it' => 'ERRORE: caricamento del file non riuscito',
                    default => 'ERROR: file upload failed',
                };
                $formsAnswers->save($formAnswers);
                return;
            }

            if (move_uploaded_file($tmpPath, $destination)) {
                $formAnswers->answer = '/public/files/forms/' . $safeName;
            } else {
                $formAnswers->answer = match(substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'ERROR: verplaatsen van het bestand is mislukt',
                    'fr' => 'ERREUR : le déplacement du fichier a échoué',
                    'de' => 'FEHLER: Verschieben der Datei fehlgeschlagen',
                    'es' => 'ERROR: mover el archivo falló',
                    'it' => 'ERRORE: spostamento del file non riuscito',
                    default => 'ERROR: moving the file failed',
                };
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
            $error = match (substr(CONFIG->website->html_lang, 0, 2)) {
                'nl' => 'STOP! Meerdere bestandsuploads worden nog niet ondersteund voor database formulieren.',
                'fr' => 'STOP! Les téléchargements de plusieurs fichiers ne sont pas encore pris en charge pour les formulaires de base de données.',
                'de' => 'STOP! Mehrfache Datei-Uploads werden für Datenbankformulare noch nicht unterstützt.',
                'es' => '¡ALTO! Las cargas de múltiples archivos aún no son compatibles con los formularios de base de datos.',
                'it' => 'STOP! Gli upload multipli di file non sono ancora supportati per i moduli del database.',
                default => 'STOP! Multiple file uploads are not supported for database forms yet.',
            };
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
                $formAnswers->$key = match(substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'Geen bestand ingezonden',
                    'fr' => 'Aucun fichier soumis',
                    'de' => 'Keine Datei eingereicht',
                    'es' => 'Ningún archivo enviado',
                    'it' => 'Nessun file inviato',
                    default => 'No file submitted',
                };
                $formsAnswers->save($formAnswers);
                return;
            } elseif (!isset($error) || $error !== UPLOAD_ERR_OK) {
                $formAnswers->$key = match(substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'ERROR: uploaden van het bestand mislukte',
                    'fr' => 'ERREUR : l\'envoi du fichier a échoué',
                    'de' => 'FEHLER: Datei-Upload fehlgeschlagen',
                    'es' => 'ERROR: la carga del archivo falló',
                    'it' => 'ERRORE: caricamento del file non riuscito',
                    default => 'ERROR: file upload failed',
                };
                $formsAnswers->save($formAnswers);
                return;
            }

            if (move_uploaded_file($tmpPath, $destination)) {
                $formAnswers->$key = '/public/files/forms/' . $safeName;
            } else {
                $formAnswers->$key = match(substr(CONFIG->website->html_lang, 0, 2)) {
                    'nl' => 'ERROR: verplaatsen van het bestand is mislukt',
                    'fr' => 'ERREUR : le déplacement du fichier a échoué',
                    'de' => 'FEHLER: Verschieben der Datei fehlgeschlagen',
                    'es' => 'ERROR: mover el archivo falló',
                    'it' => 'ERRORE: spostamento del file non riuscito',
                    default => 'ERROR: moving the file failed',
                };
            }

            $formsAnswers->save($formAnswers);
        }
    }
}