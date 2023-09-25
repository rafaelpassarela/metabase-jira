<?php

namespace App\Models;

use App\Enums\PersonaTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuesPersonas extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'issue_id',
        'persona_id',
        'type'
    ];

        /**
     * Write code on Method
     *
     * @return response()
     */
    protected $casts = [
        'type' => PersonaTypeEnum::class
    ];
}
