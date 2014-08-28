<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2001-2014, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @author     Jeroen Versteeg <jversteeg@gmail.com>
 * @copyright  2001-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.7.30
 */

/**
 *
 *
 * @package    PHPUnit
 * @author     Márcio Almada <marcio3w@gmail.com>
 * @copyright  2001-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 4.0.20
 * @covers     PHPUnit_Framework_Constraint_ExceptionMessage
 */
class ExceptionMessageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage A literal exception message
     */
    public function testLiteralMessage()
    {
        throw new Exception("A literal exception message");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage A partial
     */
    public function testPatialMessageBegin()
    {
        throw new Exception("A partial exception message");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage partial exception
     */
    public function testPatialMessageMiddle()
    {
        throw new Exception("A partial exception message");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage exception message
     */
    public function testPatialMessageEnd()
    {
        throw new Exception("A partial exception message");
    }

    /**
     * @runInSeparateProcess
     * @requires extension xdebug
     * @expectedException \Exception
     * @expectedExceptionMessage Screaming preg_match
     */
    public function testMessageWithXdebugScreamOn()
    {
        ini_set('xdebug.scream', '1');
        throw new Exception("Screaming preg_match");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage /^A polymorphic \w+ message/
     */
    public function testRegexMessage()
    {
        throw new Exception("A polymorphic exception message");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage /^a poly[a-z]+ [a-zA-Z0-9_]+ me(s){2}age$/i
     */
    public function testRegexMessageExtreme()
    {
        throw new Exception("A polymorphic exception message");
    }
}
