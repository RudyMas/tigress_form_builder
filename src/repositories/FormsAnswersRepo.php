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
                  AND fa.active = :active
                GROUP BY fa.uniq_code, fa.created
                ORDER BY fa.created DESC";
        $keyBindings = [
            'id' => $id,
            'active' => 1,
        ];
        return $this->getByQuery($sql, $keyBindings);
    }

    /**
     * Get answers by unique code.
     *
     * @param mixed $uniq_code
     * @return array
     */
    public function getAnswersByUniqCode(mixed $uniq_code): array
    {
        $sql = "SELECT fa.*, fq.question as question__question, fq.field_type_id as question__field_type_id,
                    fq.extra_info as question__extra_info, fq.extra_input as question__extra_input, fs.id as section__id,
                    users.first_name, users.last_name
                FROM forms_answers as fa
                JOIN forms_questions as fq ON fq.id = fa.forms_question_id
                JOIN forms_sections as fs ON fs.id = fq.forms_section_id
                LEFT JOIN users ON users.id = fa.created_user_id
                WHERE fa.uniq_code = :uniq_code
                  AND fa.active = :active";
        $keyBindings = [
            'uniq_code' => $uniq_code,
            'active' => 1,
        ];
        return $this->getByQuery($sql, $keyBindings);
    }
}