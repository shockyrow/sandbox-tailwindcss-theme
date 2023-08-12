<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Services;

use ReflectionNamedType;

final class NamedTypePatternResolver
{
  public function resolve(ReflectionNamedType $named_type): ?string
  {
    switch ($named_type->getName()) {
      case 'int':
        return '([\+\-]?\d+)' . ($named_type->allowsNull() ? '?' : '');
      case 'float':
        return '([\+\-]?(\d*[.])?\d+)' . ($named_type->allowsNull() ? '?' : '');
      default:
        return null;
    }
  }
}
