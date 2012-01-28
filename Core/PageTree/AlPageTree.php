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
 */

namespace AlphaLemon\PageTreeBundle\Core\PageTree;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AlphaLemon\PageTreeBundle\Core\Tools\AlToolkit;

/**
 * The AlPageTree is the deputated object to describe a website's page. This object stores several information about the web page:
 *
 *   - Theme Name
 *   - Template Name
 *   - External stylesheets
 *   - External javascripts
 *   - Internal stylesheets
 *   - Internal javascripts
 *   - Slots
 *   - Contents
 *   - SEO metatags (title, description, keywords)
 *
 * @author AlphaLemon
 */
class AlPageTree
{
    protected $container = null;
    protected $externalStylesheets = array();
    protected $externalJavascripts = array();
    protected $internalJavascript = "";
    protected $internalStylesheet = "";
    protected $contents = array();
    protected $themeName = null;
    protected $templateName = null;
    protected $templateSlots = null;
    protected $metaTitle = "";
    protected $metaDescription = "";
    protected $metaKeywords = "";
    
    /**
     * Constructor
     * 
     * @param ContainerInterface $container 
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function getExternalStylesheets()
    {
        return $this->externalStylesheets;
    }

    public function getExternalJavascripts()
    {
        return $this->externalJavascripts;
    }

    public function getInternalJavascript()
    {
        return $this->internalJavascript;
    }

    public function appendInternalJavascript($v)
    {
        $this->internalJavascript .= $v;
    }

    public function getInternalStylesheet()
    {
        return $this->internalStylesheet;
    }

    public function appendInternalStylesheet($v)
    {
        $this->internalStylesheet .= $v;
    }

    public function setTemplateName($v)
    {
        $this->templateName = $v;
        $this->setupPageTree();
    }

    public function getTemplateName()
    {
        return $this->templateName;
    }

    public function setThemeName($v)
    {
        $this->themeName = $v;
        $this->setupPageTree();
    }

    public function getThemeName()
    {
        return $this->themeName;
    }
    
    public function setMetaTitle($v)
    {
        $this->metaTitle = $v;
    }

    public function getMetaTitle()
    {
        return $this->metaTitle;
    }
    
    public function setMetaDescription($v)
    {
        $this->metaDescription = $v;
    }

    public function getMetaDescription()
    {
        return $this->metaDescription;
    }
    
    public function setMetaKeywords($v)
    {
        $this->metaKeywords = $v;
    }

    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }
    
    /**
     * Returns the web path of each given stylesheet to be loaded by the browser:
     * 
     *      @AlphaLemonThemeBundle/Resources/public/css/screen.css
     *      /path/to/Themes/AlphaLemonThemeBundle/Resources/public/css/screen.css
     * 
     * is converted as follows:
     * 
     *      /bundles/alphalemontheme/css/screen.css
     * 
     * @return  string 
     */
    public function getExternalStylesheetsForWeb()
    {
        return $this->setAssetsForWeb($this->externalStylesheets);
    }
    
    /**
     * Returns the web path of each given javascript to be loaded by the browser:
     * 
     *      @AlphaLemonThemeBundle/Resources/public/js/screen.js
     *      /path/to/Themes/AlphaLemonThemeBundle/Resources/public/js/screen.js
     * 
     * is converted as follows:
     * 
     *      /bundles/alphalemontheme/js/screen.js
     * 
     * @return  string 
     */
    public function getExternalJavascriptsForWeb()
    {
        return $this->setAssetsForWeb($this->externalJavascripts);
    }
    
    /**
     * Sets the metatags. Valid keys are: 
     *  
     *      - title
     *      - description
     *      - keywords
     * 
     * @param array $metatags 
     */
    public function setMetatags(array $metatags)
    {
        if(array_key_exists('title', $metatags)) $this->metaTitle = $metatags['title'];
        if(array_key_exists('description', $metatags)) $this->metaDescription = $metatags['description'];
        if(array_key_exists('keywords', $metatags)) $this->metaKeywords = $metatags['keywords'];
    }

    /**
     * Returns the template's slots
     * 
     * @return array 
     */
    public function getSlots()
    {
        return (null !== $this->templateSlots) ? $this->templateSlots->getSlots() : array();;
    }

    /**
     * Returns a slot by its name
     * 
     * @return array 
     */
    public function getSlot($slotName)
    {
        if(null === $this->templateSlots)
        {
            return null;
        }

        $slots = $this->getSlots();
        if(!\array_key_exists($slotName, $slots))
        {
            return null;
        }

        return $slots[$slotName];
    }

    /**
     * Adds some external stylesheets
     * 
     * @param array $value 
     */
    public function addStylesheets(array $values)
    {
        if(null !== $values)
        {
            foreach($values as $stylesheetJavascript)
            {
                $this->addStylesheet($stylesheetJavascript);
            }
        }
    }
    
    /**
     * Adds some external javascripts
     * 
     * @param array $value 
     */
    public function addJavascripts(array $values)
    {
        if(null !== $values)
        {
            foreach($values as $externalJavascript)
            {
                $this->addJavascript($externalJavascript);
            }
        }
    }

    /**
     * Adds an external stylesheet
     * 
     * @param string $value 
     */
    public function addStylesheet($value)
    {
        if($value != "" && !in_array($value, $this->externalStylesheets))
        {
            
            if(basename($value) == $value)
            {
                $bundle = ($this->container->hasParameter('al.deploy_bundle')) ? $this->container->getParameter('al.deploy_bundle') : 'ThemeEngineBundle';
                $targetFolder = ($this->container->hasParameter('al.deploy_bundle_css_folder')) ? $this->container->getParameter('al.deploy_bundle_css_folder') : 'css';
                $bundleFolder = AlToolkit::retrieveBundleWebFolder($this->container, $bundle);
                $fileName = $bundleFolder . '/' . $targetFolder . '/'. $value;
            }
            else
            { 
                // When the whole files in the folder are required, it is needed the path to the web folder
                if(substr($value, strlen($value) - 1, 1) == '*')
                {
                    $bundleName = preg_match('/^@([\w]+)\//', $value, $match);
                    $fileName = (isset($match[1])) ? AlToolkit::retrieveBundleWebFolder($this->container, $match[1]) . '/*' : '';
                }
                else
                {
                    $fileName = (strpos($value, 'bundles') === 0) ? $value : AlToolkit::locateResource($this->container, $value);
                }
            }
            
            $this->externalStylesheets[] = $fileName;
        }
    }

    /**
     * Adds an external javascript
     * 
     * @param string $value 
     */
    public function addJavascript($value)
    {
        if($value != "" && !in_array($value, $this->externalJavascripts))
        {
            if(basename($value) == $value)
            {
                $bundle = ($this->container->hasParameter('al.deploy_bundle')) ? $this->container->getParameter('al.deploy_bundle') : 'ThemeEngineBundle';
                $targetFolder = ($this->container->hasParameter('al.deploy_bundle_js_folder')) ? $this->container->getParameter('al.deploy_bundle_js_folder') : 'js';
                $bundleFolder = AlToolkit::retrieveBundleWebFolder($this->container, $bundle);
                $fileName = $bundleFolder . '/' . $targetFolder . '/'. $value;
            }
            else
            { 
                if(substr($value, strlen($value) - 1, 1) == '*')
                {
                    $bundleName = preg_match('/^@([\w]+)\//', $value, $match);
                    $fileName = (isset($match[1])) ? AlToolkit::retrieveBundleWebFolder($this->container, $match[1]) . '/*' : '';
                }
                else
                {
                    $fileName = (strpos($value, 'bundles') === 0) ? $value : AlToolkit::locateResource($this->container, $value);
                }
            }
            
            $this->externalJavascripts[] = $fileName;
        }
    }
    
    /**
     * Returns the content placed on the required slot
     * 
     * @param string    $slotName   The name of slot to retrieve the contents
     * @return array 
     */
    public function getContents($slotName = null)
    {
        if(null === $slotName)
        {
            return $this->contents;
        }

        // The designer has requested a non existent slot for the template he/she is designing
        if(null !== $this->templateSlots && !\array_key_exists($slotName, $this->getSlots()))
        {
            throw new \InvalidArgumentException($this->container->get('translator')->trans('The %slotName% is not part of this template. You can add a new slot in the configure method of the template\'s slot definition, or remove the wrong one from the template itself', array('%slotName%' => $slotName)));
        }

        // The slot has any content inside
        if(!\array_key_exists($slotName, $this->contents))
        {
            return null;
        }
        
        return $this->contents[$slotName];
    }

    /**
     * Sets the contents for the given slots. The $values array is made as follows:
     * 
     *  array('slotname' => 
     *              array(
     *                  array([HtmlContent] => 'content1'), 
     *                  array([HtmlContent] => 'content2'), 
     *                  ..., 
     *                  array([HtmlContent] => 'content[n]'),
     *                  ),
     *        'slotname1' => ... 
     *         )
     * 
     * @param   array   $values  
     * @param   type    $override  When true, overrides the contents on the slot whit the new ones
     */
    public function setContents(array $values, $override = false)
    {
        foreach($values as $slotName => $contents)
        {
            if(array_key_exists($slotName, $this->contents) && $override) unset($this->contents[$slotName]);

            if(null !== $contents)
            {
                foreach($contents as $content)
                {
                    $this->addContent($slotName, $content);
                }
            }
            else
            {
                $this->contents[$slotName] = null;
            }
        }
    }
    
    /**
     * Clears the contents for the given slot
     * 
     * @param string    $slotName 
     */
    public function clearSlotContents($slotName)
    {
        $this->contents[$slotName] = array();
    }
    
    /**
     * Adds a content on the given slot
     * 
     * @param string    $slotName   The name of the slot to add the content
     * @param array     $content    An array to pass the content. Eg. array('HtmlContent' => 'The content')
     * @param type      $key        The contents' key. Contents are store in an array whose keys are the usual  
     *                              array incremental integer, starting by zero. So if the slot has three contents,
     *                              passing 1 as key will replace the second content
     */
    public function addContent($slotName, array $content, $key = null)
    {
        if(null !== $key && array_key_exists($key, $this->contents[$slotName]))
        {
            $this->contents[$slotName][$key] = $content;
        }
        else
        {
            $this->contents[$slotName][] = $content;
        }
    }
    
    /**
     * Normalizes the paths of the assets saved inside the Resource/public folder, for the current bundle
     * 
     * @param type $assets
     * @return string 
     */
    protected function setAssetsForWeb($assets)
    {
        $result = array(); 
        foreach($assets as $asset)
        {
            $asset = AlToolkit::normalizePath($asset);
            preg_match('/\/([\w]+Bundle)\/Resources\/public\/(.*)/', $asset, $match);
            if(!empty($match))
            {
                $bundleFolder = AlToolkit::retrieveBundleWebFolder($this->container, $match[1]);
                $fileName = $bundleFolder . '/' . $match[2];

                $result[] = $fileName;
            }
            else
            {
                $result[] = $asset;
            }
        }
        return $result;
    }
    
    /**
     * Sets up the page tree object for the current template
     */
    protected function setupPageTree()
    {
        if($this->themeName != '' && $this->templateName != '') {
            $templateName = $this->templateName;
            $className = \sprintf('Themes\%s\Core\Slots\%s%sSlots', $this->themeName, $this->themeName, \ucfirst($templateName)); 
            if(!\class_exists($className)) {
                throw new \RuntimeException($this->container->get('translator')->trans('The class %className% does not exist. You must create a [ThemeName][TemplateName]Slots class for each template of your theme.', array('%className%' => $className)));
            }
            
            $this->templateSlots = new $className();
            
            $theme = preg_replace('/bundle$/', '', strtolower($this->themeName));
            
            $templateName = strtolower($templateName);
            $param = sprintf('themes.%s_%s.internal_javascript', $theme, $templateName);
            if($this->container->hasParameter($param)) $this->appendInternalJavascript($this->container->getParameter($param));
            
            $param = sprintf('themes.%s_%s.internal_stylesheet', $theme, $templateName);
            if($this->container->hasParameter($param)) $this->appendInternalStylesheet($this->container->getParameter($param));
            
            $param = sprintf('themes.%s_%s.javascripts', $theme, $templateName);
            if($this->container->hasParameter($param)) $this->addJavascripts($this->container->getParameter($param));
            
            $param = sprintf('themes.%s_%s.stylesheets', $theme, $templateName);
            if($this->container->hasParameter($param)) $this->addStylesheets($this->container->getParameter($param));
        }
    }
}
