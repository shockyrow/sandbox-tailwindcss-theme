<?php

declare(strict_types=1);

namespace Shockyrow\SandboxTailwindcssTheme\Components;

use Shockyrow\Sandbox\Entities\Call;

interface CallComponentInterface
{
  public function renderHtml(Call $call): string;

  public function renderJavaScript(): string;
}
