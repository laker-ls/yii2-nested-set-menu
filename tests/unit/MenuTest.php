<?php
namespace lakerLS\nestedSet\tests\unit;

use Codeception\Test\Unit;
use lakerLS\nestedSet\Menu;
use Yii;
use Exception;

class MenuTest extends Unit
{
    public function _before()
    {
        Yii::$app->request->pathInfo = '/';
        Yii::$app->request->hostInfo = 'http://nested-set-menu-test/';
        Yii::$app->request->url = '/';

        parent::_before();
    }

    public function testRunException()
    {
        $this->expectException(Exception::class);
        Menu::widget([
            'allCategories' => $this->allCategories(),
            'beginLvl' => 0,
        ]);
    }

    public function testRunWithMinimalParameters()
    {
        $menu = Menu::widget([
            'allCategories' => $this->allCategories(),
        ]);
        $this->assertStringContainsString($this->correctResult(), $menu);
    }

    public function testRunWithOptions()
    {
        $menuWithOptions = Menu::widget([
            'allCategories' => $this->allCategories(),
            'options' => $this->options(),
        ]);
        $this->assertStringContainsString($this->correctResultWithOptions(), $menuWithOptions);
    }

    public function testRunWithDeprecatedMethods()
    {
        $menuWithOptionsDeprecated = Menu::widget([
            'allCategories' => $this->allCategories(),
            'options' => $this->options(true),
        ]);
        $this->assertStringContainsString($this->correctResultWithOptions(true), $menuWithOptionsDeprecated);
    }

    private function correctResult()
    {
        ob_start();

        echo '<ul>' . PHP_EOL;
            echo '<li><a href="/">Главная</a></li>' . PHP_EOL;
            echo '<li>';
                echo '<a href="/technics">Техника</a>' . PHP_EOL;
                echo '<ul>' . PHP_EOL;
                    echo '<li>';
                        echo '<a href="/technics/ekskavator">Экскаваторы</a>' . PHP_EOL;
                        echo '<ul>' . PHP_EOL;
                            echo '<li><a href="/technics/ekskavator/frontalnyiy_pogruzchik">Фронтальные погрузчики</a></li>' . PHP_EOL;
                        echo '</ul>' . PHP_EOL;
                    echo '</li>' . PHP_EOL;
                    echo '<li><a href="/technics/ekskavator_pogruzchiki">Экскаваторы-погрузчики</a></li>' . PHP_EOL;
                echo '</ul>' . PHP_EOL;
            echo '</li>' . PHP_EOL;
            echo '<li><a href="/video">Видео</a></li>' . PHP_EOL;
            echo '<li><a href="/price-list">Стоимость услуг</a></li>' . PHP_EOL;
            echo '<li><a href="/contacts">Контакты</a></li>' . PHP_EOL;
        echo '</ul>' . PHP_EOL;

        return ob_get_clean();
    }

    private function correctResultWithOptions($deprecatedMode = false)
    {
        if ($deprecatedMode) {
            $tag_a_active = null;
        } else {
            $tag_a_active = ' maybe-necessary-a-instead-of-li';
        }

        ob_start();

        echo '<ul class="main-ul" style="margin-top: 20px">' . PHP_EOL;
            echo '<li class="nav-item active">';
                echo '<a class="nav-link' . $tag_a_active . '" href="/">Главная</a>';
            echo '</li>' . PHP_EOL;
            echo '<li class="nav-item dropdown">';
                echo '<a class="nav-link dropdown-toggle" href="/technics">Техника<i class="fa fa-arrow-bottom"></i></a>' . PHP_EOL;
                echo '<ul class="dropdown-menu" data-toggle="example">' . PHP_EOL;
                    echo '<li class="dropdown-item dropdown">';
                        echo '<a class="dropdown-link dropdown" href="/technics/ekskavator">Экскаваторы<i class="fa fa-arrow-right"></i></a>' . PHP_EOL;
                        echo '<ul class="dropdown-menu" data-toggle="example">' . PHP_EOL;
                            echo '<li class="dropdown-item">';
                            echo '<a class="dropdown-link" href="/technics/ekskavator/frontalnyiy_pogruzchik">Фронтальные погрузчики</a>';
                            echo '</li>' . PHP_EOL;
                        echo '</ul>' . PHP_EOL;
                        echo '</li>' . PHP_EOL;
                    echo '<li class="dropdown-item">';
                    echo '<a class="dropdown-link" href="/technics/ekskavator_pogruzchiki">Экскаваторы-погрузчики</a>';
                    echo '</li>' . PHP_EOL;
                echo '</ul>' . PHP_EOL;
            echo '</li>' . PHP_EOL;
            echo '<li class="nav-item">';
                echo '<a class="nav-link" href="/video">Видео</a></li>' . PHP_EOL;
            echo '<li class="nav-item">';
                echo '<a class="nav-link" href="/price-list">Стоимость услуг</a>';
            echo '</li>' . PHP_EOL;
            echo '<li class="nav-item">';
                echo '<a class="nav-link" href="/contacts">Контакты</a>';
            echo '</li>' . PHP_EOL;
        echo '</ul>' . PHP_EOL;

        return ob_get_clean();
    }

    private function options($deprecatedMode = false)
    {
        if ($deprecatedMode) {
            $active = ['class' => 'active'];
        } else {
            $active = [
                'li' => ['class' => 'active'],
                'a' => ['class' => 'maybe-necessary-a-instead-of-li'],
            ];
        }

        return [
            'main' => [
                'ul' => ['class' => 'main-ul', 'style' => 'margin-top: 20px'],
                'lonely' => [
                    'li' => ['class' => 'nav-item'],
                    'a' => ['class' => 'nav-link'],
                ],
                'hasNesting' => [
                    'li' => ['class' => 'nav-item dropdown'],
                    'a' => ['class' => 'nav-link dropdown-toggle'],
                    'icon' => 'fa fa-arrow-bottom'
                ],
                'active' => $active
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
                'active' => $active,
            ],
        ];
    }

    private function allCategories()
    {
        $arrays = array(
            array(
                "id" => 2,
                "lft" => 2,
                "rgt" => 3,
                "lvl" => 1,
                "name" => "Главная",
                "url" => "/",
            ),
            array(
                "id" => 3,
                "lft" => 4,
                "rgt" => 11,
                "lvl" => 1,
                "name" => "Техника",
                "url" => "technics",
            ),
            array(
                "id" => 4,
                "lft" => 5,
                "rgt" => 8,
                "lvl" => 2,
                "name" => "Экскаваторы",
                "url" => "ekskavator",
            ),
            array(
                "id" => 5,
                "lft" => 6,
                "rgt" => 7,
                "lvl" => 3,
                "name" => "Фронтальные погрузчики",
                "url" => "frontalnyiy_pogruzchik",
            ),
            array(
                "id" => 6,
                "lft" => 9,
                "rgt" => 10,
                "lvl" => 2,
                "name" => "Экскаваторы-погрузчики",
                "url" => "ekskavator_pogruzchiki",
            ),
            array(
                "id" => 7,
                "lft" => 12,
                "rgt" => 13,
                "lvl" => 1,
                "section_id" => NULL,
                "name" => "Видео",
                "url" => "video",
            ),
            array(
                "id" => 8,
                "lft" => 14,
                "rgt" => 15,
                "lvl" => 1,
                "section_id" => NULL,
                "name" => "Стоимость услуг",
                "url" => "price-list",
            ),
            array(
                "id" => 9,
                "lft" => 16,
                "rgt" => 17,
                "lvl" => 1,
                "section_id" => NULL,
                "name" => "Контакты",
                "url" => "contacts",
            ),
        );

        $object = null;
        foreach($arrays as $array) {
            $object[] = (object)$array;
        }

        return $object;
    }
}
