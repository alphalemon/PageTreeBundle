<?php
/*
 * This file is part of the AlphaLemonPageTreeBundle and it is distributed
 * under the MIT License. In addiction, to use this bundle, you must leave
 * intact this copyright notice.
 *
 * Copyright (c) AlphaLemon <webmaster@alphalemon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://alphalemon.com
 * 
 * @license    MIT License
 * 
 */

namespace AlphaLemon\PageTreeBundle\Tests\Functional\AlphaLemon\PageTreeBundle\Core\Tools;

use AlphaLemon\PageTreeBundle\Tests\TestCase;
use AlphaLemon\PageTreeBundle\Core\Tools\AlToolkit;


class AlToolkitTest extends TestCase 
{    
    public function testCommandWithoutKernelAndCommandsDoesNotExecuteAnything()
    {
        $this->assertFalse(AlToolkit::executeCommand());
    }
    
    public function testCommandWithoutCommandsDoesNotExecuteAnything()
    {
        $this->assertFalse(AlToolkit::executeCommand($this->getContainer()->get('kernel')));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testCommandWithAFakeCommandsThrowsAnException()
    {
        $this->assertFalse(AlToolkit::executeCommand($this->getContainer()->get('kernel'), array('fake')));
    }
    
    public function testRetrieveBundleWebFolder()
    {
        $this->assertEquals('bundles/alphalemonthemeengine', AlToolkit::retrieveBundleWebFolder($this->getContainer(), 'ThemeEngineBundle'));
    }
    
    public function testLocateNotExistingResource()
    {
        $this->assertEmpty(AlToolkit::locateResource($this->getContainer(), 'AlphaLemonThemeEngineBundle/fake'));
    }
    
    public function testLocateResourceWithoutChiocciola()
    {
        $this->assertNotEmpty(AlToolkit::locateResource($this->getContainer(), 'AlphaLemonThemeEngineBundle'));
    }
    
    public function testLocateResourceWithChiocciola()
    {
        $this->assertNotEmpty(AlToolkit::locateResource($this->getContainer(), '@AlphaLemonThemeEngineBundle'));
    }
    
    public function testLocateResourceWhenKernelIsNotBooted()
    {
        $this->assertFalse(AlToolkit::locateResource($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'), '@AlphaLemonThemeEngineBundle'));
    }
    
    public function testNornmalizePath()
    {
        $this->assertEquals('c:/path/to/normalize', AlToolkit::normalizePath('c:\\path\\to\\normalize'));
    }
    
    public function testTruncateString()
    {
        $this->assertEquals('test', AlToolkit::truncateString('test'));
        $this->assertEquals(15, strlen(AlToolkit::truncateString('abcdefghilmnopqrstuvz1234567890', 15)));
    }
}