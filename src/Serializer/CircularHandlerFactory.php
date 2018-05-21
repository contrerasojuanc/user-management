<?php

namespace App\Serializer;
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21/05/18
 * Time: 01:22 PM
 */
class CircularHandlerFactory
{
    /**
     * @return \Closure
     */
    public static function getId()
    {
        return function ($object) {
            return $object->getId();
        };
    }
}