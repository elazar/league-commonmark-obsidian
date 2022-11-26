<?php

namespace Elazar\LeagueCommonMarkObsidian;

/**
 * This trait is used to simulate readonly properties until PHP 8.0 reaches EOL.
 * Classes using it should make any desired readonly properties private; this trait
 * will expose them for read access using __get().
 */
trait ReadonlyTrait
{
    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name, E_USER_WARNING);
    }
}