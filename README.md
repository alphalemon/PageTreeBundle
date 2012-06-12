#PageTreeBundle
The PageTreeBundle implements the properties and methods to describe a website's page.

## Get the PageTreeBundle
Clone this bundle in the vendor/bundles/AlphaLemon directory:

    git clone git://github.com/alphalemon/PageTreeBundle.git vendor/bundles/AlphaLemon/PageTreeBundle

## Configure the ThemeEngineBundle
Open the AppKernel configuration file and add the bundle to the registerBundles() method:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new AlphaLemon\PageTreeBundle\AlphaLemonPageTreeBundle(),
        )
    }

Register the PageTreeBundle namespaces in `app/autoload.php`:

    $loader->registerNamespaces(array(
        ...
        'AlphaLemon'                     => __DIR__.'/../vendor/bundles',
    ));

## Using the object
The page tree object is loaded and injected into the Dependency Injector Container and can be retrieved as follows:
    
    $pageTree = $this->container->get('al_page_tree');

### Info and help
To get extra information or help you may write an email to info [at] alphalemon [DoT] com