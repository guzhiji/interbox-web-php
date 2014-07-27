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

###Page is a box

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

So it looks less complicated.


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
-change box order in the main column
-move one box from right column to left

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

###Redirect

Apart from the traditional way of redirecting a request to another URL, we're talking about redirecting a single box.



###Theme


###Cache


TODO ...

