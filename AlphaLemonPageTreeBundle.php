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
namespace AlphaLemon\PageTreeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlphaLemonPageTreeBundle extends Bundle
{
  public function build(ContainerBuilder $container)
    {

        throw new \RuntimeException("This bundle has been deprecated and moved to ThemeEngineBundle. Please use that bundle instead of this one")
    }
}
