<?php

namespace Tigress;

use Twig\Error\LoaderError;

/**
 * Class Menu (PHP version 8.5)
 *
 * @author Rudy Mas <rudy.mas@rudymas.be>
 * @copyright 2025 Rudy Mas (https://rudymas.be)
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @version 2025.12.09.0
 * @package Tigress\FormBuilder
 */
class FormBuilder
{
    /**
     * Get the version of the DisplayHelper
     *
     * @return string
     */
    public static function version(): string
    {
        return '2025.12.09';
    }

    /**
     * @throws LoaderError
     */
    public function __construct()
    {
        TWIG->addPath('vendor/tigress/form-builder/src/views');
    }

    /**
     * @return FormBuilder
     */
    public static function initiate(): FormBuilder
    {
        return new self();
    }
}
