<?php

namespace core;

use core\controller\RouteController;
use eftec\bladeone\BladeOne;

class Page {

    function __construct () {
        $this -> $blade = new BladeOne(
            TEMPLATE_ROOT_PATH,
            "compiled"
        );
    }


    private function get_content (): array {
        $rc = new RouteController;
        $route = $rc -> get_route();
        $language = $rc -> get_language();
        $lang = $language['current'];
        $sections_raw = $route['route']['content']['section'];
        $sections = [];

        foreach ($sections_raw as $item) {
            $sections[] = [
                'key' => $item['key'],
                'title' => $item['title'][$lang],
                'html' => $item['html'][$lang],
            ];
        }

        return [
            'key' => $route['route']['key'],
            'title' => $route['route']['content']['title'][$lang],
            'html' => $route['route']['content']['html'][$lang],
            'section' => $sections,
            'lang' => $lang,
        ];
    }

    private function get_footer (): array {
        $rc = new RouteController;
        $route = $rc -> get_route();

        return [];
    }

    private function get_menu_items () {

    }

    private function get_language_link_path ($path) {
        $rc = new RouteController;
        $language = $rc -> get_language();
        $param = $language['url_param'];
        $final = $path;
        if ($param) $final = $path . '?' . $param;

        return $final;
    }

    public function get_meta (): array {
        $rc = new RouteController;
        $language = $rc -> get_language();
        $lang = $language['current'];
        $route = $rc -> get_route();
        $global = CFG_WEB['global']['meta'];
        $meta = [
            'title' => $global['title'],
            'description' => $global['description'],
            'keywords' => $global['keywords'],
            'robots' => $global['robots'],
            'charset' => $global['charset'],
            'viewport' => $global['viewport'],
            'lang' => $lang,
            'author' => $global['description'],
            'url' => CFG_ENV['root'],
            'og:url' => $_SERVER['REDIRECT_URL'],
        ];
        if ($route['route']['meta']) {
            if ($route['route']['meta']['title'][$lang]) $meta['title'] = $route['route']['meta']['title'][$lang] . ' | ' . $meta['title'];
            if ($route['route']['meta']['description'][$lang]) $meta['description'] = $route['route']['meta']['description'][$lang];
            if ($route['route']['meta']['keywords']) $meta['keywords'] = $route['route']['meta']['keywords'];
            if ($route['route']['meta']['robots']) $meta['robots'] = $route['route']['meta']['robots'];
        }
        // TODO: detail

        return $meta;
    }

    public function get_scripts (): array {
        return [
            'head' => [
                'rest' => CFG_WEB['global']['head']['scripts'],
            ],
            'body' => [
                'main' => CFG_ENV['scripts'] . '?v=' . TIMESTAMP,
                'rest' => CFG_WEB['global']['body']['scripts'],
            ],
        ];
    }

    public function get_styles (): array {
        return [
            'head' => [
                'main' => CFG_ENV['styles'] . '?v=' . TIMESTAMP,
                'rest' => CFG_WEB['global']['head']['styles'],
            ],
            'body' => [
                'rest' => CFG_WEB['global']['body']['styles'],
            ],
        ];
    }

    public function render () {
        $rc = new RouteController;
        $route = $rc -> get_route();
        $language = $rc -> get_language();
        $lang = $language['current'];

        $pageData = [
            'template' => $route['template'],
            'route' => $route['route'],
            'content' => self::get_content(),
            'footer' => self::get_footer(),
            'language' => $language,
            'lang' => $lang,
            'public_data' => DATA_JSON,
            'languageLink' => function ($path) { return self::get_language_link_path($path); },
        ];

        echo $this -> $blade -> run(
            $route['layout'],
            $pageData,
        );
    }

}