<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Session\Test;

use Opis\Session\Flash;
use PHPUnit\Framework\TestCase;

class FlashTest extends TestCase
{
    public function testHasMethod()
    {
        $flash = new Flash(['foo' => 'bar']);
        $this->assertTrue($flash->has('foo'));
        $this->assertFalse($flash->has('bar'));
    }

    public function testGetMethod()
    {
        $flash = new Flash(['foo' => 'bar']);
        $this->assertEquals('bar', $flash->get('foo'));
        $this->assertNull($flash->get('bar'));
        $this->assertEquals('baz', $flash->get('bar', 'baz'));
    }

    public function testSetMethod()
    {
        $flash = new Flash();
        $this->assertFalse($flash->has('foo'));
        $this->assertNull($flash->get('foo'));
        $flash->set('foo', 'bar');
        $this->assertTrue($flash->has('foo'));
        $this->assertEquals('bar', $flash->get('foo'));
    }

    public function testDeleteMethod()
    {
        $flash = new Flash(['foo' => 'bar']);
        $this->assertTrue($flash->has('foo'));
        $flash->delete('foo');
        $this->assertFalse($flash->has('foo'));
        $flash->set('foo', 'bar');
        $this->assertTrue($flash->has('foo'));
        $flash->delete('foo');
        $this->assertFalse($flash->has('foo'));
    }

    public function testLoadMethod()
    {
        $cb = function () {
            return 'baz';
        };

        $flash = new Flash(['foo' => 'bar']);
        $this->assertTrue($flash->has('foo'));
        $this->assertEquals('bar', $flash->get('foo'));
        $this->assertFalse($flash->has('bar'));
        $this->assertNull($flash->get('bar'));
        $this->assertEquals('baz', $flash->load('bar', $cb));
        $this->assertTrue($flash->has('bar'));
        $this->assertEquals('baz', $flash->get('bar'));
    }

    public function testToArrayMethod()
    {
        $flash = new Flash(['foo' => 'bar']);

        $this->assertTrue($flash->has('foo'));
        $this->assertEmpty($flash->toArray());
        $flash->set('bar', 'baz');
        $this->assertEquals(['bar' => 'baz'], $flash->toArray());
        $flash->set('foo', $flash->get('foo'));
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $flash->toArray());
    }

    public function testClearMethod()
    {
        $flash = new Flash(['foo' => 'bar']);
        $this->assertTrue($flash->has('foo'));
        $this->assertEmpty($flash->toArray());
        $flash->set('bar', 'baz');
        $this->assertEquals(['bar' => 'baz'], $flash->toArray());
        $flash->clear();
        $this->assertEmpty($flash->toArray());
        $this->assertFalse($flash->has('foo'));
        $flash->clear(['a' => 'b']);
    }

    public function testClearMethodWithArray()
    {
        $flash = new Flash(['foo' => 'bar']);
        $this->assertTrue($flash->has('foo'));
        $this->assertEmpty($flash->toArray());
        $flash->set('bar', 'baz');
        $this->assertEquals(['bar' => 'baz'], $flash->toArray());
        $flash->clear(['a' => 'b']);
        $this->assertEquals(['a' => 'b'], $flash->toArray());
        $this->assertFalse($flash->has('foo'));
    }

    public function testReflashMethod()
    {
        $flash = new Flash(['foo' => 'bar']);

        $this->assertEmpty($flash->toArray());
        $flash->reflash();
        $this->assertEquals(['foo' => 'bar'], $flash->toArray());

        $flash = new Flash(['foo' => 'bar', 'bar' => 'baz']);
        $this->assertEmpty($flash->toArray());
        $flash->reflash(['bar']);
        $this->assertEquals(['bar' => 'baz'], $flash->toArray());
    }
}