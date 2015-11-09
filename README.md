# Gettext editor module for Zend Framework

Potool is a tool for web-based edition Gettext translation files. It is easy to integrate into ZF2 projects. Translator would be able to translate, edit phrases online and compile MO files for the application of results. Potool is designed using Bootstrap css framework.

## Overview
Here you can work with all internationalization files of a certain module. If you have added some new translation keys into your module, you have to import new translation keys into PO files by pressing "Upgrade" button. There is no need to add translation keys manually. Just put translate('needed_key') into your View or Controller code and it will be imported.

When you translate new keys or edit translated ones, you have to recompile MO files by pressing "Compile" button. After that new translation will be available in your application.

When you press "Show" button you get to "Languages interface":
![Languages interface](https://raw.githubusercontent.com/eugenzor/Potool/master/docs/images/potool_languages.png)

There you can upgrade keys from your project, recompile them. Also you can add new language file and a new message key for all languages.

If you have to translate any of PO files you have to press "Translate" button on certain language. After that you can see "Translation interface":

![Translation interface](https://raw.githubusercontent.com/eugenzor/Potool/master/docs/images/potool_translation.png)
Here you can edit translated phrases. Also, if you have any doubts, you can mark translated phrase as "Fuzzy". Phrases marked as fuzzy do not appear in your application.



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




