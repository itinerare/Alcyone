<?php

namespace App\Models;

class Report extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_id', 'email', 'status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reports';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the image associated with the report.
     */
    public function image() {
        return $this->hasOne(ImageUpload::class);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/
}
