<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components;

use Shockyrow\Sandbox\Entities\Call;

final class ChatStyleCallComponent implements CallComponentInterface
{
  private string $theme_color;

  public function __construct(string $theme_color)
  {
    $this->theme_color = $theme_color;
  }

  public function renderHtml(Call $call): string
  {
    $sections = [];

    if ($call->hasOutput()) {
      $sections[] = $this->renderSection('Output', $call->getOutput());
    }

    if ($call->hasError()) {
      $sections[] = $this->renderSection('Error', var_export($call->getError(), true));
    }

    if ($call->hasException()) {
      $sections[] = $this->renderSection('Exception', var_export($call->getException(), true));
    }

    if ($call->hasValue()) {
      $sections[] = $this->renderSection('Value', $call->getValue());
    }

    $sections_html = implode($sections);

    return <<<HTML
    <article>
      $sections_html
    </article>
    HTML;
  }

  public function renderJavaScript(): string
  {
    return <<<JS
    console.log('Nice');
    JS;
  }

  private function renderSection(string $title, string $body): string
  {
    return <<<HTML
    <section class='flex justify-end'>
      <article class='w-2/3 rounded-lg border border-$this->theme_color-700 bg-$this->theme_color-800'>
        <header class='p-4'>
          <h1>$title</h1>
        </header>
        <section class='p-4'>
          <pre class='whitespace-pre-wrap break-words'>$body</pre>
        </section>
      </article>
    </section>
    HTML;
  }
}
