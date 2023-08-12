<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components\Inputs;

use ReflectionParameter;

final class ToggleInputComponent extends BaseInputComponent
{
  public const DEFAULT_ACTIVE_COLOR = 'green';
  public const TOGGLE_STYLE_SQUARE = 'square';
  public const TOGGLE_STYLE_CIRCLE = 'circle';
  public const TOGGLE_STYLE_SWITCH = 'switch';

  private const DEFAULT_TOGGLE_STYLE = self::TOGGLE_STYLE_SQUARE;
  private const STATE_ICON_CIRCLE_OFF = 'fa-circle';
  private const STATE_ICON_CIRCLE_ON = 'fa-circle-check';
  private const STATE_ICON_SQUARE_OFF = 'fa-square';
  private const STATE_ICON_SQUARE_ON = 'fa-square-check';
  private const STATE_ICON_SWITCH_OFF = 'fa-toggle-off';
  private const STATE_ICON_SWITCH_ON = 'fa-toggle-on';

  private string $theme_color;
  private string $active_color;
  private string $off_icon;
  private string $on_icon;

  public function __construct(
    string $theme_color,
    string $active_color = self::DEFAULT_ACTIVE_COLOR,
    string $off_icon = self::STATE_ICON_SQUARE_OFF,
    string $on_icon = self::STATE_ICON_SQUARE_ON
  ) {
    $this->theme_color = $theme_color;
    $this->active_color = $active_color;
    $this->off_icon = $off_icon;
    $this->on_icon = $on_icon;
  }

  public static function create(
    string $theme_color,
    string $active_color = self::DEFAULT_ACTIVE_COLOR,
    string $style = self::DEFAULT_TOGGLE_STYLE
  ): ToggleInputComponent {
    switch ($style) {
      case self::TOGGLE_STYLE_SWITCH:
        return new ToggleInputComponent(
          $theme_color,
          $active_color,
          self::STATE_ICON_SWITCH_OFF,
          self::STATE_ICON_SWITCH_ON
        );
      case self::TOGGLE_STYLE_CIRCLE:
        return new ToggleInputComponent(
          $theme_color,
          $active_color,
          self::STATE_ICON_CIRCLE_OFF,
          self::STATE_ICON_CIRCLE_ON
        );
      default:
        return new ToggleInputComponent(
          $theme_color,
          $active_color,
          self::STATE_ICON_SQUARE_OFF,
          self::STATE_ICON_SQUARE_ON
        );
    }
  }

  public function render(ReflectionParameter $parameter): string
  {
    return <<<HTML
    <label
      tabindex='0'
      class='px-2 py-1 flex justify-between items-center gap-2 select-none rounded focus:outline-none focus:ring-4 focus:ring-$this->theme_color-700'
      onkeyup='event.keyCode === 32 && this.click()'
    >
      {$parameter->getName()}
      <input
        type='checkbox'
        {$this->resolveNameAttribute($parameter)}
        {$this->resolveCheckedAttribute($parameter)}
        class='hidden peer'
      >
      <i class='inline peer-checked:hidden text-xl text-$this->theme_color-600 fa-solid $this->off_icon'></i>
      <i class='hidden peer-checked:inline text-xl text-$this->active_color-500 fa-solid $this->on_icon'></i>
    </label>
    HTML;
  }

  public function getType(): string
  {
    return 'bool';
  }

  private function resolveCheckedAttribute(ReflectionParameter $parameter): string {
      return ($parameter->isDefaultValueAvailable() && $parameter->getDefaultValue()) ? 'checked' : '';
  }
}
