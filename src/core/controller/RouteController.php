<?php

namespace core\controller;

class RouteController {

    public function get_url_params (): array {
        return [
            'lang' => $_GET['lang'],
            'search' => $_GET['search'],
            'page' => $_GET['page'],
            'limit' => $_GET['limit'],
        ];
    }

    public function get_url_attrs (): array {
        $request_url_trimmed = ltrim( $_SERVER['REDIRECT_URL'], "/" );
        $request_array = explode( "/", $request_url_trimmed );
        unset($request_array[0]); // unset 'www/'
        $listed = array_values($request_array);
        $parsed = '/' . implode('/', $listed);
        $page = $listed[0];
        $detail = $listed[1] == 'detail';
        $id = $listed[2];

        return [
            'listed' => $listed,
            'parsed' => $parsed,
            'page' => $page,
            'is_detail' => $detail,
            'id' => $id,
        ];
    }

    public function get_language (): array {
        $languageList = CFG_WEB['global']['language']['list'];
        $defaultLanguage = CFG_WEB['global']['language']['default'];
        $params = self::get_url_params();
        $languageParameter = $params['lang'];
        $currentLanguage = $languageParameter ?? $defaultLanguage;
        $current = $langParam ?? $defaultLanguage;
        $urlParameter = $languageParameter ? 'lang=' . $current : '';

        return [
            'current' => $currentLanguage,
            'default' => $defaultLanguage,
            'list' => $languageList,
            'url_param' => $urlParameter,
        ];
    }

    public function get_route (): array {
        $attrs = self::get_url_attrs();
        $pageAttr = $attrs['page'];
        $pages = CFG_WEB['page'];

        if (!$pageAttr) {
            $template = $pages['home']['template'];
            $layout = 'default';
            $route = $pages['home'];
        } else if ($pages[$pageAttr]) {
            $template = $pages[$pageAttr]['template'];
            $layout = 'default';
            $route = $pages[$pageAttr];
        } else {
            $template = $pages['error']['template'];
            $layout = 'minimal';
            $route = $pages['error'];
        }

        return [
            'layout' => 'layout.' . $layout,
            'template' => 'page.' . $template,
            'route' => $route,
        ];
    }

}