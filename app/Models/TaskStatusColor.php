<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['status', 'background_color', 'text_color'])]
class TaskStatusColor extends Model
{
    //
}
