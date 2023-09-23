<?php

namespace Waka\WformWidgets\Classes\Traits;



trait BlockTrait
{
    public function getBlockCode($field, $code) {
        return $this->{$field}->where('code', $code)->first();
    }

}