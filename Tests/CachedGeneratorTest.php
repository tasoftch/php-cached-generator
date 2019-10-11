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

/**
 * CachedGeneratorTest.php
 * php-utility
 *
 * Created on 2019-10-11 17:16 by thomas
 */

use TASoft\Util\CachedGenerator;
use PHPUnit\Framework\TestCase;

class CachedGeneratorTest extends TestCase
{
    private $iterations = 0;

    public function testCachedGenerator() {
        $cachedGenerator = new CachedGenerator( (function() {
            $this->iterations++;
            yield 1;
            $this->iterations++;
            yield 2;
            $this->iterations++;
            yield 3;
            $this->iterations++;
            yield 4;
            $this->iterations++;
            yield 5;
            $this->iterations++;
            yield 6;
        })() );

        foreach($cachedGenerator() as $item) {
            $this->assertEquals(1, $item);
            break;
        }
        $this->assertEquals(1, $this->iterations);

        $count = 1;
        foreach($cachedGenerator() as $item) {
            $this->assertEquals($count, $item);
            if($count++ > 5)
                break;
        }

        $this->assertEquals(6, $this->iterations);
    }
}
