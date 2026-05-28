<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class Permission extends Model
{
    protected $table = 'permissions';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'description',
    ];

}
