<?php

namespace Shockyrow\SandboxTailwindcssTheme\Components\Inputs;

use ReflectionParameter;

abstract class BaseInputComponent implements InputComponentInterface
{
    protected function resolveNameAttribute(ReflectionParameter $parameter): string
    {
        return "name='arguments[{$parameter->getName()}]'";
    }
}
