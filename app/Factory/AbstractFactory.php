<?php

namespace App\Factory;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractFactory
{
    protected static function createModel(Model $model, array $attributes): Model
    {
        foreach ($attributes as $key => $value) {
            $model->{$key} = $value;
        }

        return $model;
    }
}
