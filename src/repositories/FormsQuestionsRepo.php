<?php

namespace Repository;

use Tigress\Repository;

/**
 * Class FormsQuestionsRepo
 */
class FormsQuestionsRepo extends Repository
{
    /**
     * Initialize the repository
     */
    public function __construct()
    {
        $this->dbName = 'default';
        $this->table = 'forms_questions';
        $this->primaryKey = ['id'];
        $this->model = 'DefaultModel';
        $this->autoload = true;
        $this->softDelete = true;
        parent::__construct();
    }

    /**
     * Get the maximum sort value for questions in a specific section of a survey.
     *
     * @param mixed $forms_section_id
     * @return int
     */
    public function getMaxSort(mixed $forms_section_id): int
    {
        $sql = "SELECT MAX(sort) AS max_sort
                FROM forms_questions
                WHERE forms_section_id = :forms_section_id";
        $keyBindings = [
            'forms_section_id' => $forms_section_id,
        ];
        $result = $this->getByQuery($sql, $keyBindings);
        return (int)($result[0]->max_sort ?? 0); // Return 0 if no questions found
    }
}