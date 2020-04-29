Getting Started With PNDynamicFormBundle for multi languages web apps
==================================

### Prerequisites
1. Symfony 3.4
2. [PNMediaBundle](https://github.com/PerfectNeeds/media-bundle)
3. [PNServiceBundle](https://github.com/PerfectNeeds/service-bundle)


Installation
------------

Installation is a quick (I promise!) 7 step process:

1. Download PNDynamicFormBundle using composer
2. Enable the Bundle in AppKernel
3. Create your Post class
4. Create your PostRepository class
5. Configure the PNDynamicFormBundle
6. Import PNDynamicFormBundle routing
7. Update your database schema
------------
### Step 1: Download PNDynamicFormBundle using composer
Require the bundle with composer:
```sh
$ composer require perfectneeds/dynamic-form-single-lang-bundle "~1.0"
```
### Step 2: Enable the Bundle in AppKernel
Require the bundle with composer:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new PN\MediaBundle\PNMediaBundle(),
        new \PN\ServiceBundle\PNServiceBundle(),
        new \PN\DynamicFormBundle\PNDynamicFormBundle(),
        // ...
    );
}
```

### Step 3: Create your Post class
The goal of this bundle is to persist some `Post` class to a database. Your first job, then, is to create the
`Post` class for your application. This class can look and act however
you want: add any properties or methods you find useful. This is *your*
`Post` class.

The bundle provides base classes which are already mapped for most
fields to make it easier to create your entity. Here is how you use it:

1.  Extend the base `Post` class (from the `Entity` folder if you are
    using any of the doctrine variants)
2.  Map the `id` field. It must be protected as it is inherited from the
    parent class.

#### Caution!

When you extend from the mapped superclass provided by the bundle, don't redefine the mapping for the other fields as it is provided by the bundle.

In the following sections, you'll see examples of how your `Post` class should look, depending on how you're storing your posts (Doctrine ORM).

##### Note

The doc uses a bundle named `DynamicFormBundle`. However, you can of course place your post class in the bundle you want.

###### Caution!

If you override the __construct() method in your Post class, be sure to call parent::__construct(), as the base Post class depends on this to initialize some fields.


#### Doctrine ORM Post class

If you're persisting your post via the Doctrine ORM, then your `Post` class should live in the Entity namespace of your bundle and look like this to start:

*You can add all relations between other entities in this class

```php
<?php
// src/PN/Bundle/DynamicFormBundle/Entity/Post.php

namespace PN\Bundle\DynamicFormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// DON'T forget the following use statement!!!
use PN\DynamicFormBundle\Entity\Post as BasePost;
use PN\DynamicFormBundle\Model\PostTrait;

 /**
 * Post
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="PN\Bundle\DynamicFormBundle\Repository\PostRepository")
 */
class Post extends BasePost {

    use PostTrait;
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
```

### Step 4: Create your PostRepository class
You can use this `Repository` to add any custom methods 

```php
<?php
// src/PN/Bundle/DynamicFormBundle/Repository/PostRepository.php


namespace PN\Bundle\DynamicFormBundle\Repository;

use PN\DynamicFormBundle\Repository\PostRepository as BasePostRepository;

class PostRepository extends BasePostRepository {

}
```

### Step 5: Configure the PNDynamicFormBundle
Add the following configuration to your config.yml file according to which type of datastore you are using.

```ymal
# app/config/config.yml 

doctrine:
   orm:
        # search for the "ResolveTargetEntityListener" class for an article about this
        resolve_target_entities: 
            PN\MediaBundle\Entity\Image: PN\Bundle\MediaBundle\Entity\Image
            PN\MediaBundle\Entity\Document: PN\Bundle\MediaBundle\Entity\Document

pn_content:
    # The fully qualified class name (FQCN) of the Post class which you created in Step 3.
    post_class: PN\Bundle\DynamicFormBundle\Entity\Post
```

### Step 6: Import PNDynamicFormBundle routing files

```ymal
# app/config/routing.yml 

pn_media:
    resource: "@PNMediaBundle/Resources/config/routing.yml"

pn_content:
    resource: "@PNDynamicFormBundle/Resources/config/routing.yml"
```

### Step 7: Update your database schema
Now that the bundle is configured, the last thing you need to do is update your database schema because you have added a new entity, the `Post` class which you created in Step 3.

```sh
$ php bin/console doctrine:schema:update --force
```

------
# How to use PNDynamicFormBundle

1. Use **Post** in Entity using Doctrine ORM
2. Use **Post** in Form Type
3. How to add a custom fields _ex. brief, description, etc ..._
4. Use **Post** in controller
5. Use **Post** in details page like `show.html.twig`
--------------------------
#### 1. Use Post in Entity using Doctrine ORM

First of all you need to add a relation between an Entity need to use Post with Post class in `src/PN/Bundle/DynamicFormBundle/Entity/Post.php`
_ex. Blogger, Product, etc ..._
Example entities:  
Post.php 
```php
<?php
// src/PN/Bundle/DynamicFormBundle/Entity/Post.php

namespace PN\Bundle\DynamicFormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PN\DynamicFormBundle\Entity\Post as BasePost;
use PN\DynamicFormBundle\Model\PostTrait;

/**
 * Post
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="PN\Bundle\CMSBundle\Repository\PostRepository")
 */
class Post extends BasePost {

    use PostTrait;
        
    // Add here your own relations
    
    /**
     * @ORM\OneToOne(targetEntity="\PN\Bundle\CMSBundle\Entity\DynamicPage", mappedBy="post")
     */
    protected $dynamicPage;
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
```

DynamicPage.php
```php
<?php

namespace PN\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PN\ServiceBundle\Model\DateTimeTrait;

/**
 * DynamicPage
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="dynamic_page")
 * @ORM\Entity(repositoryClass="PN\Bundle\CMSBundle\Repository\DynamicPageRepository")
 */
class DynamicPage {

    use DateTimeTrait;
    ....

    /**
     * @ORM\OneToOne(targetEntity="PN\Bundle\DynamicFormBundle\Entity\Post", inversedBy="dynamicPage", cascade={"persist", "remove" })
     */
    protected $post;
    
    ....
}

```

#### 2. Use _Post_ in Form Type
You need to add Post Type in any Form type to use this magical tool

DynamicPageType.php
```php
<?php

namespace PN\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

// DON'T forget the following use statement!!!
use PN\DynamicFormBundle\Form\PostType;


class DynamicPageType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('post', PostType::class)
                ......
                ;
    }
    .....
}
```

### 3. How to add a custom fields
##### If you need to add a custom fields for any Form type 
for example add a `shortDescription` field in DyncamicPageType.php

```php
<?php

namespace PN\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use PN\DynamicFormBundle\Form\PostType;

// DON'T forget the following use statement!!!
use PN\DynamicFormBundle\Form\Model\PostTypeModel;


class DynamicPageType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $postTypeModel = new PostTypeModel();
        $postTypeModel->add("description", "descriptionsss");
        $postTypeModel->add("brief", "Brief");
        
        /** documentation
         * @param string $name field_name (must not contain any spaces or special characters)
         * @param string $label field_label
         * @param array $options field_options
         */
        $postTypeModel->add({field_name}, {field_label}, {field_options});
    
        $builder
                ->add('post', PostType::class, [
                    //  DON'T forget the following statement!!!
                    "attributes" => $postTypeModel
                ])
                ......
                ;
    }
    .....
}
```

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/PerfectNeeds/dynamic-form-single-lang-bundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.