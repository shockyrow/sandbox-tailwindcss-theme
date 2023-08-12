<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components;

use ReflectionParameter;
use Shockyrow\SandboxTailwindcssTheme\Components\Inputs\InputComponentInterface;

final class ParameterComponent
{
  private InputComponentInterface $default_input_component;
  /** @var InputComponentInterface[] */
  private array $input_components;

  /**
   * @param InputComponentInterface[] $input_components
   */
  public function __construct(
    InputComponentInterface $default_input_component,
    array $input_components
  ) {
    $this->default_input_component = $default_input_component;
    $this->input_components = [];

    foreach ($input_components as $input_component) {
      $this->input_components[$input_component->getType()] = $input_component;
    }
  }

  public function render(ReflectionParameter $parameter): string
  {
    $input_skin = $this->resolveInputSkin($parameter);

    return $input_skin->render($parameter);
  }

  private function resolveInputSkin(ReflectionParameter $parameter): InputComponentInterface
  {
    $type = $parameter->getType();

    if ($type === null) {
      return $this->default_input_component;
    }

    return $this->input_components[$type->getName()] ?? $this->default_input_component;
  }
}
