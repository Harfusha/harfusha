<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;

abstract class AppController extends Controller
{
    /**
     * Initialize controller components and request services.
     */
    public function initialize(): void
    {
        parent::initialize();
    }
}
