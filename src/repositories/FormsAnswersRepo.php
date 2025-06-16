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

    /**
     * Get answers by form ID.
     *
     * @param mixed $id
     * @return array
     */
    public function getAnswersByFormId(mixed $id): array
    {
        $sql = "SELECT fa.uniq_code, fa.forms_question_id, fa.created_user_id, fa.created, users.last_name, users.first_name
                FROM forms_answers as fa
                JOIN forms_questions as fq ON fq.id = fa.forms_question_id
                JOIN forms_sections as fs ON fs.id = fq.forms_section_id
                JOIN forms as f ON f.id = fs.form_id
                LEFT JOIN users ON users.id = fa.created_user_id
                WHERE f.id = :id
                GROUP BY fa.uniq_code, fa.created
                ORDER BY fa.created DESC";
        $keyBindings = [
            'id' => $id,
        ];
        return $this->getByQuery($sql, $keyBindings);
    }
}