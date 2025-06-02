<?php

namespace Repository;

use Tigress\DataRepository;

/**
 * Class BevragingenRepo
 */
class FormBuilderFieldTypesRepo extends DataRepository
{
    /**
     * Initialize the repository
     */
    public function __construct()
    {
        $this->dbName = null;
        $this->primaryKey = ['id'];
        $this->model = 'DefaultModel';
        parent::__construct();

        $this->setFields([
            'id' => [
                'value' => 0,
                'type' => 'int',
            ],
            'input_type' => [
                'value' => '',
                'type' => 'string',
            ],
            'name' => [
                'value' => '',
                'type' => 'string',
            ],
            'naam' => [
                'value' => '',
                'type' => 'string',
            ],
            'nom' => [
                'value' => '',
                'type' => 'string',
            ],
        ]);

        $this->setData([
            [
                'id' => 1,
                'input_type' => 'text',
                'name' => 'Text',
                'naam' => 'Tekst',
                'nom' => 'Texte',
            ],
            [
                'id' => 2,
                'input_type' => 'textarea',
                'name' => 'Textarea',
                'naam' => 'Lange tekst',
                'nom' => 'Texte longue',
            ],
            [
                'id' => 3,
                'input_type' => 'number',
                'name' => 'Number',
                'naam' => 'Getal',
                'nom' => 'Nombre',
            ],
            [
                'id' => 4,
                'input_type' => 'email',
                'name' => 'Email',
                'naam' => 'E-mail',
                'nom' => 'E-mail',
            ],
            [
                'id' => 5,
                'input_type' => 'password',
                'name' => 'Password',
                'naam' => 'Wachtwoord',
                'nom' => 'Mot de passe',
            ],
            [
                'id' => 6,
                'input_type' => 'select',
                'name' => 'Selection list',
                'naam' => 'Keuzelijst',
                'nom' => 'Liste déroulante',
            ],
            [
                'id' => 7,
                'input_type' => 'datatime',
                'name' => 'Date + Time',
                'naam' => 'Datum + Tijd',
                'nom' => 'Date + Heure',
            ],
            [
                'id' => 8,
                'input_type' => 'datetime-local',
                'name' => 'Local Date + Time',
                'naam' => 'Lokale Datum + Tijd',
                'nom' => 'Date + Heure Locale',
            ],
            [
                'id' => 9,
                'input_type' => 'date',
                'name' => 'Date',
                'naam' => 'Datum',
                'nom' => 'Date',
            ],
            [
                'id' => 10,
                'input_type' => 'time',
                'name' => 'Time',
                'naam' => 'Tijd',
                'nom' => 'Heure',
            ],
            [
                'id' => 11,
                'input_type' => 'checkbox',
                'name' => 'Checkbox',
                'naam' => 'Selectievakje',
                'nom' => 'Case à cocher',
            ],
            [
                'id' => 12,
                'input_type' => 'radio',
                'name' => 'Radio button',
                'naam' => 'Keuzerondje',
                'nom' => 'Bouton radio',
            ],
            [
                'id' => 13,
                'input_type' => 'file',
                'name' => 'File upload',
                'naam' => 'Bestand uploaden',
                'nom' => "Téléverser un fichier",
            ],
            [
                'id' => 14,
                'input_type' => 'color',
                'name' => 'Color picker',
                'naam' => 'Kleurkiezer',
                'nom' => "Sélecteur de couleur",
            ],
            [
                'id' => 15,
                'input_type' => 'range',
                'name' => 'Range slider',
                'naam' => "Schaal",
                'nom' => "Curseur de plage",
            ],
        ]);
    }
}