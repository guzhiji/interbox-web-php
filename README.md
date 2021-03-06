#InterBox Core 1.2 - interbox.core.web

##Demo

![home of the demo](http://guzhijistudio.comoj.com/uploadedfiles/1405953941.png)

home of the demo (available from the demo branch)


###Multi-theme

![the yellow theme](http://guzhijistudio.comoj.com/uploadedfiles/140595394164.png)

Click "black" on the yellow theme.

![theme changes to black](http://guzhijistudio.comoj.com/uploadedfiles/140595394115.png)

It switches to the black theme now.


###Multi-language

![English](http://guzhijistudio.comoj.com/uploadedfiles/1405953984.png)

English

![Simplified Chinese](http://guzhijistudio.comoj.com/uploadedfiles/140595398414.png)

Simplified Chinese


###Caching

####5s Timing

![first load or timeout](http://guzhijistudio.comoj.com/uploadedfiles/140595409265.png)

When first load or timeout, content is reloaded.

![within caching time](http://guzhijistudio.comoj.com/uploadedfiles/140595409258.png)

Within 5s' caching time, it saves loading time.


####Versioning

![version up-to-date](http://guzhijistudio.comoj.com/uploadedfiles/14059540927.png)

When version is up-to-date, it saves loading time.

![version updated](http://guzhijistudio.comoj.com/uploadedfiles/140595409253.png)

When there is a newer version, it reloads.


####Turned Off

![no caching](http://guzhijistudio.comoj.com/uploadedfiles/1405954092.png)

Caching is off.


##The Box!

###Page is box

A page is the top level container which is essentially a box as well. However, a page does have something different. For example, the Route() method is able to route a request to a module and render it's view/boxes.

A module consists of a set of boxes (view) and a set of processes (business logic). A page has its default module and a module has its default view.

The default module is at the top level, where we can directly define the default view in the attribute "box" or "boxes".

```php
$p = new TestPage();
$p->Route(array(
    'box' => array('WelcomeBox', NULL)
));
$p->Show();
```

Or

```php
$p = new TestPage();
$p->Route(array(
    'boxes' => array(
        array('WelcomeBox', NULL),
        array('MenuBox', NULL)
    )
));
$p->Show();
```

If more modules are needed, we can specify them in an attribute called "modules":

```php
$p = new TestPage();
$p->Route(array(
    'box' => array('WelcomeBox', NULL),
    'modules' => array(
        'configuration' => array(
            'box' => array('ConfigBox', NULL)
        )
    )
));
$p->Show();
```

So, we can access the box "WelcomeBox" (the default module) at / and the box "ConfigBox" (the module "configuration") at /configuration/.

If there're some nesting modules, we prefer:

```php
$p = new TestPage();
$p->Route(array(
    'box' => array('WelcomeBox', NULL),
    'modules' => array(
        'configuration' => array(
            'box' => array('ConfigBox', NULL)
        ),
        'configuration/layout' => array(
            'box' => array('ConfigLayoutBox', NULL)
        )
    )
));
$p->Show();
```

There're fewer nested arrays, so it looks less complicated.


###Organize

For example, we have such a page with 3 columns, but there's nothing in the left one:

```php
$p = new TestPage();
$p->Route(array(
    'boxes' => array(
        array('MainColBox1', NULL),
        array('MainColBox2', NULL),
        'right_col' => array(
            array('RightColBox1', NULL),
            array('RightColBox2', NULL)
        ),
        'left_col' => array()
    )
));
$p->Show();
```

Now, we can change its configuration:
* change box order in the main column
* move one box from right column to left

```php
$p = new TestPage();
$p->Route(array(
    'boxes' => array(
        array('MainColBox2', NULL),
        array('MainColBox1', NULL),
        'right_col' => array(
            array('RightColBox1', NULL)
        ),
        'left_col' => array(
            array('RightColBox2', NULL)
        )
    )
));
$p->Show();
```

Or even simplified:

```php
$p = new TestPage();
$p->Route(array(
    'boxes' => array(
        array('MainColBox2', NULL),
        array('MainColBox1', NULL),
        'right_col' => array('RightColBox1', NULL),
        'left_col' => array('RightColBox2', NULL)
    )
));
$p->Show();
```

###Templates

TODO ...
template engines: `TransformTpl`, `RenderPHPTpl` (changing)
template files: grouped by classname, but classname can be specified `parent::__construct(__CLASS__)`
auto formatting: type declaration prefix with underscope

###Arguments

TODO ...

###Output Status

Each box has its status that indicates its behaviour when loading content.

* Normal status
* Hidden status
* Forward status
* UseCache status

Essentially, content is loaded either successfully or unsuccessfully. So normal is normal, otherwise a box may express its special status according to its needs.

For example, if there's no result to be displayed, a box can simply escape from the world or redirect to another box showing something different.

And from my personal experience using free web hosting providers, their database servers can be very unstable since they are more susceptible to hackers. However PHP often hide the fact that database fails to connect. It showed nothing but it wasn't that case at all. To make it less frustrating in such situations, the UseCache status becomes extremely helpful.

What's behind the scene is the `AddBox()` method. It first calls `BoxModel::Before($page)` to run some preliminary code so as to test whether it needs to load content at all. Either after `BoxModel::Before($page)` or `BoxModel::LoadContent()`, the parent reads the box's status in order to deal with its special behviours such as performing 'forward'. (still under review)

To change status of a box, there are methods provided:

* `BoxModel::Hide()`
* `BoxModel::Forward($box, $params)`
* `BoxModel::UseCache()`
* or, call nothing so it's normal


###Cache



TODO ...


###Localization

##The Process

###Overview

It's simple. It just means to do something, but everything happening here should be completely outside the logics dealing with the views!

There can be some ways of output.
* a box!
* some data: json, xml, binary... (needs improving)
* TODO: location header (I forgot)

###Organize

Similarly, processes can be easily plugged into modules.

```php
$p = new TestPage();
$p->Route(array(
    'box' => array('WelcomeBox', NULL),
    'functions' => array(
        'setlang' => array('SetLang', NULL)
    ),
    'modules' => array(
        'configuration' => array(
            'box' => array('ConfigBox', NULL),
            'functions' => array(
                'save' => array('SaveConfig', NULL)
            )
        )
    )
));
$p->Show();
```

###Output a Box


###Output JSON Data




##TODO: utility extensions

Currently, some designs even don't look good to me. Well, keep hard-working!
