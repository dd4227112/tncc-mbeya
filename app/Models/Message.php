<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['phone', 'body', 'status', 'reference', 'response'])]
class Message extends Model {
    
}
