<?php

namespace App\Repositories\General;


use Illuminate\Support\Facades\Validator;

class RulesRepository
{
    public static function isArrayRole()
    {
        Validator::extend('array', function ($attribute, $value, $parameters) {
            return is_array($value);
        });
    }

    public static function countArray()
    {
        Validator::extend('countArray', function ($attribute, $value, $parameters) {
            return (count($value) === intval($parameters[0]));
        });
    }

    public static function countArrayEqualField()
    {
        Validator::extend('countArrayEqual', function ($attribute, $value, $parameters, $validator) {
            $count = data_get($validator->getData(), $parameters[0]);
            return (count($value) === intval($count));
        });
    }

    public static function countArrayBetween()
    {
        Validator::extend('countArrayBetween', function ($attribute, $value, $parameters, $validator) {
            return (isset($parameters[0]) && isset($parameters[1]) &&
                count($value) >= intval($parameters[0]) && count($value) <= intval($parameters[1]));
        });
    }

}