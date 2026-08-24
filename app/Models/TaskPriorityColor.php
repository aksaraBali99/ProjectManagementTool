<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['priority', 'background_color', 'text_color'])]
class TaskPriorityColor extends Model
{
    //
}
