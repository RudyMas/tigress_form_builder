<?php

namespace Repository;

use Tigress\DataRepository;

/**
 * Class FormBuilderTilesRepo
 * Repository for managing form builder tiles.
 *
 * Copy this file to your own project and adjust the tile names as needed.
 */
class FormBuilderTilesRepo extends DataRepository
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
            'tile_name' => [
                'value' => '',
                'type' => 'string',
            ],
        ]);

        $this->setData([
            [
                'id' => 1,
                'tile_name' => 'Name of a tile',
            ],
            [
                'id' => 2,
                'tile_name' => 'Name of a tile',
            ],
        ]);
    }

    /**
     * Get the tile name by ID.
     *
     * @param int $id
     * @return string
     */
    public function getTile(int $id): string
    {
        return $this->get($id)->tile ?? false;
    }
}