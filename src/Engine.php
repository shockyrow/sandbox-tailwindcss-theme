<?php

namespace Shockyrow\SandboxTailwindcssTheme;

use Shockyrow\Sandbox\EngineInterface;
use Shockyrow\Sandbox\Entities\Act;
use Shockyrow\Sandbox\Entities\ActList;
use Shockyrow\Sandbox\Entities\Call;
use Shockyrow\Sandbox\Entities\CallList;
use Shockyrow\Sandbox\Entities\CallRequest;
use Shockyrow\SandboxTailwindcssTheme\Components\ActComponent;
use Shockyrow\SandboxTailwindcssTheme\Components\CallComponentInterface;

final class Engine implements EngineInterface
{
    public const DEFAULT_THEME_COLOR = 'neutral';
    public const DEFAULT_CURSOR_COLOR = 'neutral';

    private ActComponent $act_component;
    private CallComponentInterface $call_component;
    private string $theme_color;
    private string $cursor_color;

    public function __construct(
        ActComponent $act_component,
        CallComponentInterface $call_component,
        string $theme_color = self::DEFAULT_THEME_COLOR,
        string $cursor_color = self::DEFAULT_CURSOR_COLOR
    ) {
        $this->act_component = $act_component;
        $this->call_component = $call_component;
        $this->theme_color = $theme_color;
        $this->cursor_color = $cursor_color;
    }

    public function getCallRequest(ActList $act_list): ?CallRequest
    {
        $act = $act_list->getOneByName($this->resolveActName());

        if ($act !== null) {
            $arguments = [];

            foreach ($this->resolveArguments() as $name => $argument) {
                $arguments[$name] = (string)$argument;
            }

            return new CallRequest($act, $arguments);
        }

        return null;
    }

    public function run(ActList $act_list, CallList $call_list): void
    {
        echo $this->render($act_list, $call_list);
    }

    private function resolveActName(): string
    {
        return (string)($_REQUEST['act'] ?? '');
    }

    private function resolveArguments(): array
    {
        return (array)($_REQUEST['arguments'] ?? []);
    }

    private function render(ActList $act_list, CallList $call_list): string
    {
        return <<<HTML
        <!doctype html>
        <html lang="en">
            {$this->renderHead()}
            {$this->renderBody($act_list, $call_list)}
        </html>
        HTML;
    }

    private function renderHead(): string
    {
        return <<<HTML
        <head>
          <meta charset='UTF-8'>
          <meta http-equiv='X-UA-Compatible' content='IE=edge'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <title>Sandbox</title>
          <script src='https://cdn.tailwindcss.com'></script>
          <script src='https://kit.fontawesome.com/2dfdd177f9.js' crossorigin='anonymous'></script>
          <style>
            *::-webkit-scrollbar {
              width: 6px;                /* width of the entire scrollbar */
            }

            *::-webkit-scrollbar-track {
              background: none;          /* color of the tracking area */
            }

            *::-webkit-scrollbar-thumb {
              background-color: #aaaaaa; /* color of the scroll thumb */
              border-radius: 20px;       /* roundness of the scroll thumb */
              border: none;              /* creates padding around scroll thumb */
            }

            body * {
              scrollbar-gutter: stable;
            }
          </style>
        </head>
        HTML;
    }

    public function renderBody(ActList $act_list, CallList $call_list): string
    {
        $acts_html = implode(
            array_map(
                fn(Act $act) => "<li id='{$act->getName()}'>{$this->act_component->render($act)}</li>",
                $act_list->getAll()
            )
        );

        $calls_html = implode(
            array_map(
                fn(Call $call) => "<li class='py-2'>{$this->call_component->renderHtml($call)}</li>",
                $call_list->getAll()
            )
        );

        $component_scripts = implode(
            PHP_EOL,
            [
                $this->act_component->renderJavaScript(),
                $this->call_component->renderJavaScript(),
            ]
        );

        return <<<HTML
        <body class='bg-$this->theme_color-900 text-$this->theme_color-100'>
          <div class='h-screen flex flex-col gap-4 container m-auto p-4'>
            <header class='flex justify-between items-center p-4 rounded-lg border border-$this->theme_color-700 bg-$this->theme_color-800'>
              <div><i class='fa-solid fa-signature'></i></div>
              <div>{$_SERVER['HTTP_HOST']}</div>
            </header>

            <main class='h-0 flex-grow flex gap-4'>
              <ul class='px-4 py-2 w-2/5 max-w-sm flex-shrink-0 overflow-y-auto'>
                $acts_html
              </ul>
              <ul class='overflow-y-auto flex-grow px-4'>
                $calls_html
              </ul>
            </main>
          </div>

          <script>
            {$component_scripts}
          </script>
        </body>
        HTML;
    }
}
