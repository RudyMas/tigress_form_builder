<?php

namespace Repository;

use Tigress\Repository;

/**
 * Class BevragingenRepo
 */
class SystemFormBuilderFieldTypesRepo extends Repository
{
    /**
     * Initialize the repository
     */
    public function __construct()
    {
        $this->dbName = 'default';
        $this->table = 'system_form_builder_field_types';
        $this->primaryKey = ['id'];
        $this->model = 'DefaultModel';
        $this->autoload = true;
        $this->softDelete = true;
        parent::__construct();
    }
}