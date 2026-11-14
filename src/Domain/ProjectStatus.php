<?php
declare(strict_types=1);
namespace App\Domain;

enum ProjectStatus: string
{
    case Active = 'active';
    case Prototype = 'prototype';
    case Maintenance = 'maintenance';
    case Archived = 'archived';
}
