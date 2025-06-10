<?php

namespace Repository;

use Tigress\Repository;

/**
 * Class FormsRepo
 */
class FormsRepo extends Repository
{
    /**
     * Initialize the repository
     */
    public function __construct()
    {
        $this->dbName = 'default';
        $this->table = 'forms';
        $this->primaryKey = ['id'];
        $this->model = 'DefaultModel';
        $this->autoload = true;
        $this->softDelete = true;
        parent::__construct();
    }

    /**
     * @param array $args
     * @return array
     */
    public function getAllQuestions(array $args): array
    {
        $sql = "SELECT fq.*, fs.name AS section_name
                FROM forms_questions AS fq
                JOIN forms_sections AS fs ON fs.id = fq.forms_section_id AND fs.active = :active
                JOIN forms AS f ON f.id = fs.form_id
                WHERE f.id = :id
                  AND fq.active = :active
                ORDER BY fs.sort, fq.sort";
        $keyBindings = [
            'id' => $args['id'],
            'active' => ($args['show'] === 'active') ? 1 : 0,
        ];

        return $this->getByQuery($sql, $keyBindings);
    }
}