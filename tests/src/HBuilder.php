<?php

namespace DeimosTest;

use Deimos\Builder\Builder;
use Deimos\Helper\Helper;

class HBuilder extends Builder
{

    public function helper()
    {
        return $this->once(function () {
            return new Helper($this);
        }, __METHOD__);
    }

}
