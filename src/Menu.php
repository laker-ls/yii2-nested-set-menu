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
     * Передаем выборку из базы данных в которой лежат пункты меню.
     * Обязательные поля в таблице: id, lft, rgt, lvl, name, url.
     * Поле url должно содержать относительный путь. Полный путь будет сформирован.
     * @param object $allCategory
     */
    public $allCategories;

    /**
     * В параметрах идущих ниже необходимо указать все необходимые классы для тегов <ul>, <li>, <a>.
     */

    // оборачивает всю конструкцию
    public $UL_all = [];

    // Пункт меню вложенный на один уровень.
    public $UL_nested_one = [];

    // Пункт меню вложенный на два и более уровней.
    public $UL_nested_more = [];

    // Пункт меню без вложеностей, корневой.
    public $LI_lonely_main = [];
    public $A_lonely_main = [];

    // Пункт меню с вложенностями, корневой.
    public $LI_has_nesting_main = [];
    public $A_has_nesting_main = [];

    // Пункт меню вложенный, не содержит вложеностей.
    public $LI_lonely = [];
    public $A_lonely = [];

    // Пункт меню вложенный, содержит вложенности.
    public $LI_has_nesting = [];
    public $A_has_nesting = [];

    // Активный пункт меню, корневой. Указывать только дополнительные параметры необходимые для активного пункта,
    // всё остальное будет наследовано от $LI_has_nesting_main.
    public $LI_active_main = [];

    // Активный пункт меню, вложенный. Указывать только дополнительные параметры необходимые для активного пункта,
    // всё остальное будет наследовано от $LI_has_nesting.
    public $LI_active = [];

    // Добавить иконку к корневому пункту меню, который имеет вложенности.
    // Необходимо передать строкой классы иконки.
    public $A_icon_has_nesting_main = '';

    // Добавить иконку к вложенному пункту меню, который так же имеет вложенности.
    // Необходимо передать строкой классы иконки.
    public $A_icon_has_nesting = '';

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
            echo Html::tag('a', $category->name . '<i class="' . $this->icon($category) . '"></i>',
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
            $optionsTag = $this->UL_all;
        } elseif ($value == 'nested' && $lvl >= 3) {
            $optionsTag = $this->UL_nested_more;
        } elseif ($value == 'nested') {
            $optionsTag = $this->UL_nested_one;
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
                    $optionsTag = $this->glueArray($this->LI_lonely_main, $this->LI_active_main);
                } else {
                    $optionsTag = $this->LI_lonely_main;
                }
            } else {
                if ($active) {
                    $optionsTag = $this->glueArray($this->LI_lonely, $this->LI_active);
                } else {
                    $optionsTag = $this->LI_lonely;
                }

            }
        } else {
            if ($main) {
                if ($active) {
                    $optionsTag = $this->glueArray($this->LI_has_nesting_main, $this->LI_active_main);
                } else {
                    $optionsTag = $this->LI_has_nesting_main;
                }
            } else {
                if ($active) {
                    $optionsTag = $this->glueArray($this->LI_has_nesting, $this->LI_active);
                } else {
                    $optionsTag = $this->LI_has_nesting;
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

        if ($category->lft + 1 == $category->rgt) {
            if ($category->lvl == 1) {
                $optionsTag = array_merge($this->A_lonely_main, ['href' => $createUrl]);
            } else {
                $optionsTag = array_merge($this->A_lonely, ['href' => $createUrl]);
            }
        } else {
            if ($category->lvl == 1) {
                $optionsTag = array_merge($this->A_has_nesting_main, ['href' => $createUrl]);
            } else {
                $optionsTag = array_merge($this->A_has_nesting, ['href' => $createUrl]);
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
                $icon = $this->A_icon_has_nesting_main;
            } else {
                $icon = $this->A_icon_has_nesting;
            }
        }
        return $icon;
    }

    /**
     * Составление полного адреса для пунктов меню.
     *
     * @param object $categorys
     * @param integer $id
     * @return array
     */
    public function createUrl($categorys, $id)
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
}