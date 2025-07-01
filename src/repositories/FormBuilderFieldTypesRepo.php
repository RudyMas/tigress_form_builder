<?php

namespace Repository;

use Tigress\DataRepository;

/**
 * Class FormBuilderFieldTypesRepo
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
            'name_de' => [
                'value' => '',
                'type' => 'string',
            ],
            'name_fr' => [
                'value' => '',
                'type' => 'string',
            ],
            'name_nl' => [
                'value' => '',
                'type' => 'string',
            ],
            'name_en' => [
                'value' => '',
                'type' => 'string',
            ],
            'name_es' => [
                'value' => '',
                'type' => 'string',
            ],
            'name_it' => [
                'value' => '',
                'type' => 'string',
            ],
            'name_sv' => [
                'value' => '',
                'type' => 'string',
            ],
        ]);

        $this->setData([
            [
                'id' => 1,
                'input_type' => 'text',
                'name_de' => 'Text',
                'name_en' => 'Text',
                'name_es' => 'Texto',
                'name_fr' => 'Texte',
                'name_it' => 'Testo',
                'name_nl' => 'Tekst',
                'name_sv' => 'Text',
            ],
            [
                'id' => 2,
                'input_type' => 'textarea',
                'name_de' => 'Textbereich',
                'name_en' => 'Text area',
                'name_es' => 'Área de texto',
                'name_fr' => 'Zone de texte',
                'name_it' => 'Area di testo',
                'name_nl' => 'Tekstvak',
                'name_sv' => 'Textområde',
            ],
            [
                'id' => 3,
                'input_type' => 'number',
                'name_de' => 'Nummer',
                'name_en' => 'Number',
                'name_es' => 'Número',
                'name_fr' => 'Nombre',
                'name_it' => 'Numero',
                'name_nl' => 'Nummer',
                'name_sv' => 'Nummer',
            ],
            [
                'id' => 4,
                'input_type' => 'email',
                'name_de' => 'E-Mail',
                'name_en' => 'Email',
                'name_es' => 'Correo electrónico',
                'name_fr' => 'E-mail',
                'name_it' => 'E-mail',
                'name_nl' => 'E-mail',
                'name_sv' => 'E-post',
            ],
            [
                'id' => 5,
                'input_type' => 'tel',
                'name_de' => 'Telefon',
                'name_en' => 'Telephone',
                'name_es' => 'Teléfono',
                'name_fr' => 'Téléphone',
                'name_it' => 'Telefono',
                'name_nl' => 'Telefoon',
                'name_sv' => 'Telefon',
            ],
            [
                'id' => 16,
                'input_type' => 'url',
                'name_de' => 'Weblink',
                'name_en' => 'Weblink',
                'name_es' => 'Enlace web',
                'name_fr' => 'Lien web',
                'name_it' => 'Collegamento web',
                'name_nl' => 'Weblink',
                'name_sv' => 'Webblänk',
            ],
            [
                'id' => 6,
                'input_type' => 'password',
                'name_de' => 'Passwort',
                'name_en' => 'Password',
                'name_es' => 'Contraseña',
                'name_fr' => 'Mot de passe',
                'name_it' => 'Password',
                'name_nl' => 'Wachtwoord',
                'name_sv' => 'Lösenord',
            ],
            [
                'id' => 7,
                'input_type' => 'select',
                'name_de' => 'Auswahlliste',
                'name_en' => 'Selection list',
                'name_es' => 'Lista de selección',
                'name_fr' => 'Liste de sélection',
                'name_it' => 'Elenco di selezione',
                'name_nl' => 'Keuzelijst',
                'name_sv' => 'Urvalslista',
            ],
            [
                'id' => 8,
                'input_type' => 'datetime-local',
                'name_de' => 'Datum + Uhrzeit',
                'name_en' => 'Date + Time',
                'name_es' => 'Fecha + Hora',
                'name_fr' => 'Date + Heure',
                'name_it' => 'Data + Ora',
                'name_nl' => 'Datum + Tijd',
                'name_sv' => 'Datum + Tid',
            ],
            [
                'id' => 9,
                'input_type' => 'date',
                'name_de' => 'Datum',
                'name_en' => 'Date',
                'name_es' => 'Fecha',
                'name_fr' => 'Date',
                'name_it' => 'Data',
                'name_nl' => 'Datum',
                'name_sv' => 'Datum',
            ],
            [
                'id' => 18,
                'input_type' => 'month',
                'name_de' => 'Monat',
                'name_en' => 'Month',
                'name_es' => 'Mes',
                'name_fr' => 'Mois',
                'name_it' => 'Mese',
                'name_nl' => 'Maand',
                'name_sv' => 'Månad',
            ],
            [
                'id' => 19,
                'input_type' => 'week',
                'name_de' => 'Woche',
                'name_en' => 'Week',
                'name_es' => 'Semana',
                'name_fr' => 'Semaine',
                'name_it' => 'Settimana',
                'name_nl' => 'Week',
                'name_sv' => 'Vecka',
            ],
            [
                'id' => 10,
                'input_type' => 'time',
                'name_de' => 'Zeit',
                'name_en' => 'Time',
                'name_es' => 'Hora',
                'name_fr' => 'Heure',
                'name_it' => 'Ora',
                'name_nl' => 'Tijd',
                'name_sv' => 'Tid',
            ],
            [
                'id' => 11,
                'input_type' => 'checkbox',
                'name_de' => 'Kontrollkästchen',
                'name_en' => 'Checkbox',
                'name_es' => 'Casilla de verificación',
                'name_fr' => 'Case à cocher',
                'name_it' => 'Casella di controllo',
                'name_nl' => 'Selectievakje',
                'name_sv' => 'Kryssruta',
            ],
            [
                'id' => 12,
                'input_type' => 'radio',
                'name_de' => 'Optionsfeld',
                'name_en' => 'Radio button',
                'name_es' => 'Botón de opción',
                'name_fr' => 'Bouton radio',
                'name_it' => 'Pulsante radio',
                'name_nl' => 'Keuzerondje',
                'name_sv' => 'Radioknapp',
            ],
            [
                'id' => 13,
                'input_type' => 'file',
                'name_de' => 'Datei hochladen',
                'name_en' => 'File upload',
                'name_es' => 'Subir archivo',
                'name_fr' => 'Téléverser un fichier',
                'name_it' => 'Caricamento file',
                'name_nl' => 'Bestand uploaden',
                'name_sv' => 'Filuppladdning',
            ],
            [
                'id' => 14,
                'input_type' => 'color',
                'name_de' => 'Farbwähler',
                'name_en' => 'Color picker',
                'name_es' => 'Selector de color',
                'name_fr' => 'Sélecteur de couleur',
                'name_it' => 'Selettore di colore',
                'name_nl' => 'Kleurkiezer',
                'name_sv' => 'Färgplockare',
            ],
            [
                'id' => 15,
                'input_type' => 'range',
                'name_de' => 'Schieberegler',
                'name_en' => 'Range slider',
                'name_es' => 'Deslizador de rango',
                'name_fr' => 'Curseur de plage',
                'name_it' => 'Slider di intervallo',
                'name_nl' => 'Schaal',
                'name_sv' => 'Intervallreglage',
            ],
            [
                'id' => 17,
                'input_type' => 'search',
                'name_de' => 'Suche',
                'name_en' => 'Search',
                'name_es' => 'Buscar',
                'name_fr' => 'Recherche',
                'name_it' => 'Ricerca',
                'name_nl' => 'Zoeken',
                'name_sv' => 'Sök',
            ],
            [
                'id' => 20,
                'input_type' => 'hidden',
                'name_de' => 'Verstecktes Feld',
                'name_en' => 'Hidden',
                'name_es' => 'Oculto',
                'name_fr' => 'Champ caché',
                'name_it' => 'Nascosto',
                'name_nl' => 'Verborgen veld',
                'name_sv' => 'Dolt',
            ],
            [
                'id' => 100,
                'input_type' => 'hr',
                'name_de' => 'Layout: Horizontale Linie',
                'name_en' => 'Layout: Horizontal line',
                'name_es' => 'Diseño: Línea horizontal',
                'name_fr' => 'Disposition : Ligne horizontale',
                'name_it' => 'Layout: Linea orizzontale',
                'name_nl' => 'Indeling: Horizontale lijn',
                'name_sv' => 'Layout: Horisontell linje',
            ],
            [
                'id' => 101,
                'input_type' => 'p',
                'name_de' => 'Layout: Textabschnitt',
                'name_en' => 'Layout: Text section',
                'name_es' => 'Diseño: Sección de texto',
                'name_fr' => 'Disposition : Section de texte',
                'name_it' => 'Layout: Sezione di testo',
                'name_nl' => 'Indeling: Tekstsectie',
                'name_sv' => 'Layout: Textavsnitt',
            ],
        ]);
    }

    /**
     * Get the input type by ID.
     *
     * @param int $id
     * @return string
     */
    public function getInputType(int $id): string
    {
        return $this->get($id)->input_type ?? false;
    }
}