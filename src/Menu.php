<?php

namespace lakerLS\nestedSet;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Это расширение является виджетом для yii2. Используется для реализации динамической системы пунктов меню.
 * Выводит список пунктов меню используя дерево каталогов Nested Set. Формирование происходит средствами PHP
 * без дополнительных запросов к базе данных. Вложенность не ограничена. Подходит для любых шаблонов, есть возможность
 * указать class для всех тегов.
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
    public $UL_all = 'nav navbar-nav navbar-right';

    // Пункт меню вложенный на один уровень.
    public $UL_nested_one = 'dropdown-menu';

    // Пункт меню вложенный на два и более уровней.
    public $UL_nested_more = 'dropdown-menu';

    // Пункт меню без вложеностей, корневой.
    public $LI_lonely_main = '';
    public $A_lonely_main = '';

    // Пункт меню с вложенностями, корневой.
    public $LI_has_nesting_main = 'dropdown';
    public $A_has_nesting_main = 'dropdown-toggle';

    // Пункт меню вложенный, не содержит вложеностей.
    public $LI_lonely = '';
    public $A_lonely = '';

    // Пункт меню вложенный, содержит вложенности.
    public $LI_has_nesting = 'dropdown-submenu';
    public $A_has_nesting = '';

    // Активный пункт меню, корневой.
    public $LI_active_main = 'active';

    // Активный пункт меню, вложенный.
    public $LI_active = 'active';

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
            echo Html::tag('a', $category->name, $this->a($this->allCategories, $category)) . "\n";

            $lvl = $category->lvl;
            $count = 'some';
        }
        echo Html::endTag('ul');
    }

    /**
     * Присваиваем классы для тегов <a>.
     *
     * @param string $value
     * @param null|integer $lvl
     * @return array|bool
     */
    private function ul($value, $lvl = null)
    {
        if ($value == 'main') {
            $optionsTag = ['class' => $this->UL_all];
        } elseif ($value == 'nested' && $lvl >= 3) {
            $optionsTag = ['class' => $this->UL_nested_more];
        } elseif ($value == 'nested') {
            $optionsTag = ['class' => $this->UL_nested_one];
        } else {
            $optionsTag = false;
        }
        return $optionsTag;
    }

    /**
     * Присваиваем классы для тегов <li>.
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
                $optionsTag = ['class' => $this->LI_active_main . ' ' . $this->LI_has_nesting_main];
            } else {
                $optionsTag = ['class' => $this->LI_active . ' ' . $this->LI_has_nesting];
            }
        } elseif ($category->lft + 1 == $category->rgt) {
            if ($category->lvl == 1) {
                $optionsTag = ['class' => $this->LI_lonely_main];
            } else {
                $optionsTag = ['class' => $this->LI_lonely];
            }
        } else {
            if ($category->lvl == 1) {
                $optionsTag = ['class' => $this->LI_has_nesting_main];
            } else {
                $optionsTag = ['class' => $this->LI_has_nesting];
            }
        }
        return $optionsTag;
    }

    /**
     * Присваиваем классы и url'ы для тегов <a>.
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
                $optionsTag = ['class' => $this->A_lonely_main, 'href' => $createUrl];
            } else {
                $optionsTag = ['class' => $this->A_lonely, 'href' => $createUrl];
            }
        } else {
            if ($category->lvl == 1) {
                $optionsTag = ['class' => $this->A_has_nesting_main, 'href' => $createUrl];
            } else {
                $optionsTag = ['class' => $this->A_has_nesting, 'href' => $createUrl];
            }
        }
        return $optionsTag;
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