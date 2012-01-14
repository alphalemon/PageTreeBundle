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

namespace AlphaLemon\PageTreeBundle\Tests\DependencyInjector;

use AlphaLemon\PageTreeBundle\Tests\TestCase;
use AlphaLemon\PageTreeBundle\DependencyInjection\AlphaLemonPageTreeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class AlphaLemonPageTreeExtensionTest extends TestCase 
{   
    public function testConfigLoad()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);
        $loader = new AlphaLemonPageTreeExtension();

        $loader->load(array(array()), $container);
        $this->assertTrue($container->hasDefinition('al_page_tree'));   
    }
}