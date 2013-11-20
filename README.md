Проект "CARGO"
==============

Веб-интерфейс проекта CARGO.

### Технологии:
1. Angular JS 1.2.x [Doc] (http://angularjs.org) [GitHub] (https://github.com/angular/angular.js)
2. Bootstrap 3 [Doc] (http://getbootstrap.com) [GitHub] (https://github.com/twbs/bootstrap/)
3. Angular Bootstrap UI [Doc] (http://angular-ui.github.io/bootstrap/) [GitHub] (https://github.com/angular-ui/bootstrap/tree/bootstrap3) - собрать билд для bootstrap 3, т.к. в мастере версия для Bootstrap 2

Для разработки фронтенда необходимо наладить процесс сборки.
Если разрабокрой фронтенда не заниматься, то можно этого не делать.

### Установка
---------

1. Установить [Node.JS] (http://nodejs.org)
2. Установить [Grunt] (http://gruntjs.com/getting-started)

    ```
    npm install -g grunt-cli
    npm install grunt --save-dev
    ```

3. Установить переменные окружения
(Выбрать что то одно, или dev или prod или manual, если вы не уверены - пропустите этот пункт)

Для Production машины:

    ```
    grunt prod --force
    ```

Для Test машины:

    ```
    grunt manual --force
    ```

Для Development машины:

    ```
    grunt dev --force
    ```

### Настройка среды для разработки
---------

Для продукции JetBrains (WebStorm, PhpStorm, etc):

1. Открыть настройки (Settings, Ctrl+Alt+S)
2. Во вкладке "Project Settings" выбрвть пунки "File Watchers"
3. Создать новый watcher для js (зелёный плюсик ADD, Alt+Ins), выбрать тип Custom или UglifyJs (разницы нет)

  * Name: `concat:js`
  * Description: `Compress .js files`

  * File Type: `JavsScript files`
  * Program: `путь до скрипта uglify_js в папке scripts проекта`
  * Working directory: `$FileDir$`
  * Output paths to refresh: `полный путь до /js/compiled/cargo.js`

  * После этого надо создать scope в этом же окошке:
  Напротив поля `Scope` нажать кнопку `...`.  Добавить новый Scope (зелёный плюсик, Ins)
  * Name: `dev_js`
  * Pattern: `file:site/public/js//*&&!file:site/public/js/compiled//*`

  Сохранить Scope, сохранить созданный watcher.

4. Аналогично создать новый watcher для css

  * Name: `concat:css`
  * Description: `Compress and minify .css files`

  * File Type:` Cascading style sheet`
  * Program: `путь до скрипта minify_css в папке scripts проекта`
  * Arguments: `$FileName$ -o $FileNameWithoutExtension$.min.css`
  * Working directory: `$FileDir$`
  * Output paths to refresh: `$FileNameWithoutExtension$.min.css`

  Добавляем новый Scope:
  * Name: `dev_css`
  * Pattern: `file:site/public/css//*&&!file:site/public/css/cargo.handheld.min.css&&!file:site/public/css/cargo.min.css`

Если используется другая среда разработки, или описаный выше способ не подходит, можно запускать команды для сборки из консоли:

    ```
    grunt cssmin:minify
    grunt concat:js
    ```

Или можно запустить wathcer'ы из консоли:

    ```
    grunt watch:js
    grunt watch:css
    ```

(Что бы консоль можно было при этом закрыть, а watcher продолжил работу, надо запускать с каким-то флагом, гугл в помощь)

### Проверка кода и тесты
---------

Перед тем, как сливать ветку в мастер следует проверить качество js-кода:

    ```
    grunt check_js
    ```

Если в консоле ошибки - поправить (ну, или спросить как быть)

Для запуска acceptance тестов веб-интерфейса:
запустить скрипт `run_web_tests` из папки `scripts`.
Или выполнить: `'protractor ./node_modules/protractor/referenceConf.js'`

Для запуска acceptance тестов в папке `site/tests/web-test/selenium`
должны лежать:
* [Chromedriver] (http://chromedriver.storage.googleapis.com/index.html)
* [selenium-server-standalone-2.37.0.jar] (https://selenium.googlecode.com/files/selenium-server-standalone-2.37.0.jar)

Для тестов используется [Protractor] (https://github.com/angular/protractor), в случаи чего смотри [Getting Started] (https://github.com/angular/protractor/blob/master/docs/getting-started.md)