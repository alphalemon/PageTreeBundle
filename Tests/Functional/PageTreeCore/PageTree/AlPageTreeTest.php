<?php
/*
 * This file is part of the AlphaLemonPageTreeBundle and it is distributed
 * under the MIT License. In addiction, to use this bundle, you must leave
 * intact this copyright notice.
 *
 * (c) Since 2011 AlphaLemon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://alphalemon.com
 * 
 * @license    MIT License
 * 
 */

namespace AlphaLemon\PageTreeBundle\Tests\Functional\AlphaLemon\PageTreeBundle\Core\PageTree;

use AlphaLemon\PageTreeBundle\Tests\TestCase;
use AlphaLemon\PageTreeBundle\Core\PageTree\AlPageTree;


class AlPageTreeTest extends TestCase 
{    
    public function testDeclaringOnlyTheThemeDoesNotFillTheSlots()
    {
        $pageTree = new AlPageTree($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $pageTree->setThemeName('AlphaLemonThemeBundle');
        $this->assertTrue(count($pageTree->getSlots()) == 0);
    }
    
    public function testDeclaringOnlyTheTemplateDoesNotFillTheSlots()
    {
        $pageTree = new AlPageTree($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $pageTree->setTemplateName('home');
        $this->assertTrue(count($pageTree->getSlots()) == 0);
    }
    
    public function testDeclaringThemeAndTemplateFillsUpTheSlots()
    {
        $pageTree = new AlPageTree($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $pageTree->setThemeName('AlphaLemonThemeBundle');
        $pageTree->setTemplateName('home');
        $this->assertTrue(count($pageTree->getSlots()) > 0);
    }
    
    public function testGetSlot()
    {
        $pageTree = new AlPageTree($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $this->assertNull($pageTree->getSlot('logo'));
        
        $pageTree->setThemeName('AlphaLemonThemeBundle');
        $pageTree->setTemplateName('home');
        $this->assertNull($pageTree->getSlot('foo'));
        $this->assertTrue(count($pageTree->getSlot('logo')) > 0);
    }
    
    public function testAddStylesheetRelativePath()
    {
        $pageTree = new AlPageTree($this->getContainer());
        $this->assertTrue(count($pageTree->getExternalStylesheets()) == 0);
        $pageTree->addStylesheet('@AlphaLemonThemeBundle/Resources/public/css/screen.css');
        $this->assertTrue(count($pageTree->getExternalStylesheets()) == 1); 
        $stylesheets = $pageTree->getExternalStylesheets();
        $this->assertRegExp('/\/([\w]+Bundle)\/Resources\/public\/(.*)/', $stylesheets[0]); 
    }
    
    public function testAddOnlyStylesheet()
    {
        $pageTree = new AlPageTree($this->getContainer());
        $this->assertTrue(count($pageTree->getExternalStylesheets()) == 0);
        $pageTree->addStylesheet('fake');
        $this->assertTrue(count($pageTree->getExternalStylesheets()) == 1);
        $this->assertTrue(in_array('bundles/alphalemonthemeengine/css/fake', $pageTree->getExternalStylesheets()));        
    }
    
    public function testAddStylesheets()
    {
        $pageTree = new AlPageTree($this->getContainer());
        $this->assertTrue(count($pageTree->getExternalStylesheets()) == 0);
        $pageTree->addStylesheets(array('@AlphaLemonThemeBundle/Resources/public/css/screen.css', 'fake'));
        $this->assertTrue(count($pageTree->getExternalStylesheets()) == 2);  
        
        return $pageTree;
    }
    
    /**
     * @depends testAddStylesheets
     */
    public function testGetExternalStylesheetsForWeb(AlPageTree $pageTree)
    {
        $stylesheets = $pageTree->getExternalStylesheetsForWeb();
        $this->assertEquals('bundles/alphalemontheme/css/screen.css', $stylesheets[0]); 
        $this->assertEquals('bundles/alphalemonthemeengine/css/fake', $stylesheets[1]); 
    }
    
    public function testAddOnlyJavascript()
    {
        $pageTree = new AlPageTree($this->getContainer());
        $this->assertTrue(count($pageTree->getExternalJavascripts()) == 0);
        $pageTree->addJavascript('fake');
        $this->assertTrue(count($pageTree->getExternalJavascripts()) == 1);
        $this->assertTrue(in_array('bundles/alphalemonthemeengine/js/fake', $pageTree->getExternalJavascripts()));        
    }
    
    public function testAddJavascripts()
    {
        $pageTree = new AlPageTree($this->getContainer());
        $this->assertTrue(count($pageTree->getExternalJavascripts()) == 0);
        $pageTree->addJavascripts(array('foo', 'bar'));
        $this->assertTrue(count($pageTree->getExternalJavascripts()) == 2);  
        
        return $pageTree;
    }
    
    /**
     * @depends testAddJavascripts
     */
    public function testGetExternalJavascriptsForWeb(AlPageTree $pageTree)
    {
        $stylesheets = $pageTree->getExternalJavascriptsForWeb();
        $this->assertEquals('bundles/alphalemonthemeengine/js/foo', $stylesheets[0]); 
        $this->assertEquals('bundles/alphalemonthemeengine/js/bar', $stylesheets[1]); 
    }
    
    public function testMetatags()
    {
        $pageTree = new AlPageTree($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        
        $this->assertEmpty($pageTree->getMetaTitle());
        $this->assertEmpty($pageTree->getMetaDescription());
        $this->assertEmpty($pageTree->getMetaKeywords());
        
        $pageTree->setMetatags(array('title' => 'the title', 'description' => 'page description', 'keywords' => 'page keywords'));
        
        $this->assertEquals('the title', $pageTree->getMetaTitle());
        $this->assertEquals('page description', $pageTree->getMetaDescription());
        $this->assertEquals('page keywords', $pageTree->getMetaKeywords());
    }
    
    public function testAddContent()
    {
        $pageTree = new AlPageTree($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $pageTree->setThemeName('AlphaLemonThemeBundle');
        $pageTree->setTemplateName('home');
        $this->assertTrue(count($pageTree->getContents('logo')) == 0);
        $pageTree->addContent('logo', array('HtmlContent' => 'my content'));
        $this->assertTrue(count($pageTree->getContents('logo')) == 1);
        
        
        return $pageTree;
    }
    
    /**
     * @depends testAddContent
     */
    public function testAddAnotherContentOnTheSameSlot(AlPageTree $pageTree)
    {
        $pageTree->addContent('logo', array('HtmlContent' => 'my other content'));
        $this->assertTrue(count($pageTree->getContents('logo')) == 2);
    }
    
    public function testSetContents()
    {
        $pageTree = new AlPageTree($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $pageTree->setThemeName('AlphaLemonThemeBundle');
        $pageTree->setTemplateName('home');
        $this->assertTrue(count($pageTree->getContents('logo')) == 0);
        $pageTree->setContents(array('logo' => array(array('HtmlContent' => 'my content'))));
        $this->assertTrue(count($pageTree->getContents('logo')) == 1);
    }
    
    public function testSetMoreContentsATime()
    {
        $pageTree = new AlPageTree($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $pageTree->setThemeName('AlphaLemonThemeBundle');
        $pageTree->setTemplateName('home');
        $this->assertTrue(count($pageTree->getContents('logo')) == 0);
        $pageTree->setContents(array('logo' => array(array('HtmlContent' => 'my content'), array('HtmlContent' => 'my content 1'))));
        $this->assertTrue(count($pageTree->getContents('logo')) == 2);
        
        return $pageTree;
    }
    
    /**
     * @depends testSetMoreContentsATime
     */
    public function testSetContentsOverridingTheOnesAlreadySaved(AlPageTree $pageTree)
    {
        $this->assertTrue(count($pageTree->getContents('logo')) > 1);
        $pageTree->setContents(array('logo' => array(array('HtmlContent' => 'new content'))), true);
        $this->assertTrue(count($pageTree->getContents('logo')) == 1);
    }
}