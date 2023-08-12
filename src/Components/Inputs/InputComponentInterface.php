<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components\Inputs;

use ReflectionParameter;

interface InputComponentInterface
{
  public function render(ReflectionParameter $parameter): string;

  public function getType(): string;
}
