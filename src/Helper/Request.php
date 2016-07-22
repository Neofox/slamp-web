<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/07/2016
 * Time: 14:18
 */
namespace Slamp\Web\Helper;

use stdClass;

interface Request
{
    public function getUri(): string;

    public function getArgs(): stdClass;

    public function getPayload();
}