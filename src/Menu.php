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

    // Добавить иконку к пункту меню, который имеет вложенность.
    public $A_icon_in_name = 'ui--caret fontawesome-angle-down px18';

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
     * @param object $categorys
     * @param object $category
     * @return array
     */
    private function li($categorys, $category)
    {
        $request = Yii::$app->request;
        $pathInfo = '/' . $request->pathInfo;
        $createUrl = $this->createUrl($categorys, $category->id);
        $substrCount = substr_count($request->absoluteUrl, $request->hostInfo . $createUrl . '/');

        if (!empty($substrCount) || $pathInfo == $createUrl || $pathInfo == '/index') {
            if ($category->lvl == 1) {
                $optionsTag = $this->glueArray($this->LI_active_main, $this->LI_has_nesting_main);
            } else {
                $optionsTag = $this->glueArray($this->LI_active, $this->LI_has_nesting);
            }
        } elseif ($category->lft + 1 == $category->rgt) {
            if ($category->lvl == 1) {
                $optionsTag = $this->LI_lonely_main;
            } else {
                $optionsTag = $this->LI_lonely;
            }
        } else {
            if ($category->lvl == 1) {
                $optionsTag = $this->LI_has_nesting_main;
            } else {
                $optionsTag = $this->LI_has_nesting;
            }
        }
        return $optionsTag;
    }

    /**
     * Склеиваем параметры у элементов с одинаковым ключом.
     * К примеру $LI_active_main наследует параметры от $LI_has_nesting_main.
     */
    private function glueArray($firstArray, $secondArray)
    {
        $optionsTag = [];

        $oneArr = array_keys($firstArray);
        $twoArr = array_keys($secondArray);
        $sumArr = array_merge(array_flip($oneArr), array_flip($twoArr));
        foreach ($sumArr as $key => $notNeed) {
            $optionsTag[$key] = ArrayHelper::getValue($this->LI_active_main, $key) . ' '
                . ArrayHelper::getValue($this->LI_has_nesting_main, $key);
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
        $nameTag = null;
        if ($category->lft + 1 != $category->rgt) {
            $nameTag = $this->A_icon_in_name;
        }
        return $nameTag;
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