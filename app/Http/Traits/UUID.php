<?php

namespace App\Http\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait UUID
{
    public static function boot()
    {
        // Boot other traits on the Model
        parent::boot();

        /**
         * Listen for the creating event on the user model.
         * Sets the 'id' to a UUID using Str::uuid() on the instance being created
         */
        static::creating(function ($model) {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());
            }

            // Set created_at and created_by value
            $model->created_at = Carbon::now();
            $model->created_by = auth()->user() ? auth()->user()->id : NULL;
        });

        // create a event to happen on updating
        static::updating(function ($model) {

            // Set updated_at and updated_by value
            $model->updated_at = Carbon::now();
            $model->updated_by = auth()->user() ? auth()->user()->id : NULL;
        });

        static::deleting(function ($model) {

            // Set updated_at and updated_by value
            $model->deleted_at = Carbon::now();
            $model->deleted_by = auth()->user() ? auth()->user()->id : NULL;
        });
    }

    // Tells the database not to auto-increment this field
    public function getIncrementing()
    {
        return false;
    }

    // Helps the application specify the field type in the database
    public function getKeyType()
    {
        return 'string';
    }
}
