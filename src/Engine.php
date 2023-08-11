<?php

namespace Shockyrow\SandboxTailwindcssTheme;

use Shockyrow\Sandbox\EngineInterface;
use Shockyrow\Sandbox\Entities\ActList;
use Shockyrow\Sandbox\Entities\CallList;
use Shockyrow\Sandbox\Entities\CallRequest;

final class Engine implements EngineInterface
{
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
        foreach ($act_list->getAll() as $act) {
            echo $act->getName() . '<br>';
        }

        foreach ($call_list->getAll() as $call) {
            $output = $call->getOutput() ?? var_export(null, true);
            $value_as_string = var_export($call->getValue(), true);
            $exception_as_string = var_export($call->getException(), true);
            $error_as_string = var_export($call->getError(), true);

            echo <<<HTML
            <div style="padding: 8px; border-radius:8px; border: 1px solid black;">
                <header>{$call->getRequest()->getAct()->getName()}</header>
                <pre>$output</pre>
                <pre>$value_as_string</pre>
                <pre>$exception_as_string</pre>
                <pre>$error_as_string</pre>
            </div>
            HTML;
        }
    }

    private function resolveActName(): string
    {
        return (string)($_REQUEST['act'] ?? '');
    }

    private function resolveArguments(): array
    {
        return (array)($_REQUEST['arguments'] ?? []);
    }
}
