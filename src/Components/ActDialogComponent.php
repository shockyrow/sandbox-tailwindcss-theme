<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components;

use ReflectionParameter;
use Shockyrow\Sandbox\EngineInterface;
use Shockyrow\Sandbox\Entities\Act;

final class ActDialogComponent
{
  private ParameterComponent $parameter_component;
  private string $theme_color;

  public function __construct(
    ParameterComponent $parameter_component,
    string $theme_color
  ) {
    $this->parameter_component = $parameter_component;
    $this->theme_color = $theme_color;
  }

  public function render(Act $act): string
  {
    return <<<HTML
    <dialog class='w-full max-w-sm backdrop:backdrop-blur-sm backdrop:bg-$this->theme_color-900/50 bg-transparent'>
      <article class='p-4 flex flex-col gap-2 shadow-lg rounded-lg border border-$this->theme_color-700 bg-$this->theme_color-800 text-$this->theme_color-400'>
        <header class='flex justify-between gap-4'>
          <h1 class='flex-grow flex items-center gap-2 text-$this->theme_color-300 font-bold break-all'>
            <i class='p-2 fa-solid fa-bolt'></i>
            {$act->getName()}
          </h1>
          <button onclick='dismissClosestDialog(event)' class='w-8 h-8 flex-shrink-0 rounded select-none focus:text-$this->theme_color-200 focus:outline-none hover:text-$this->theme_color-200 active:text-$this->theme_color-500'>
            <i class='fa-solid fa-xmark'></i>
          </button>
        </header>
        <form method='post' onkeyup='dismissClosestDialog(event)' class='flex flex-col gap-4 font-mono text-lg'>
          <input id='act' name='act' type='hidden' value='{$act->getName()}'>
          {$this->renderParameters($act->getFunction()->getParameters())}
          <button class='p-2 flex justify-center items-center gap-1 select-none rounded bg-$this->theme_color-700 hover:bg-$this->theme_color-600 focus:bg-$this->theme_color-600 focus:outline-none focus:ring-4 focus:ring-$this->theme_color-700 active:bg-$this->theme_color-900'>
            Call
          </button>
        </form>
      </article>
    </dialog>
    HTML;
  }

  public function renderJavaScript(): string
  {
    return <<<JS
    function dismissClosestDialog(event) {
      if (event instanceof KeyboardEvent && event.code !== 'Escape') {
        return;
      }
      
      event.target.closest('dialog').close();
    }
    JS;
  }

  /**
   * @param ReflectionParameter[] $parameters
   */
  private function renderParameters(array $parameters): string
  {
    return implode(
      array_map(
        fn(ReflectionParameter $parameter) => $this->parameter_component->render($parameter),
        $parameters
      )
    );
  }
}
