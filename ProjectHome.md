This project is a small PHP website toolkit created over years of web sites development. I've come up with this framework more recently and finally decided to release it under LGPL.

Why another PHP framework? Well, as already said, this project comes from years of PHP development. There are probably better frameworks out there at this time, but when I started this project, I wasn't able to find one that was giving me a good degree of liberty and flexibility, and that was designed to be multilingual (something that is too often neglect in other frameworks).

-- Georges-Etienne Legendre, http://legege.com

### Main features ###
  * Multilingual support (in URL, language browser detection, ...)
  * Template definition and inheritance
  * Control the final URL for each page (and alias support)
  * Page hierarchy
  * Custom actions and data manipulation
  * Keep the control: this framework doesn't use awkward template syntax. You still write PHP in HTML files.

### Requirements ###

  * PHP 5
  * Apache (with mod\_rewrite)
    * Would probably work with other HTTP server. You only need to translate the .htaccess to support another HTTP server.

### Example of website running PHPWT ###

  * http://legege.com (Georges-Etienne's web site)
  * http://www.fermestadolphe.com
  * http://www.mariannelegendre.com
  * http://rubico.info (Felix-Antoine's web site)
  * Intranet web sites

### Contributions ###

  * I welcome your contribution. Simply [contact me](http://legege.com)!

### FAQ (or not so frequent yet) ###

#### How does a web site structure looks like? ####

```
.htaccess
index.php
content/
  (all your website content, you can organize your files as you want, no matter what you want to have for the URL)
  index.en.php
  index.fr.php
  contact.en.php
  contact.fr.php
phpwt/
  conf/
    config.inc.php
    config.xml
  core/
    (all PHPWT files)
```

#### Where do I configure templates, pages, site hierarchy? ####

All configurations are in XML files. You can have a single config.xml file or multiple ones all referenced in config.xml.

Here is an example of a config.xml file.
```
<?xml version="1.0" ?>
<toolkit>
  <website lang="en">
    <resource name="global" file="/content/resources/global.en.properties" />

    <page-definitions>
      <page id="/home">
        <property name="title" value="Home" />
      </page>
      <page id="/contact" />
    </page-definitions>

    <page-mappings>
      <page id="/home" uri="/">
        <alias uri="/index" />
      </page>
      <page id="/contact" uri="/home/contact" />
    </page-mappings>

    <hierarchy>
      <page id="/home">
        <page id="/contact">
      </page>
    </hierarchy>

    <template-mappings>
      <global-templates>
        <template name="site" base="/content/templates/base.inc.php">
          <section name="header" file="/content/templates/header.inc.php" />
          <section name="body" />
          <section name="footer" file="/content/templates/footer.inc.php" />
          <section name="left" file="/content/templates/left/base.inc.php" />
          <section name="left-hierarchy" />
          <section name="left-about" />
        </template>

        <template name="site.en" extends="site">
          <section name="left-about" file="/content/templates/left/about.en.inc.php" />
        </template>
      </global-templates>

      <page-templates id="/home" default="default">
        <template name="default" extends="site.en">
          <section name="body" file="/content/index.en.php">
            <property name="myCustomValue" value="test" />
          </section>
        </template>
      </page-templates>

      <page-templates id="/contact" default="default">
        <template name="default" extends="site.en">
          <section name="body" file="/content/contact.en.php" />
        </template>
      </page-templates>
    </template-mappings>
  </website>
</toolkit>
```