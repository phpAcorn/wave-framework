<?php
/**
 * @author Dimitar
 * @copyright 2015 Dimitar Dimitrov <daghostman.dimitrov@gmail.com>
 * @package codewave
 * @license MIT
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Wave\Framework\Application;

use Go\Aop\Aspect;
use Go\Core\AspectContainer;
use Go\Core\AspectKernel;

/**
 * Class AspectsKernel
 * @package Wave\Framework\Application
 */
class AspectsKernel extends AspectKernel
{

    /**
     * Registers an aspect to the aspects kernel
     * @param Aspect $aspect
     * @return $this
     */
    public function addAspect(Aspect $aspect)
    {
        if ($this->container !== null) {
            $this->container->registerAspect($aspect);
        }

        return $this;
    }

    /**
     * Configure an AspectContainer with advisers, aspects and pointcuts
     *
     * @param AspectContainer $container
     *
     * @return void
     */
    protected function configureAop(AspectContainer $container)
    {
        // Implements required abstract method
        /*
         * Prior to version 3 there will be some Aspects which
         * will be loaded for every application, if `aspects`
         * option is provided upon object creation.
         */
    }
}
