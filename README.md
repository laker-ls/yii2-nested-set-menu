<h1 align="center">
    yii2-nested-set-menu
</h1>


[![Stable Version](https://poser.pugx.org/laker-ls/yii2-nested-set-menu/v/stable)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![Unstable Version](https://poser.pugx.org/laker-ls/yii2-nested-set-menu/v/unstable)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![License](https://poser.pugx.org/laker-ls/yii2-nested-set-menu/license)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![Total Downloads](https://poser.pugx.org/laker-ls/yii2-nested-set-menu/downloads)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)

Это расширение является виджетом для yii2. Используется для реализации динамической системы пунктов меню.
Выводит список пунктов меню, используя дерево каталогов Nested Set. Формирование происходит средствами PHP без дополнительных запросов к базе данных.
Вложенность не ограничена. Подходит для любых шаблонов, есть возможность указать `class` для всех тегов.

## Установка

Рекомендуемый способ установки этого расширения является использование [composer](http://getcomposer.org/download/).
Проверьте [composer.json](https://github.com/laker-ls/yii2-nested-set-menu/blob/master/composer.json) на предмет требований и зависимостей данного расширения.

Для установки запустите

```
$ php composer.phar require laker-ls/yii2-nested-set-menu "~1.0.0"
```

или добавьте в `composer.json` в раздел `require` следующую строку

```
"laker-ls/yii2-nested-set-menu": "~1.0.0"
```

> Смотрите [список изменений](https://github.com/laker-ls/yii2-nested-set-menu/blob/master/CHANGE.md) для подробной информации о версиях.

## Использование

Обязательный параметр `allCategories`, должен быть объектом и содержать в себе выборку из базы данных.
Обязательными полями в базе данных являются: `id`, `lft`, `rgt`, `lvl`, `name`, `url`.

Остальные параметры являются не обязательными и используются для указания `class` тегов. Подробнее о каждом параметре смотрите в `src/Menu.php`.

```php
use lakerLS\nestedSet\Menu;

echo Menu::widget([
                    'allCategories' => $сategory,

                    'UL_all' => 'nav navbar-nav navbar-right',
                    'UL_nested_one' => 'dropdown-menu',
                    'UL_nested_more' => 'dropdown-menu',

                    'LI_lonely_main' => false,
                    'A_lonely_main' => 'border',

                    'LI_has_nesting_main' => 'dropdown',
                    'A_has_nesting_main' => 'dropdown-toggle border',

                    'LI_lonely' => false,
                    'A_lonely' => false,

                    'LI_has_nesting' => 'dropdown',
                    'A_has_nesting' => 'dropdown-toggle',

                    'LI_active_main' => 'active',
                    'LI_active' => 'activeNestedMenu',
                ]);
```

## Лицензия

**yii2-nested-set-menu** выпущено по лицензии BSD-3-Clause. Ознакомиться можно в файле `LICENSE.md`.
