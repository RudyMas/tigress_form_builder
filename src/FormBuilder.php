<?php

namespace Tigress;

use Twig\Error\LoaderError;

/**
 * Class Menu (PHP version 8.4)
 *
 * @author Rudy Mas <rudy.mas@rudymas.be>
 * @copyright 2025 Rudy Mas (https://rudymas.be)
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
 * @version 2025.06.10.1
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
        return '2025.06.10';
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
