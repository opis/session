<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

class SessionTest extends TestCase
{
    public function testSessionId()
    {
        $session = new Session();
        $this->assertEquals('id_1', $session->id());
    }

    public function testHasMethod()
    {
        $session = new Session(['foo' => 'bar']);
        $this->assertTrue($session->has('foo'));
        $this->assertFalse($session->has('bar'));
    }

    public function testGetMethod()
    {
        $session = new Session(['foo' => 'bar']);
        $this->assertEquals('bar', $session->get('foo'));
        $this->assertNull($session->get('bar'));
        $this->assertEquals('BAR', $session->get('bar', 'BAR'));
    }

    public function testSetMethod()
    {
        $session = new Session();
        $this->assertFalse($session->has('foo'));
        $session->set('foo', 'bar');
        $this->assertTrue($session->has('foo'));
        $this->assertEquals('bar', $session->get('foo'));
    }

    public function testLoadMethod()
    {
        $session = new Session(['foo' => 'bar']);
        $cb = function() {
            return 'baz';
        };
        $this->assertEquals('bar', $session->load('foo', $cb));
        $this->assertFalse($session->has('bar'));
        $this->assertNull($session->get('bar'));
        $this->assertEquals('baz', $session->load('bar', $cb));
        $this->assertTrue($session->has('bar'));
        $this->assertEquals('baz', $session->get('bar'));
    }

    public function testDeleteMethod()
    {
        $session = new Session(['foo' => 'bar']);
        $this->assertTrue($session->has('foo'));
        $session->delete('foo');
        $this->assertFalse($session->has('foo'));
    }

    public function testFlashMethod()
    {
        $session = new Session();
        $this->assertInstanceOf(Flash::class, $session->flash());
    }

    public function testClearMethod()
    {
        $session = new Session(['foo' => true, 'bar' => true]);

        $this->assertEquals('id_1', $session->id());
        $this->assertTrue($session->has('foo'));
        $this->assertTrue($session->has('bar'));
        $session->clear();
        $this->assertEquals('id_1', $session->id());
        $this->assertFalse($session->has('foo'));
        $this->assertFalse($session->has('bar'));
    }

    public function testRegenerateMethod()
    {
        $session = new Session(['foo' => true, 'bar' => true]);

        $this->assertEquals('id_1', $session->id());
        $this->assertTrue($session->has('foo'));
        $this->assertTrue($session->has('bar'));
        $this->assertTrue($session->regenerate());
        $this->assertEquals('id_2', $session->id());
        $this->assertFalse($session->has('foo'));
        $this->assertFalse($session->has('bar'));
    }

    public function testRegenerateMethodKeep()
    {
        $session = new Session(['foo' => true, 'bar' => true]);

        $this->assertEquals('id_1', $session->id());
        $this->assertTrue($session->has('foo'));
        $this->assertTrue($session->has('bar'));
        $this->assertTrue($session->regenerate(true));
        $this->assertEquals('id_2', $session->id());
        $this->assertTrue($session->has('foo'));
        $this->assertTrue($session->has('bar'));
    }

    public function testDestroyMethod()
    {
        $session = new Session(['foo' => true, 'bar' => true]);

        $this->assertEquals('id_1', $session->id());
        $this->assertTrue($session->has('foo'));
        $this->assertTrue($session->has('bar'));
        $this->assertTrue($session->destroy());
        $this->assertEquals('id_0', $session->id());
        $this->assertFalse($session->has('foo'));
        $this->assertFalse($session->has('bar'));
    }
}