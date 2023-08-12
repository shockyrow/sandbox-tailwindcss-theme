<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components\Inputs;

use ReflectionParameter;
use Shockyrow\SandboxTailwindcssTheme\Services\NamedTypePatternResolver;

final class TextInputComponent extends BaseInputComponent
{
  private NamedTypePatternResolver $pattern_resolver;
  private string $theme_color;

  public function __construct(
    NamedTypePatternResolver $pattern_resolver,
    string $theme_color
  ) {
    $this->pattern_resolver = $pattern_resolver;
    $this->theme_color = $theme_color;
  }

  public function render(ReflectionParameter $parameter): string
  {
    return <<<HTML
    <label class='flex flex-col gap-1'>
      <span class='px-2'>{$parameter->getName()}</span>
      <input
        {$this->resolveNameAttribute($parameter)}
        {$this->resolveAttributes($parameter)}
        class='w-full px-2 py-1 rounded bg-$this->theme_color-900 invalid:bg-red-800 text-$this->theme_color-50 placeholder:text-$this->theme_color-700 placeholder:invalid:text-$this->theme_color-800 focus:outline-none focus:ring-4 focus:ring-$this->theme_color-700 focus:invalid:ring-red-700'
      >
    </label>
    HTML;
  }

  public function getType(): string
  {
    return 'string';
  }

  private function resolveAttributes(ReflectionParameter $parameter): string
  {
    return implode(
      ' ',
      array_filter([
        $this->resolvePlaceholderAttribute($parameter),
        $this->resolveValueAttribute($parameter),
        $this->resolvePatternAttribute($parameter),
        $this->resolveRequiredAttribute($parameter),
      ])
    );
  }

  private function resolvePlaceholderAttribute(ReflectionParameter $parameter): string
  {
    $type = $parameter->getType();
    $type_name = $type ? $type->getName() : 'mixed';

    return "placeholder='$type_name'";
  }

  private function resolveValueAttribute(ReflectionParameter $parameter): string
  {
    if ($parameter->isDefaultValueAvailable() === false) {
      return '';
    }

    $value = $parameter->getDefaultValue() ?? '';

    return "value='$value'";
  }

  private function resolvePatternAttribute(ReflectionParameter $parameter): string
  {
    $pattern = $this->resolvePattern($parameter);

    if ($pattern === null) {
      return '';
    }

    return "pattern='$pattern'";
  }

  private function resolvePattern(ReflectionParameter $parameter): ?string
  {
    $named_type = $parameter->getType();

    if ($named_type === null) {
      return null;
    }

    return $this->pattern_resolver->resolve($named_type);
  }

  private function resolveRequiredAttribute(ReflectionParameter $parameter): string
  {
    if ($parameter->isOptional() || $parameter->allowsNull()) {
      return '';
    }

    return 'required';
  }
}
