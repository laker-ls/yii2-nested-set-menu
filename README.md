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
$ php composer.phar require laker-ls/yii2-nested-set-menu "~2.1.0"
```

или добавьте в `composer.json` в раздел `require` следующую строку

```
"laker-ls/yii2-nested-set-menu": "~2.1.0"
```

> Смотрите [список изменений](https://github.com/laker-ls/yii2-nested-set-menu/blob/master/CHANGE.md) для подробной информации о версиях.

## Использование

Обязательный параметр `allCategories`, должен быть массивом, который содержит объекты категорий.
Обязательными полями в базе данных являются: `id`, `lft`, `rgt`, `lvl`, `name`, `url`.

> ВНИМАНИЕ: элементы массива должны быть отсортированы по полю `alt` по возрастанию.

Остальные параметры являются не обязательными и используются для указания атрибутов тегам, к примеру `class`, `style` и другие.
Для того, что бы присвоить к вложенному пункту меню иконку, передайте строкой классы иконки.

Пример использования виджета с вложенными пунктами меню:
```php
use lakerLS\nestedSet\Menu;
           
echo Menu::widget([
    'allCategories' => $allCategory,
    'options' => [
        'main' => [
            'ul' => ['class' => 'navbar-nav mr-auto', 'style' => 'margin-top: 20px'],
            'lonely' => [
                'li' => ['class' => 'nav-item'],
                'a' => ['class' => 'nav-link'],
            ],
            'hasNesting' => [
                'li' => ['class' => 'nav-item dropdown'],
                'a' => ['class' => 'nav-link dropdown-toggle'],
                'icon' => 'fa fa-arrow-bottom'
            ],
            'active' => [
                'li' => ['class' => 'active'],
                'a' => ['class' => 'maybe-necessary-a-instead-of-li',
            ]
        ],
        'nested' => [
            'ul' => ['class' => 'dropdown-menu', 'data-toggle' => 'example'],
            'lonely' => [
                'li' => ['class' => 'dropdown-item'],
                'a' => ['class' => 'dropdown-link'],
            ],
            'hasNesting' => [
                'li' => ['class' => 'dropdown-item dropdown'],
                'a' => ['class' => 'dropdown-link dropdown'],
                'icon' => 'fa fa-arrow-right'
            ],
            'active' => [
                'li' => ['class' => 'active'],
                'a' => ['class' => 'maybe-necessary-a-instead-of-li',
            ]
        ],
    ],
]);
```

Пример использования виджета без вложенных пунктов меню:

```php
use lakerLS\nestedSet\Menu;
           
echo Menu::widget([
    'allCategories' => $allCategory,
    'options' => [
        'main' => [
            'ul' => ['class' => 'navbar-nav mr-auto', 'style' => 'margin-top: 20px'],
            'lonely' => [
                'li' => ['class' => 'nav-item'],
                'a' => ['class' => 'nav-link'],
            ],
            'hasNesting' => [
                'li' => ['class' => 'nav-item dropdown'],
                'a' => ['class' => 'nav-link dropdown-toggle'],
                'icon' => 'fa fa-arrow-bottom'
            ],
            'active' => [
                'li' => ['class' => 'active'],
            ]
        ],
    ],
]);
```

`main` - меню первого уровня, не вложенное в какие-либо категории. <br />
`nested` - меню второго или ниже уровня, вложенное.

`lonely` - пункт меню, который НЕ имеет вложенных в него категорий. <br />
`hasNesting` - пункт меню, который имеет вложенные в него категории.

`active` - указываем дополнительные параметры для активного пункта меню, которые применятся к тегу `li` и `a`.
Основные параметры наследуются.

Параметры для `ul`, `li`, `a`, `active` передаются массивом. <br />
Параметры для `icon` передаются строкой.

## Лицензия

**yii2-nested-set-menu** выпущено по лицензии BSD-3-Clause. Ознакомиться можно в файле `LICENSE.md`.
