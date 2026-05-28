<?php

namespace App\Models;

use App\Traits\BelongsToEtablissement;
use App\Traits\UsesUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $utilisateur_id
 * @property string $etablissement_id
 * @property string|null $fonction
 * @property string|null $date_prise_fonction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Utilisateur $utilisateur
 * @property-read Etablissement $etablissement
 */
class PersonnelAdministratif extends Model
{
    use BelongsToEtablissement, UsesUuidAsPrimaryKey;

    public $timestamps = false;

    protected $fillable = [
        'utilisateur_id', 'etablissement_id', 'fonction', 'date_prise_fonction',
    ];

    protected function casts(): array
    {
        return [
            'date_prise_fonction' => 'date:Y-m-d',
        ];
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
