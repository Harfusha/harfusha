<?php
declare(strict_types=1);

namespace App\Domain;

enum ProjectStatus: string
{
    case Active = 'active';
    case Beta = 'beta';
    case Prototype = 'prototype';
    case Proposal = 'proposal';
    case Record = 'record';
    case Maintenance = 'maintenance';
    case Archived = 'archived';
}
