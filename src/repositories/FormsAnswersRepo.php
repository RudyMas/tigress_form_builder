<?php

namespace Repository;

use Tigress\Repository;

/**
 * Class FormsRepo
 */
class FormsAnswersRepo extends Repository
{
    /**
     * Initialize the repository
     */
    public function __construct()
    {
        $this->dbName = 'default';
        $this->table = 'forms_answers';
        $this->primaryKey = ['id'];
        $this->model = 'DefaultModel';
        $this->autoload = true;
        $this->softDelete = true;
        parent::__construct();
    }
}