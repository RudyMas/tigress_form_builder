<?php

namespace Tigress;

/**
 * Class Menu (PHP version 8.4)
 *
 * @author Rudy Mas <rudy.mas@rudymas.be>
 * @copyright 2025 Rudy Mas (https://rudymas.be)
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
 * @version 2025.05.28.2
 * @package Tigress\FormBuilder
 */
class FormBuilder
{
    private string $form;
    private int $steps = 0;

    /**
     * Get the version of the DisplayHelper
     *
     * @return string
     */
    public static function version(): string
    {
        return '2025.05.28';
    }

    /**
     * @param string $accept_charset
     * @param string $action
     * @param string $autocomplete
     * @param string $class
     * @param string $enctype
     * @param string $id
     * @param string $method
     * @param string $name
     * @param string $target
     */
    public function __construct(
        string $accept_charset = 'UTF-8',
        string $action = '',
        string $autocomplete = 'on',
        string $class = '',
        string $enctype = 'application/x-www-form-urlencoded',
        string $id = '',
        string $method = 'POST',
        string $name = '',
        string $target = '',
    )
    {
        $this->form = '<form';
        if (!empty($accept_charset)) $this->form .= ' accept-charset="' . htmlspecialchars($accept_charset) . '"';
        if (!empty($action)) $this->form .= ' action="' . htmlspecialchars($action) . '"';
        if (!empty($autocomplete)) $this->form .= ' autocomplete="' . htmlspecialchars($autocomplete) . '"';
        if (!empty($class)) $this->form .= ' class="' . htmlspecialchars(trim($class)) . '"';
        if (!empty($enctype)) $this->form .= ' enctype="' . htmlspecialchars($enctype) . '"';
        if (!empty($id)) $this->form .= ' id="' . htmlspecialchars($id) . '"';
        if (!empty($method)) $this->form .= ' method="' . htmlspecialchars($method) . '"';
        if (!empty($name)) $this->form .= ' name="' . htmlspecialchars($name) . '"';
        if (!empty($target)) $this->form .= ' target="' . htmlspecialchars($target) . '"';
        $this->form .= '>';
    }

    /**
     * @param string $autofocus
     * @param string $disabled
     * @param string $form
     * @param string $name
     * @param string $type
     * @param string $value
     * @param string $id
     * @param string $class
     * @param string $aria_label
     * @param string $aria_describedby
     * @param string $aria_required
     * @param string $aria_invalid
     * @param string $aria_valuenow
     * @param string $aria_valuemin
     * @param string $aria_valuemax
     * @param string $aria_valuetext
     * @param string $text
     * @return void
     */
    public function addButton(
        string $autofocus = '',
        string $disabled = '',
        string $form = '',
        string $name = '',
        string $type = 'button',
        string $value = '',
        string $id = '',
        string $class = '',
        string $aria_label = '',
        string $aria_describedby = '',
        string $aria_required = '',
        string $aria_invalid = '',
        string $aria_valuenow = '',
        string $aria_valuemin = '',
        string $aria_valuemax = '',
        string $aria_valuetext = '',
        string $text = 'Submit'
    ): void
    {
        $buttonClass = $this->addClass($class, 'btn btn-primary');
        $this->form .= '<button';
        if (!empty($autofocus)) $this->form .= ' autofocus="' . htmlspecialchars($autofocus) . '"';
        if (!empty($disabled)) $this->form .= ' disabled="' . htmlspecialchars($disabled) . '"';
        if (!empty($form)) $this->form .= ' form="' . htmlspecialchars($form) . '"';
        if (!empty($name)) $this->form .= ' name="' . htmlspecialchars($name) . '"';
        if (!empty($type)) $this->form .= ' type="' . htmlspecialchars($type) . '"';
        if (!empty($value)) $this->form .= ' value="' . htmlspecialchars($value) . '"';
        if (!empty($id)) $this->form .= ' id="' . htmlspecialchars($id) . '"';
        if (!empty($buttonClass)) $this->form .= ' class="' . htmlspecialchars(trim($buttonClass)) . '"';
        if (!empty($aria_label)) $this->form .= ' aria-label="' . htmlspecialchars($aria_label) . '"';
        if (!empty($aria_describedby)) $this->form .= ' aria-describedby="' . htmlspecialchars($aria_describedby) . '"';
        if (!empty($aria_required)) $this->form .= ' aria-required="' . htmlspecialchars($aria_required) . '"';
        if (!empty($aria_invalid)) $this->form .= ' aria-invalid="' . htmlspecialchars($aria_invalid) . '"';
        if (!empty($aria_valuenow)) $this->form .= ' aria-valuenow="' . htmlspecialchars($aria_valuenow) . '"';
        if (!empty($aria_valuemin)) $this->form .= ' aria-valuemin="' . htmlspecialchars($aria_valuemin) . '"';
        if (!empty($aria_valuemax)) $this->form .= ' aria-valuemax="' . htmlspecialchars($aria_valuemax) . '"';
        if (!empty($aria_valuetext)) $this->form .= ' aria-valuetext="' . htmlspecialchars($aria_valuetext) . '"';
        $this->form .= '>' . htmlspecialchars($text) . '</button>';
    }

    /**
     * @param string $currentClass
     * @param string $defaultClass
     * @return string
     */
    private function addClass(string $currentClass, string $defaultClass): string
    {
        // Voeg de default class toe tenzij deze al aanwezig is
        if (empty($currentClass)) return $defaultClass;
        return (!str_contains($currentClass, $defaultClass)) ? "$currentClass $defaultClass" : $currentClass;
    }

    /**
     * @param string $id
     * @param array $options
     * @return void
     */
    public function addDatalist(
        string $id,
        array  $options = [],
    ): void
    {
        $this->form .= '<datalist id="' . htmlspecialchars($id) . '">';
        foreach ($options as $option => $value) {
            $this->form .= '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($value) . '</option>';
        }
        $this->form .= '</datalist>';
    }

    /**
     * @param string $label
     * @param string $type
     * @param string $name
     * @param string $id
     * @param string $class
     * @param string $value
     * @param string $accept
     * @param string $alt
     * @param string $autocomplete
     * @param string $autofocus
     * @param string $checked
     * @param string $files
     * @param string $form
     * @param string $height
     * @param string $list
     * @param string $max
     * @param string $maxlength
     * @param string $min
     * @param string $minlength
     * @param string $multiple
     * @param string $pattern
     * @param string $placeholder
     * @param string $size
     * @param string $src
     * @param string $step
     * @param string $title
     * @param string $width
     * @param array $options
     * @param string $aria_label
     * @param string $aria_describedby
     * @param string $aria_required
     * @param string $aria_invalid
     * @param string $aria_valuenow
     * @param string $aria_valuemin
     * @param string $aria_valuemax
     * @param string $aria_valuetext
     * @param string $extra_attributes
     * @param bool $required
     * @param bool $disabled
     * @param bool $readonly
     * @return void
     */
    public function addInput(
        string $label = '',
        string $type = 'text',
        string $name = '',
        string $id = '',
        string $class = '',
        string $value = '',
        string $accept = '',
        string $alt = '',
        string $autocomplete = '',
        string $autofocus = '',
        string $checked = '',
        string $files = '',
        string $form = '',
        string $height = '',
        string $list = '',
        string $max = '',
        string $maxlength = '',
        string $min = '',
        string $minlength = '',
        string $multiple = '',
        string $pattern = '',
        string $placeholder = '',
        string $size = '',
        string $src = '',
        string $step = '',
        string $title = '',
        string $width = '',
        array  $options = [],
        string $aria_label = '',
        string $aria_describedby = '',
        string $aria_required = '',
        string $aria_invalid = '',
        string $aria_valuenow = '',
        string $aria_valuemin = '',
        string $aria_valuemax = '',
        string $aria_valuetext = '',
        string $extra_attributes = '',
        bool   $required = false,
        bool   $disabled = false,
        bool   $readonly = false,
    ): void
    {
        switch ($type) {
            case 'button':
                $this->addInputRaw(
                    type: 'button',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    disabled: $disabled,
                );;
                break;
            case 'checkbox':
                $this->addInputRaw(
                    label: $label,
                    type: 'checkbox',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autofocus: $autofocus,
                    checked: $checked,
                    form: $form,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'color':
                $this->addInputRaw(
                    label: $label,
                    type: 'color',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    disabled: $disabled,
                );
                break;
            case 'date':
                $this->addInputRaw(
                    label: $label,
                    type: 'date',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    max: $max,
                    min: $min,
                    step: $step,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'datetime':
                $this->addInputRaw(
                    label: $label,
                    type: 'datetime',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    max: $max,
                    min: $min,
                    step: $step,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'datetime-local':
                $this->addInputRaw(
                    label: $label,
                    type: 'datetime-local',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    max: $max,
                    min: $min,
                    step: $step,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'email':
                $this->addInputRaw(
                    label: $label,
                    type: 'email',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    multiple: $multiple,
                    pattern: $pattern,
                    placeholder: $placeholder,
                    size: $size,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'file':
                $this->addInputRaw(
                    label: $label,
                    type: 'file',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    accept: $accept,
                    autofocus: $autofocus,
                    files: $files,
                    form: $form,
                    multiple: $multiple,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'hidden':
                $this->addInputRaw(
                    type: 'hidden',
                    name: $name,
                    value: $value,
                    form: $form,
                );
                break;
            case 'image':
                $this->addInputRaw(
                    label: $label,
                    type: 'image',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    alt: $alt,
                    autofocus: $autofocus,
                    form: $form,
                    height: $height,
                    src: $src,
                    width: $width,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'month':
                $this->addInputRaw(
                    label: $label,
                    type: 'month',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    max: $max,
                    min: $min,
                    step: $step,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'number':
                $this->addInputRaw(
                    label: $label,
                    type: 'number',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    max: $max,
                    min: $min,
                    placeholder: $placeholder,
                    step: $step,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'password':
                $this->addInputRaw(
                    label: $label,
                    type: 'password',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    pattern: $pattern,
                    placeholder: $placeholder,
                    size: $size,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'radio':
                $this->addInputRaw(
                    label: $label,
                    type: 'radio',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autofocus: $autofocus,
                    checked: $checked,
                    form: $form,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'range':
                $this->addInputRaw(
                    label: $label,
                    type: 'range',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    max: $max,
                    min: $min,
                    step: $step,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'reset':
                $this->addInputRaw(
                    type: 'reset',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autofocus: $autofocus,
                    form: $form,
                    disabled: $disabled,
                );
                break;
            case 'search':
                $this->addInputRaw(
                    label: $label,
                    type: 'search',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    pattern: $pattern,
                    placeholder: $placeholder,
                    size: $size,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'submit':
                $this->addInputRaw(
                    type: 'submit',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autofocus: $autofocus,
                    form: $form,
                    disabled: $disabled,
                );
                break;
            case 'tel':
                $this->addInputRaw(
                    label: $label,
                    type: 'tel',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    pattern: $pattern,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'text':
                $this->addInputRaw(
                    label: $label,
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    pattern: $pattern,
                    placeholder: $placeholder,
                    size: $size,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'time':
                $this->addInputRaw(
                    label: $label,
                    type: 'time',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    max: $max,
                    min: $min,
                    step: $step,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'url':
                $this->addInputRaw(
                    label: $label,
                    type: 'url',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    pattern: $pattern,
                    placeholder: $placeholder,
                    size: $size,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'week':
                $this->addInputRaw(
                    label: $label,
                    type: 'week',
                    name: $name,
                    id: $id,
                    class: $class,
                    value: $value,
                    autocomplete: $autocomplete,
                    autofocus: $autofocus,
                    form: $form,
                    list: $list,
                    max: $max,
                    min: $min,
                    step: $step,
                    required: $required,
                    disabled: $disabled,
                );
                break;
            case 'select':
                $this->addSelect(
                    label: $label,
                    name: $name,
                    id: $id,
                    class: $class,
                    options: $options,
                    selected: $value,
                    autofocus: $autofocus,
                    form: $form,
                    multiple: $multiple,
                    size: $size,
                    disabled: $disabled,
                    required: $required,
                );
            case 'datalist':
                $this->addDatalist(
                    id: $id,
                    options: $options,
                );
        }
    }

    public function addSelect(
        string $label = '',
        string $name = '',
        string $id = '',
        string $class = '',
        array  $options = [],
        string $selected = '',
        string $autofocus = '',
        string $form = '',
        string $multiple = '',
        string $size = '',
        bool   $disabled = false,
        bool   $required = false,
        string $aria_label = '',
        string $aria_describedby = '',
    ): void
    {
        if (!empty($label)) $this->form .= '<label for="' . htmlspecialchars($id) . '" class="form-label">' . htmlspecialchars($label) . '</label>';
        $selectClass = $this->addClass($class, 'form-select');
        $this->form .= '<select';
        if (!empty($name)) $this->form .= ' name="' . htmlspecialchars($name) . '"';
        if (!empty($id)) $this->form .= ' id="' . htmlspecialchars($id) . '"';
        if (!empty($selectClass)) $this->form .= ' class="' . htmlspecialchars(trim($selectClass)) . '"';
        if (!empty($autofocus)) $this->form .= ' autofocus="' . htmlspecialchars($autofocus) . '"';
        if (!empty($form)) $this->form .= ' form="' . htmlspecialchars($form) . '"';
        if (!empty($multiple)) $this->form .= ' multiple="' . htmlspecialchars($multiple) . '"';
        if (!empty($size)) $this->form .= ' size="' . htmlspecialchars($size) . '"';
        if (!empty($aria_label)) $this->form .= ' aria-label="' . htmlspecialchars($aria_label) . '"';
        if (!empty($aria_describedby)) $this->form .= ' aria-describedby="' . htmlspecialchars($aria_describedby) . '"';
        if ($required) $this->form .= ' required';
        if ($disabled) $this->form .= ' disabled';
        $this->form .= '>';

        foreach ($options as $option => $value) {
            if ($option == $selected) {
                $this->form .= '<option value="' . htmlspecialchars($option) . '" selected>' . htmlspecialchars($value) . '</option>';
            } else {
                $this->form .= '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($value) . '</option>';
            }
        }

        $this->form .= '</select>';
    }

    public function addStepsLine(int $steps = 1): void
    {
        $this->steps = $steps;
        $this->form .= '<div class="steps mb-3">';
        $this->form .= '<div class="step current"></div>';
        for ($i = 2; $i <= $steps; $i++) {
            $this->form .= '<div class="step"></div>';
        }
        $this->form .= '</div>';
    }

    public function closeForm(): void
    {
        $this->form .= '</form>';
    }

    public function startStep(
        string $id = '',
        string $class = '',
        bool $current = false,
        bool $display = true,
        int $data_step = 0,
    ): void
    {
        $this->form .= '<div class="step-content';
        $this->form .= $current ? ' current"' : '"';
        if (!empty($id)) $this->form .= ' id="' . htmlspecialchars($id) . '"';
        if (!empty($class)) $this->form .= ' class="' . htmlspecialchars($class) . '"';
        if (!$display) $this->form .= ' style="display: none;"';
        if ($data_step > 0) $this->form .= ' data-step="' . htmlspecialchars($data_step) . '"';
        $this->form .= '>';
    }

    public function addStepButtons(
        string $labelNext = 'Next',
        string $labelPrevious = 'Previous',
        string $class = 'btn btn-info',
        int $step = 1,
    ): void
    {
        $this->form .= '<div class="d-flex justify-content-between mt-3">';
        if ($step > 1) {
            $this->form .= '<a href="#" class="' . htmlspecialchars($class) . '" data-set-step="' . htmlspecialchars($step - 1) . '">' . htmlspecialchars($labelPrevious) . '</a>';
        }
        if ($step < $this->steps) {
            $this->form .= '<a href="#" class="' . htmlspecialchars($class) . '" data-set-step="' . htmlspecialchars($step + 1) . '">' . htmlspecialchars($labelNext) . '</a>';
        }
        $this->form .= '</div>';
    }

    public function stopStep(): void
    {
        $this->form .= '</div>'; // Close step-content
    }

    private function addInputRaw(
        string $label = '',
        string $type = 'text',
        string $name = '',
        string $id = '',
        string $class = '',
        string $value = '',
        string $accept = '',
        string $alt = '',
        string $autocomplete = '',
        string $autofocus = '',
        string $checked = '',
        string $files = '',
        string $form = '',
        string $height = '',
        string $list = '',
        string $max = '',
        string $maxlength = '',
        string $min = '',
        string $minlength = '',
        string $multiple = '',
        string $pattern = '',
        string $placeholder = '',
        string $size = '',
        string $src = '',
        string $step = '',
        string $title = '',
        string $width = '',
        string $aria_label = '',
        string $aria_describedby = '',
        string $aria_required = '',
        string $aria_invalid = '',
        string $aria_valuenow = '',
        string $aria_valuemin = '',
        string $aria_valuemax = '',
        string $aria_valuetext = '',
        string $extra_attributes = '',
        bool   $required = false,
        bool   $disabled = false,
        bool   $readonly = false,
    ): void
    {
        $isCheck = in_array($type, ['checkbox', 'radio']);
        $inputClass = $class;
        $labelClass = 'form-label';

        if ($isCheck) {
            $inputClass = $this->addClass($class, 'form-check-input');
            $labelClass = 'form-check-label';
            $this->form .= '<div class="form-check">';
        } elseif ($type === 'file') {
            $inputClass = $this->addClass($class, 'form-control');
        } elseif (in_array($type, ['submit', 'reset', 'button'])) {
            $inputClass = $this->addClass($class, 'btn btn-success');
        } elseif (in_array($type, ['reset'])) {
            $inputClass = $this->addClass($class, 'btn btn-danger');
        } else {
            $inputClass = $this->addClass($class, 'form-control');
        }

        // Label
        if (!empty($label)) {
            $for = !empty($id) ? $id : $name;
            $this->form .= '<label for="' . htmlspecialchars($for) . '" class="' . $labelClass . '">' . htmlspecialchars($label) . '</label>';
        }

        $this->form .= '<input type="' . htmlspecialchars($type) . '"';
        if (!empty($name)) $this->form .= ' name="' . htmlspecialchars($name) . '"';
        if (!empty($id)) $this->form .= ' id="' . htmlspecialchars($id) . '"';
        if (!empty($inputClass)) $this->form .= ' class="' . htmlspecialchars(trim($inputClass)) . '"';
        if (!empty($value)) $this->form .= ' value="' . htmlspecialchars($value) . '"';
        if (!empty($accept)) $this->form .= ' accept="' . htmlspecialchars($accept) . '"';
        if (!empty($alt)) $this->form .= ' alt="' . htmlspecialchars($alt) . '"';
        if (!empty($autocomplete)) $this->form .= ' autocomplete="' . htmlspecialchars($autocomplete) . '"';
        if (!empty($autofocus)) $this->form .= ' autofocus="' . htmlspecialchars($autofocus) . '"';
        if (!empty($checked)) $this->form .= ' checked="' . htmlspecialchars($checked) . '"';
        if (!empty($files)) $this->form .= ' files="' . htmlspecialchars($files) . '"';
        if (!empty($form)) $this->form .= ' form="' . htmlspecialchars($form) . '"';
        if (!empty($height)) $this->form .= ' height="' . htmlspecialchars($height) . '"';
        if (!empty($list)) $this->form .= ' list="' . htmlspecialchars($list) . '"';
        if (!empty($max)) $this->form .= ' max="' . htmlspecialchars($max) . '"';
        if (!empty($maxlength)) $this->form .= ' maxlength="' . htmlspecialchars($maxlength) . '"';
        if (!empty($min)) $this->form .= ' min="' . htmlspecialchars($min) . '"';
        if (!empty($minlength)) $this->form .= ' minlength="' . htmlspecialchars($minlength) . '"';
        if (!empty($multiple)) $this->form .= ' multiple="' . htmlspecialchars($multiple) . '"';
        if (!empty($pattern)) $this->form .= ' pattern="' . htmlspecialchars($pattern) . '"';
        if (!empty($placeholder)) $this->form .= ' placeholder="' . htmlspecialchars($placeholder) . '"';
        if (!empty($size)) $this->form .= ' size="' . htmlspecialchars($size) . '"';
        if (!empty($src)) $this->form .= ' src="' . htmlspecialchars($src) . '"';
        if (!empty($step)) $this->form .= ' step="' . htmlspecialchars($step) . '"';
        if (!empty($title)) $this->form .= ' title="' . htmlspecialchars($title) . '"';
        if (!empty($width)) $this->form .= ' width="' . htmlspecialchars($width) . '"';
        if (!empty($aria_label)) $this->form .= ' aria-label="' . htmlspecialchars($aria_label) . '"';
        if (!empty($aria_describedby)) $this->form .= ' aria-describedby="' . htmlspecialchars($aria_describedby) . '"';
        if (!empty($aria_required)) $this->form .= ' aria-required="' . htmlspecialchars($aria_required) . '"';
        if (!empty($aria_invalid)) $this->form .= ' aria-invalid="' . htmlspecialchars($aria_invalid) . '"';
        if (!empty($aria_valuenow)) $this->form .= ' aria-valuenow="' . htmlspecialchars($aria_valuenow) . '"';
        if (!empty($aria_valuemin)) $this->form .= ' aria-valuemin="' . htmlspecialchars($aria_valuemin) . '"';
        if (!empty($aria_valuemax)) $this->form .= ' aria-valuemax="' . htmlspecialchars($aria_valuemax) . '"';
        if (!empty($aria_valuetext)) $this->form .= ' aria-valuetext="' . htmlspecialchars($aria_valuetext) . '"';
        if (!empty($extra_attributes)) $this->form .= ' ' . htmlspecialchars($extra_attributes);
        if ($required) $this->form .= ' required';
        if ($disabled) $this->form .= ' disabled';
        if ($readonly) $this->form .= ' readonly';
        $this->form .= '>';

        if ($isCheck) {
            $this->form .= '</div>';
        }
    }

    /**
     * @return string
     */
    public function getForm(): string
    {
        return $this->form;
    }
}
