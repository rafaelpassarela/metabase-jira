<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issues extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'keyJira',
        'summary',
        'issueType',
        'storyPoints',
        'classe',
        'tema',
        'subTema',
        'areaDemandante',
        'sprintId',
        'sprintName',
        'status',
        'priority',
        'url',
        'parentKey',
        'parentUrl',
        'resolution',
        'resolvedAt',
        'lastUpdated',
        'filterId'
    ];

}