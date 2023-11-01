<?php

namespace App\Models\Report;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reporter extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'ip', 'is_banned',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reporters';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the reporter's name.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return ($this->is_banned ? '<strike>' : '').($this->email ?? 'Reporter #'.$this->id).($this->is_banned ? '</strike>' : '');
    }
}
