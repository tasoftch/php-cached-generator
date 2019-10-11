<?php
/**
 * Copyright (c) 2019 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace TASoft\Util;


use Generator;

class CachedGenerator
{
    private $generator;
    private $cache;

    /**
     * CachedGenerator constructor.
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Invoke the CachedGenerator object for iterations
     *
     * @return Generator
     */
    public function __invoke()
    {
        if(NULL === $this->cache) {
            $this->generator->rewind();

            goto iterate_over_generator;
        } elseif(isset($this->cache)) {
            foreach($this->cache as $key => $item) {
                yield $key => $item;
            }
            $this->generator->next();
            goto iterate_over_generator;
        } else {
            foreach($this->cache as $key => $item)
                yield $key => $item;
        }

        return;

        iterate_over_generator:

        while ($this->generator && $this->generator->valid()) {
            $k = $this->generator->key();
            $v = $this->generator->current();
            $this->cache[$k] = $v;

            yield $k => $v;

            $this->generator->next();
        }

        unset($this->generator);
    }
}