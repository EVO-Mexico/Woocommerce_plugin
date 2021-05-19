<?php

namespace Payments;

abstract class Executable extends Configurable {

    public abstract function validate();

    public abstract function execute($callback = NULL, $result_from_prev = array());
}
