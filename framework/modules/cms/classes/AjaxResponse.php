<?php namespace Cms\Classes;

use Illuminate\Http\Response;
use October\Rain\Exception\ApplicationException;
use October\Rain\Exception\ValidationException;
use ArrayAccess;

/**
 * AjaxResponse
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class AjaxResponse extends Response implements ArrayAccess
{
    /**
     * @var array vars are variables included with the result
     */
    public $vars = [];

    /**
     * setHandlerVars
     */
    public function setHandlerVars($vars): static
    {
        $this->vars = (array) $vars;

        return $this;
    }

    /**
     * setContent captures the variables from a handler and merges any resulting data
     */
    public function setHandlerResponse($content): static
    {
        if (is_string($content)) {
            $content = ['result' => $content];
        }

        if (is_array($content)) {
            $this->vars = $content  + $this->vars;
        }

        $this->setContent([
            'data' => $this->vars
        ]);

        return $this;
    }

    /**
     * setException
     */
    public function setHandlerException($exception): static
    {
        $this->exception = $exception;

        $error = [];
        $error['message'] = $exception->getMessage();

        if ($exception instanceof ValidationException) {
            $this->setStatusCode(422);
            $error['fields'] = $exception->getFields();
        }
        elseif ($exception instanceof ApplicationException) {
            $this->setStatusCode(400);
        }
        else {
            $this->setStatusCode(500);
        }

        $this->setContent([
            'error' => $error
        ]);

        return $this;
    }

    /**
     * offsetExists implementation
     */
    public function offsetExists($offset): bool
    {
        return isset($this->original[$offset]);
    }

    /**
     * offsetSet implementation
     */
    public function offsetSet($offset, $value): void
    {
        $this->original[$offset] = $value;
    }

    /**
     * offsetUnset implementation
     */
    public function offsetUnset($offset): void
    {
        unset($this->original[$offset]);
    }

    /**
     * offsetGet implementation
     */
    public function offsetGet($offset): mixed
    {
        return $this->original[$offset] ?? null;
    }
}
