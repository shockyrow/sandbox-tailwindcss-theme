<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components;

use Shockyrow\Sandbox\Entities\Act;

final class ActComponent
{
  private ActDialogComponent $act_dialog_component;
  private string $theme_color;

  public function __construct(
    ActDialogComponent $act_dialog_component,
    string $theme_color
  ) {
    $this->act_dialog_component = $act_dialog_component;
    $this->theme_color = $theme_color;
  }

  public function render(Act $act): string
  {
    return <<<HTML
    <div class='flex flex-col'>
      <button
        onclick='openSiblingDialog(event)'
        class='p-2 text-$this->theme_color-500 hover:text-$this->theme_color-50 focus:text-$this->theme_color-50 focus:outline-none break-all text-left'
      >
        {$act->getName()}
      </button>
      {$this->act_dialog_component->render($act)}
    </div>
    HTML;
  }

  public function renderJavaScript(): string
  {
    return <<<JS
    function openSiblingDialog(event) {
      let dialog = event.target.nextElementSibling;
      let act_input = dialog.querySelector('#act'); 

      dialog.showModal();
      act_input.nextElementSibling.focus();
    }

    {$this->act_dialog_component->renderJavaScript()}
    JS;
  }
}
