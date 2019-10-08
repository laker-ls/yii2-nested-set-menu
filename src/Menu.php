<?php

namespace lakerLS\nestedSet;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Это расширение является виджетом для yii2. Используется для реализации динамической системы пунктов меню.
 * Выводит список пунктов меню используя дерево каталогов Nested Set. Формирование происходит средствами PHP
 * без дополнительных запросов к базе данных. Вложенность не ограничена. Подходит для любых шаблонов, есть возможность
 * указать любые параметры для всех тегов.
 */
class Menu extends Widget
{
    /**
     *  Передаем выборку из базы данных в которой лежат пункты меню.
     *  Обязательные поля в таблице: `id`, `lft`, `rgt`, `lvl`, `name`, `url`.
     *  Поле `url` должно содержать относительный путь. Полный путь будет сформирован.
     *
     *  @param object $allCategory
     */
    public $allCategories;

    /**
     *  Передаем все параметры для тегов. Передавать можно как `class`, так и любые атрибуты тега.
     *  Если ваше меню не будет иметь вложенность, то параметр `nested` нет необходимости указывать.
     *  ПРИМЕР:
     *
     *  'options' => [
     *      'main' => [
     *          'ul' => ['class' => 'navbar-nav mr-auto', 'style' => 'margin-top: 20px'],
     *          'lonely' => [
     *              'li' => ['class' => 'nav-item'],
     *              'a' => ['class' => 'nav-link'],
     *          ],
     *          'hasNesting' => [
     *              'li' => ['class' => 'nav-item dropdown'],
     *              'a' => ['class' => 'nav-link dropdown-toggle'],
     *              'icon' => 'fa fa-arrow-bottom'
     *          ],
     *          'active' => ['class' => 'active'],
     *      ],
     *      'nested' => [
     *          'ul' => ['class' => 'dropdown-menu', 'data-toggle' => 'example'],
     *          'lonely' => [
     *              'li' => ['class' => 'dropdown-item'],
     *              'a' => ['class' => 'dropdown-link'],
     *          ],
     *          'hasNesting' => [
     *              'li' => ['class' => 'dropdown-item dropdown'],
     *              'a' => ['class' => 'dropdown-link dropdown'],
     *              'icon' => 'fa fa-arrow-right'
     *          ],
     *          'active' => ['class' => 'active', 'id' => 'example']
     *      ],
     *  ],
     *
     *  `main` - меню первого уровня, не вложенный в какие-либо категории.
     *  `nested` - меню второго или ниже уровня, вложенное.
     *
     *  `lonely` - пункт меню, который НЕ имеет вложенных в него категорий.
     *  `hasNesting` - пункт меню, который имеет вложенные в него категории.
     *
     *  `active` - указаываем параметры для активного пункта меню, которые применятся к тегу `li`. Атрибуты наследуются
     *  от тега `li`, не нужно дублировать атрибуты в том числе и классы в параметре `active`.
     *
     *  Параметры для `ul`, `li`, `a`, `active` передаются массивом.
     *  Параметры для `icon` передаются строкой.
     *
     *  @var array
     */
    public $options = [];

    /**
     * Вывод пунктов меню.
     */
    public function run()
    {
        $lvl = 1;
        $count = 'first';

        echo Html::beginTag('ul', $this->ul('main')) . "\n";
        foreach ($this->allCategories as $category) {
            if ($lvl == $category->lvl && $count == 'some') {
                echo Html::endTag('li') . "\n";
            }
            if ($lvl < $category->lvl) {
                echo Html::beginTag('ul', $this->ul('nested', $category->lvl)) . "\n";
            }
            if ($lvl > $category->lvl) {
                for ($cycle = $lvl - $category->lvl; $cycle; $cycle--) {
                    echo Html::endTag('li') . "\n";
                    echo Html::endTag('ul') . "\n";
                }
            }
            echo Html::beginTag('li', $this->li($this->allCategories, $category)) . "\n";
            echo Html::tag('a', $category->name . $this->icon($category),
                    $this->a($this->allCategories, $category)) . "\n";

            $lvl = $category->lvl;
            $count = 'some';
        }
        echo Html::endTag('ul');
    }

    /**
     * Присваиваем параметры для тегов <a>.
     *
     * @param string $value
     * @param null|integer $lvl
     * @return array|bool
     */
    private function ul($value, $lvl = null)
    {
        if ($value == 'main') {
            $optionsTag = $this->val('main.ul');
        } elseif ($value == 'nested') {
            $optionsTag = $this->val('nested.ul');
        } else {
            $optionsTag = false;
        }
        return $optionsTag;
    }

    /**
     * Присваиваем параметры для тегов <li>.
     *
     * @param object $categories
     * @param object $category
     * @return array
     */
    private function li($categories, $category)
    {
        $request = Yii::$app->request;
        $url = $this->createUrl($categories, $category->id);
        $currentUrl = '/' . $request->pathInfo;
        $parentUrl = substr_count($request->absoluteUrl, $request->hostInfo . $url . '/');

        $main = $category->lvl == 1;
        $notNesting = $category->lft + 1 == $category->rgt;
        $active = $parentUrl || $url == $currentUrl || '/index' == $currentUrl;

        if ($notNesting) {
            if ($main) {
                if ($active) {
                    $optionsTag = $this->glueArray($this->val('main.lonely.li'), $this->val('main.active'));
                } else {
                    $optionsTag = $this->val('main.lonely.li');
                }
            } else {
                if ($active) {
                    $optionsTag = $this->glueArray($this->val('nested.lonely.li'), $this->val('nested.active'));
                } else {
                    $optionsTag = $this->val('nested.lonely.li');
                }

            }
        } else {
            if ($main) {
                if ($active) {
                    $optionsTag = $this->glueArray($this->val('main.hasNesting.li'), $this->val('main.active'));
                } else {
                    $optionsTag = $this->val('main.hasNesting.li');
                }
            } else {
                if ($active) {
                    $optionsTag = $this->glueArray($this->val('nested.hasNesting.li'), $this->val('nested.active'));
                } else {
                    $optionsTag = $this->val('nested.hasNesting.li');
                }
            }
        }
        return $optionsTag;
    }

    /**
     * Склеиваем параметры у элементов с одинаковым ключом.
     * К примеру $LI_active_main наследует параметры от $LI_has_nesting_main.
     *
     * @param array $main Основные атрибуты пункта меню.
     * @param array $active Атрибуты пункта меню, если активен.
     * @return array
     */
    private function glueArray($main, $active)
    {
        $optionsTag = [];

        $mainKey = array_keys($main);
        $activeKey = array_keys($active);
        $sumArr = array_merge(array_flip($mainKey), array_flip($activeKey));
        foreach ($sumArr as $key => $notNeed) {
            $optionsTag[$key] = ArrayHelper::getValue($main, $key) . ' ' . ArrayHelper::getValue($active, $key);
        }
        return $optionsTag;
    }

    /**
     * Присваиваем параметры и url'ы для тегов <a>.
     *
     * @param object $categorys
     * @param object $category
     * @return array
     */
    private function a($categorys, $category)
    {
        $createUrl = $this->createUrl($categorys, $category->id);

        $test = ArrayHelper::getValue($this->options, 'main.hasNesting.a');

        if ($category->lft + 1 == $category->rgt) {
            if ($category->lvl == 1) {
                $optionsTag = array_merge($this->val('main.lonely.a'), ['href' => $createUrl]);
            } else {
                $optionsTag = array_merge($this->val('nested.lonely.a'), ['href' => $createUrl]);
            }
        } else {
            if ($category->lvl == 1) {
                $optionsTag = array_merge($this->val('main.hasNesting.a'), ['href' => $createUrl]);
            } else {
                $optionsTag = array_merge($this->val('nested.hasNesting.a'), ['href' => $createUrl]);
            }
        }
        return $optionsTag;
    }

    /**
     * Присваеваем иконку для тега <a>, если пункт меню имеет вложенность.
     *
     * @param object $category
     * @return string
     */
    private function icon($category)
    {
        $main = $category->lvl == 1;
        $nesting = $category->lft + 1 != $category->rgt;

        $icon = null;
        if ($nesting) {
            if ($main) {
                $icon = ArrayHelper::getValue($this->options, 'main.hasNesting.icon');
            } else {
                $icon = ArrayHelper::getValue($this->options, 'nested.hasNesting.icon');
            }
        }

        if (!empty($icon)) {
            $prettyIcon = '<i class="' . $icon . '"></i>';
        } else {
            $prettyIcon = null;
        }

        return $prettyIcon;
    }

    /**
     * Составление полного адреса для пунктов меню.
     *
     * @param object $categorys
     * @param integer $id
     * @return array
     */
    private function createUrl($categorys, $id)
    {
        $previous = $categorys['0'];
        $path = null;
        $array = null;

        foreach ($categorys as $current) {
            if ($current->lft > $previous->lft && $current->rgt < $previous->rgt) {
                $path = $path . '/' . $previous->url;
            } elseif ($current->lvl != $previous->lvl && $current->lvl == '1') {
                $path = null;
            } elseif ($current->lvl != $previous->lvl) {
                $path = explode('/', $path);
                for ($cycle = $previous->lvl - $current->lvl; $cycle; $cycle--) {
                    array_pop($path);
                }
                $path = implode('/', $path);
            }
            if ($current->url != '/') {
                $finish_url = $path . '/' . $current->url;
            } else {
                $finish_url = '/';
            }
            $previous = $current;
            $array[$current->id] = $finish_url;
        }
        return $array[$id];
    }

    /**
     * Функция, для получения значений массива options. Сокращенный синтаксис ArrayHelper'а.
     *
     * @param $key
     * @return mixed
     */
    private function val($key)
    {
        return ArrayHelper::getValue($this->options, $key, []);
    }
}