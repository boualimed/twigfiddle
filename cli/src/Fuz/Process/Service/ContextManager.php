<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Process\Service;

use Fuz\Framework\Base\BaseService;
use Fuz\Framework\Service\StringLoader;
use Fuz\Process\Agent\FiddleAgent;
use Fuz\Process\Entity\Error;
use Fuz\Process\Exception\StopExecutionException;

class ContextManager extends BaseService
{
    protected $stringLoader;

    public function __construct(StringLoader $stringLoader)
    {
        $this->stringLoader = $stringLoader;
    }

    public function extractContext(FiddleAgent $agent)
    {
        $fiddle = $agent->getFiddle();
        if (is_null($fiddle)) {
            throw new \LogicException('You should load a fiddle before trying to extract its context.');
        }

        $content = $fiddle->getContext()->getContent();
        $format  = $fiddle->getContext()->getFormat();

        if (strlen(str_replace([' ', "\n", "\r", "\t"], '', $content)) == 0) {
            $this->logger->debug('No context to extract.');

            return $this;
        }

        $this->logger->debug("Extracting Twig context from format: {$format}.");
        try {
            $array = $this->stringLoader->load($content, $format);
        } catch (\InvalidArgumentException $ex) {
            $agent->addError(Error::E_UNKNOWN_CONTEXT_FORMAT, ['format' => $format]);
            throw new StopExecutionException();
        } catch (\LogicException $ex) {
            $agent->addError(Error::E_UNEXPECTED, $ex);
            throw new StopExecutionException();
        } catch (\Exception $ex) {
            $agent->addError(Error::E_INVALID_CONTEXT_SYNTAX, $ex);
            throw new StopExecutionException();
        }

        if (!is_array($array)) {
            $agent->addError(Error::E_INVALID_CONTEXT_TYPE, ['context' => $array]);
            throw new StopExecutionException();
        }

        $this->logger->debug('Successfully extracted the context.', ['context' => $array]);
        $agent->setContext($array);

        return $this;
    }

    public function getContextFromAgent(FiddleAgent $agent)
    {
        $context = $agent->getContext();
        if (is_null($context)) {
            throw new \LogicException('Context has not been converted in this fiddle.');
        }

        return $context;
    }
}
