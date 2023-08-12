<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components;

use Shockyrow\Sandbox\Entities\Argument;
use Shockyrow\Sandbox\Entities\Call;

final class BlockStyleCallComponent implements CallComponentInterface
{
  private const ACTIVE_TAB_IDENTIFIER_CLASS = 'text-white';
  private const DEFAULT_TAB_CLASSES = [
    "p-1",
    "rounded-lg",
    "font-bold",
    "text-xs",
    "hover:text-white",
    "focus:outline-0",
    "focus:text-white",
  ];
  private const ACTIVE_TAB_CLASSES = [self::ACTIVE_TAB_IDENTIFIER_CLASS];
  private const INACTIVE_TAB_CLASSES = [];
  private const DEFAULT_TAB_BODY_CLASSES = [
    "p-4",
    "whitespace-pre-wrap",
    "break-words",
    "rounded-b-lg",
    "select-all",
  ];
  private const ACTIVE_TAB_BODY_CLASSES = [];
  private const INACTIVE_TAB_BODY_CLASSES = ["hidden"];

  private string $theme_color;

  public function __construct(string $theme_color)
  {
    $this->theme_color = $theme_color;
  }

  public function renderHtml(Call $call): string
  {
    $called_at = date('d M Y @ H:i', $call->getCalledAt());

    $crap_list = $this->resolveDataList($call);
    $last_crap_key = array_key_last($crap_list);
    $tabs = [];
    $data_list = [];

    foreach ($crap_list as $key => $data) {
      $data_id = $call->getCalledAt() . $key . rand();
      $is_active = $key === $last_crap_key . 'a';
      $tab_body_classes = implode(' ', $this->resolveTabBodyClasses($is_active));
      $tab_classes = implode(' ', $this->resolveTabClasses($is_active));

      $data_list[] = <<<HTML
      <pre id='$data_id' class='$tab_body_classes'>$data</pre>
      HTML;

      $tabs[] = <<<HTML
      <button data-target='$data_id' onclick='selectTab(this)' class='$tab_classes'>$key</button>
      HTML;
    }

    $tabs_html = '';
    $data_list_html = '';

    if (count($tabs) > 0) {
      $tabs_html = sprintf("<section class='flex flex-wrap items-center gap-2'>%s</section>", implode($tabs));
      $data_list_html = sprintf("<section>%s</section>", implode($data_list));
    }

    $status_icon = 'circle-check';
    $status_color = 'green-400';

    if ($call->hasException() || $call->hasError()) {
      $status_icon = 'circle-xmark';
      $status_color = 'red-600';
    }

    return <<<HTML
    <article class='group rounded-lg border border-$this->theme_color-700 bg-$this->theme_color-800 hover:ring hover:ring-$status_color'>
      <header class='p-4 flex flex-col justify-between gap-2'>
        <div class='flex items-center gap-2'>
          <i class='p-2 flex justify-center items-center fa-solid fa-$status_icon text-$status_color'></i>
          <div class='flex-grow font-mono'>
            <h1 class='break-all text-$this->theme_color-300'>
              {$call->getRequest()->getAct()->getName()}
            </h1>
            <h2 class='text-xs text-$this->theme_color-500'>$called_at</h2>
          </div>
          <section class='hidden group-hover:flex flex-shrink-0 items-center text-sm'>
            <button class='w-6 h-6 text-$this->theme_color-500 hover:text-white'>
              <i class='fa-solid fa-rotate-right'></i>
            </button>
            <button class='w-6 h-6 text-$this->theme_color-500 hover:text-sky-500'>
              <i class='fa-solid fa-arrow-up-right-from-square'></i>
            </button>
            <button class='w-6 h-6 text-$this->theme_color-500 hover:text-yellow-500'>
              <i class='fa-solid fa-star'></i>
            </button>
          </section>
        </div>
        $tabs_html
      </header>
      $data_list_html
    </article>
    HTML;
  }

  public function renderJavaScript(): string
  {
    $active_tab_identifier_class = self::ACTIVE_TAB_IDENTIFIER_CLASS;

    return <<<JS
    function selectTab(tab) {
      let target_id = tab.dataset.target;
      let target = document.getElementById(target_id);

      if (tab.classList.contains('$active_tab_identifier_class')) {
        tab.classList.remove('$active_tab_identifier_class');
        tab.classList.add('text-$this->theme_color-500');
        target.classList.add('hidden');
        
        return;
      }
      
      for (let child of tab.parentElement.children) {
        child.classList.add('text-$this->theme_color-500');
        child.classList.remove('$active_tab_identifier_class');
      }
      
      tab.classList.add('$active_tab_identifier_class');
      tab.classList.remove('text-$this->theme_color-500');
      
      for (let child of target.parentElement.children) {
        child.classList.add('hidden');
      }
      
      target.classList.remove('hidden');
    }
    JS;
  }

  /**
   * @param Call $call
   * @return string[]
   */
  private function resolveDataList(Call $call): array
  {
    return array_map(
      fn($value) => is_string($value) ? $value : var_export($value, true),
      array_filter([
        'Arguments' => array_map(fn(string $argument) => $argument, $call->getRequest()->getArguments()),
        'Output' => $call->getOutput(),
        'Value' => $call->getValue(),
        'Error' => $call->getError(),
        'Exception' => $call->getException(),
      ])
    );
  }

  private function resolveTabClasses(bool $is_active = false): array
  {
    return array_merge(
      self::DEFAULT_TAB_CLASSES,
      $is_active ? self::ACTIVE_TAB_CLASSES : self::INACTIVE_TAB_CLASSES,
      $is_active ? [] : ["text-$this->theme_color-500"]
    );
  }

  private function resolveTabBodyClasses(bool $is_active = false): array
  {
    return array_merge(
      self::DEFAULT_TAB_BODY_CLASSES,
      $is_active ? self::ACTIVE_TAB_BODY_CLASSES : self::INACTIVE_TAB_BODY_CLASSES,
      ["bg-$this->theme_color-900"]
    );
  }
}
