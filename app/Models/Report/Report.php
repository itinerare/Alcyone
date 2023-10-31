<?php

namespace App\Models\Report;

use App\Models\ImageUpload;
use App\Models\Model;
use App\Models\User\User;

class Report extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_upload_id', 'reporter_id', 'key', 'reason', 'status', 'staff_comments', 'staff_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reports';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'reporter', 'image', 'staff',
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for images.
     *
     * @var array
     */
    public static $createRules = [
        //
        'image_url' => 'required',
        'reason'    => 'required',
        'email'     => 'nullable|email',
        'agreement' => 'required|accepted',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the reporter associated with the report.
     */
    public function reporter() {
        return $this->belongsTo(Reporter::class);
    }

    /**
     * Get the image associated with the report.
     */
    public function image() {
        return $this->belongsTo(ImageUpload::class, 'image_upload_id')->withTrashed()->with('user');
    }

    /**
     * Get the staff associated with the report.
     */
    public function staff() {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the report's URL.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('reports/'.$this->key);
    }

    /**
     * Gets the URL for editing the report in the admin panel.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/reports/'.$this->id);
    }
}
