<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait HasActivityLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(class_basename($this))
            ->logOnlyDirty()
            ->logAll()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Crear',
                'updated' => 'Editar',
                'deleted' => 'Eliminar',
                default => $eventName,
            });
    }
}