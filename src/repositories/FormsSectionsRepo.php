<?php

namespace Repository;

use Tigress\Repository;

/**
 * Class FormsRepo
 */
class FormsSectionsRepo extends Repository
{
    /**
     * Initialize the repository
     */
    public function __construct()
    {
        $this->dbName = 'default';
        $this->table = 'forms_sections';
        $this->primaryKey = ['id'];
        $this->model = 'DefaultModel';
        $this->autoload = true;
        $this->softDelete = true;
        parent::__construct();
    }

    /**
     * Get the maximum sort value for sections in a specific survey.
     *
     * @param mixed $id
     * @return int
     */
    public function getMaxSort(mixed $id): int
    {
        $sql = "SELECT MAX(sort) AS max_sort
                FROM forms_sections
                WHERE form_id = :id";
        $keyBindings = [
            'id' => $id,
        ];

        $result = $this->getByQuery($sql, $keyBindings);
        return (int)$result[0]->max_sort ?? 0; // Return 0 if no sections found
    }
}