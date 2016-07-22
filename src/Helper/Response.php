<?php

namespace Slamp\Web\Helper;

interface Response {
    /**
     * @return int
     */
    public function getStatus(): int;
    /**
     * @return mixed
     */
    public function getData();
    /**
     * @return array
     */
    public function getLinks(): array;
}