YAPTE
=====

Yet Another PHP Template Engine

Useful little (<150 lines) templater that doesn't get in the way.

Supports parent-child/master templates with sections (think TWIG).

PHP 5.3+ Recommended.

Basic Usage
-----------

The core of the templater is focused on some simple reg-ex replcements. As Below.

 * __Output / Logic__
 * {{ $hello }}  --> <?php echo $hello ;?>
 * {% if(true): %}True!{% endif %} --> <?php if(true): ;?>True!<?php endif ;?>
 * __Templater Shortcuts__
 * {{t Render('menu') }} --> <?php echo $this->Render('menu') ;?>
 * {%t StartSection('content') %} --> <?php $this->StartSection('content') ;?>

The theme here is that double brackets output and bracket-percent performs actions/does logic.

You are of course free to use normal php tags where you see fit, these are there to help but are not required.

Compiled Templates
------------------

The templates can be compiled to skip the step of reg-exing them, the configuration is explained below.

The compiled templates can be cleared at run-time by calling `Template::ClearCompiled()`

Configuration
-------------

The configuration is done via constants, as this is simpler than some sort of config system (except where one already exists).

 * DIR_TEMPLATES -- The template directory.
 * DIR_TEMPLATES_COMPILED - The compiled templates directory.
    If not defined or false compiled templates are not used.
    This is not relative to the template directory.

Both can be either absolute paths or relative based on what `file_get_contents` can find.

Master Template Example
-----------------------

Child Template `hello.php`:

    {%t SetMaster('master') %}

    {%t StartSection('content') %}
    Hello {{$world}}
    {%t EndSection() %}

Any text/content outside the sections will be lost but executed (ie not output).

When evaluated the master template will be rendered and the content of the sections will be available to output. 

Parent Template `master.php`:

    <!doctype html>
    <html>
        <head>
            <title>{{$title}}</title>
        </head>
        <body>
            {{t GetSection('content') }}
        </body>
    </html>

`$this->sections` may be used for the available sections.

Context
-------

The context is the data initially provided to the template engine and is available via `$this->context`; these variables are extracted as references for use.

A new context is created for each recursive call (ie master template, menu, etc) which contains the 'upper' context. modification is one way a higher context may affect lower contexts but not vice versa.

For instance, if a child template were to set the title in the current context this will be available in context when the master template is rendered.  
IE. `{%t context['title'] = 'Hello!' }%`. The master can then use `{{$title}}` to display 'Hello!'

Due to the nature of `extract` some variables cannot be used in render data, these include `context`, `this`, `file` among others.