<?php
/*
 * This file is part of the AlphaLemon CMS Application and it is distributed
 * under the GPL LICENSE Version 2.0. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) AlphaLemon <webmaster@alphalemon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.alphalemon.com
 *
 * @license    GPL LICENSE Version 2.0
 *
 */

namespace AlphaLemon\PageTreeBundle\Core\PageBlocks;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AlphaLemon\AlphaLemonCmsBundle\Core\Model\Entities\BlockModelInterface;
use AlphaLemon\AlphaLemonCmsBundle\Core\Exception\Content\General;

/**
 * AlPageBlocks is the object responsible to manage the blocks on a web page. A block on a web
 * page contains a single content.
 *
 *
 * Providing the page id and language id, it retrieves the contents and arrange them
 * into an array which keys are the name of slot where the contents live.
 *
 * @author alphalemon <webmaster@alphalemon.com>
 */
class AlPageBlocks implements AlPageBlocksInterface
{
    protected $blocks = array();
    protected $validContentParams = array('HtmlContent' => '');

    /**
     * {@inheritdoc}
     */
    public function add($slotName, array $values, $position = null)
    {
        $value = array_diff($this->validContentParams, $values);
        if (empty($value)) {
            throw new \InvalidArgumentException('The block requires a key ');
        }

        if(null !== $position && array_key_exists($position, $this->blocks[$slotName]))
        {
            $this->blocks[$slotName][$position] = $value;
        }
        else
        {
            $this->blocks[$slotName][] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addRange(array $values, $override = false)
    {
        foreach($values as $slotName => $contents)
        {
            if (array_key_exists($slotName, $this->blocks) && $override) {
                $this->clearSlotBlocks($slotName);
            }

            if(null !== $contents)
            {
                foreach($contents as $content)
                {
                    $this->addBlock($slotName, $content);
                }
            }
            else
            {
                $this->blocks[$slotName] = null;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearSlotBlocks($slotName)
    {
        $this->blocks[$slotName] = array();
    }

    /**
     * {@inheritdoc}
     */
    public function clearBlocks()
    {
        $this->blocks = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlotBlocks($slotName)
    {
        return (array_key_exists($slotName, $this->blocks)) ? $this->blocks[$slotName] : array();
    }
}