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

use Opis\Session\{Handlers\File, SessionHandler};
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /** @var SessionHandler */
    protected static $handler;

    public static function setUpBeforeClass(): void
    {
        static::$handler = $h = new File(__DIR__ . DIRECTORY_SEPARATOR . 'sessions');
        $h->open('PHPSESSIONID');
    }

    public static function tearDownAfterClass(): void
    {
        $rrmdir = function($dir) use(&$rrmdir) {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (is_dir($dir . DIRECTORY_SEPARATOR . $object)) {
                            $rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                        } else {
                            unlink   ($dir . DIRECTORY_SEPARATOR . $object);
                        }
                    }
                }
                reset($objects);
                rmdir($dir);
            }
        };

        $rrmdir(__DIR__ . DIRECTORY_SEPARATOR . 'sessions');
    }

    public function testCreateSession()
    {
        $h = static::$handler;
        $session = $h->create('id_1', time() - 3600);
        $this->assertNotNull($session);
        $this->assertEquals($session->id(), 'id_1');
    }

    public function testReadSession()
    {
        $h = static::$handler;
        $session = $h->read('id_1');
        $this->assertNotNull($session);
        $this->assertEquals($session->id(), 'id_1');
        $this->assertNull($h->read('unknown_id'));
    }

    public function testGC()
    {
        $h = static::$handler;
        $h->create('id_2', time() + 3600);
        $h->gc(0);
        $this->assertNull($h->read('id_1'));
        $this->assertNotNull($h->read('id_2'));
    }

    public function testUpdate()
    {
        $h = static::$handler;
        $s = $h->read('id_2');
        $this->assertNotEquals(100, $s->expiresAt());

        $s->setExpirationDate(100);
        $h->update($s);
        $s = $h->read('id_2');

        $this->assertEquals(100, $s->expiresAt());
    }
}