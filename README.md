# Gettext editor module for Zend Framework 2

Potool is a tool for web-based edition gettext translation files. It is easy to integrate it into ZF2 project. Translator would be able to translate, edit phrases online and compile MO files for applying results. Potool is designed with using Bootstrap css framework.

## Overview
After installing you can open address http[s]://{you project root}/potool
Here is list of your zf2 modules:
![List of modules](https://raw.githubusercontent.com/eugenzor/Potool/master/docs/images/potool_modules.png)
Here you can work with all internationalization files of certain module. If you have added some new translation key into your module, you have to import new translation keys into PO files by pressing "Upgrade" button. It is not neсessery to add translation keys manually. Just put translate('needed_key') into your View or Controller and it will be inported. 

When you translate new keys or edit translated you have to recompile MO files by pressing "Compile" button. After that new translation will be available im your application.

When you press "Show" button you get to "Languages interface": 

There you can upgrede keys from your project, recompile them. Also you can new language file and  add a new message key for all languages.

If you have to translate any of PO files you have to press "Translate" button on certain language. After that you can see "Translation interface":

Here you can edit translation phrases. Also you if you have any doubts can mark translation phrase as "Fuzzy". Phrases marked as fuzzy don't appear on you application.



## Installation

* Add into your "require" section of composer.json:

    ```json
    "eugenzor/potool": "dev-master"
    ```
    
* run:
    
    ```bash
    php composer.phar update
    ```

* Activate module in your 'application.config.php' by adding 'Potool' into 'modules' section.
* Open user interface with calling http[s]://{application_root}/potool




