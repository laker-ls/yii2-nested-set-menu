<h1 align="center">
    yii2-nested-set-menu
</h1>


[![Stable Version](https://poser.pugx.org/laker-ls/yii2-nested-set-menu/v/stable)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![Unstable Version](https://poser.pugx.org/laker-ls/yii2-nested-set-menu/v/unstable)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![License](https://poser.pugx.org/laker-ls/yii2-nested-set-menu/license)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![Total Downloads](https://poser.pugx.org/laker-ls/yii2-nested-set-menu/downloads)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)

Это расширение является виджетом для yii2. Используется для реализации динамической системы пунктов меню.
Выводит список пунктов меню, используя дерево каталогов Nested Set. Формирование происходит средствами PHP без дополнительных запросов к базе данных.
Вложенность не ограничена. Подходит для любых шаблонов.

Есть возможность указать любые атрибуты для всех тегов, к примеру `class`, `style` и другие, так же есть возможность указать иконку для пунтка меню, который имеет вложенности.

## Установка

Рекомендуемый способ установки этого расширения является использование [composer](http://getcomposer.org/download/).
Проверьте [composer.json](https://github.com/laker-ls/yii2-nested-set-menu/blob/master/composer.json) на предмет требований и зависимостей данного расширения.

Для установки запустите

```
$ php composer.phar require laker-ls/yii2-nested-set-menu "~1.1.1"
```

или добавьте в `composer.json` в раздел `require` следующую строку

```
"laker-ls/yii2-nested-set-menu": "~1.1.1"
```

> Смотрите [список изменений](https://github.com/laker-ls/yii2-nested-set-menu/blob/master/CHANGE.md) для подробной информации о версиях.

## Использование

Обязательный параметр `allCategories`, должен быть объектом и содержать в себе выборку из базы данных.
Обязательными полями в базе данных являются: `id`, `lft`, `rgt`, `lvl`, `name`, `url`.

Остальные параметры являются не обязательными и используются для указания атрибутов тегам, к примеру `class`, `style` и другие.
Для того, что бы присвоить к вложенному пункту меню иконку, передайте строкой классы иконки. Подробнее о каждом параметре смотрите в `src/Menu.php`.

```php
use lakerLS\nestedSet\Menu;
           
echo Menu::widget([
    'allCategories' => $allCategory,

    'UL_all' => [
        'class' => 'sf-menu clearfix unstyled-all',
        'id' => 'header-navigation',
    ],
    'UL_nested_one' => [
        'class' => 'sub-menu',
        'style' => 'display: none',
    ],
    'UL_nested_more' => [
        'class' => 'sub-menu',
        'style' => 'display: none',
    ],
    'LI_lonely_main' => [
        'class' => 'menu-item',
    ],
    'LI_has_nesting_main' => [
        'class' => 'menu-item menu-item-type',
    ],
    'LI_lonely' => [
        'class' => 'menu-item-type-post_type',
    ],
    'A_lonely' => [
            'class' => 'sf-with-ul',
    ],
    
    'A_icon_has_nesting_main' => 'angle-down',
    'A_icon_has_nesting' => 'angle-right',
]);
```

## Лицензия

**yii2-nested-set-menu** выпущено по лицензии BSD-3-Clause. Ознакомиться можно в файле `LICENSE.md`.
