<?php

namespace App;

/**
 *
 * @author tibo
 */
interface HasStatus
{
    public function status() : Status;
}
