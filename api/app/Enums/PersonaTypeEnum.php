<?php

namespace App\Enums;

// final class PersonaTypeEnum
// {
//     const ASSIGNEE = 'assignee';
//     const REPORTER = 'reporter';
//     const REVISOR  = 'revisor';
//     const CORESPONSAVEL = 'coresp';
// }

enum PersonaTypeEnum: string
{
    case ASSIGNEE = 'assignee';
    case REPORTER = 'reporter';
    case REVISOR  = 'revisor';
    case CORESPONSAVEL = 'coresp';
}

