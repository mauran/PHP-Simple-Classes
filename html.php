<?php
class HTML {

    static private string $charset = 'UTF-8';
    static private string $title = '';
    static private string $title_end = '';
    static private string $description = '';
    static private string $viewport = 'width=device-width, initial-scale=1.0';
    static private string $canonical = '';
    static private array $robots = ['index', 'follow', 'max-image-preview:large'];

    static private array $meta = [];
    static private array $link = [];
    static private array $script = [];
    static private array $scriptBody = [];

    static public function setTitle (string $title) {
        self::$title = $title;
    }

    static public function setTitleEnd (string $title_end) {
        self::$title_end = $title_end;
    }

    static public function setDescription (string $description) {
        self::$description = $description;
    }

    static public function setCanonical (string $url) {
        self::$canonical = $url;
    }

    static public function setRobots (array $tags) {
        self::$robots = $tags;
    }

    static public function addMeta (array $tag) {
        self::$meta[] = $tag;
    }

    static public function addLink (array $tag) {
        self::$link[] = $tag;
    }

    static public function addScript (array $tag) {
        self::$script[] = $tag;
    }

    static public function addScriptBody (string $code) {
        self::$scriptBody[] = $code;
    }

    static public function renderHead (bool $headTags = false) {
        $tags_meta = [
            ['charset'=>self::$charset],
            ['name'=>'description', 'content'=>self::$description],
            ['name'=>'robots', 'content'=>implode(', ', self::$robots)],
            ['name'=>'viewport', 'content'=>self::$viewport],
            ...self::$meta
        ];
        $tags_link = [
            ['name'=>'canonical', 'href'=>self::$canonical],
            ...self::$link
        ];
        $tags_script = [
            ...self::$script
        ];
        $tags_script_body = [
            ...self::$scriptBody
        ];
        $buffer = [
            '<title>'. strip_tags(trim(self::$title)) . strip_tags(trim(self::$title_end)) .'</title>'
        ];
        foreach ($tags_meta as $meta) {
            $tag = '<meta ';
            foreach ($meta as $attr => $value) {
                $tag .= $attr .'="'. htmlentities(trim($value)) .'" ';
            }
            $tag .= '/>';
            $buffer[] = $tag;
        }
        foreach ($tags_link as $link) {
            $tag = '<link ';
            foreach ($link as $attr => $value) {
                $tag .= $attr .'="'. htmlentities(trim($value)) .'" ';
            }
            $tag .= '/>';
            $buffer[] = $tag;
        }
        foreach ($tags_script as $script) {
            $tag = '<script ';
            foreach ($script as $attr => $value) {
                $tag .= $attr .'="'. htmlentities(trim($value)) .'" ';
            }
            $tag .= '/></script>';
            $buffer[] = $tag;
        }
        foreach ($tags_script_body as $body) {
            $tag = '<script>';
            foreach ($script as $attr => $value) {
                $tag .= trim($$body);
            }
            $tag .= '</script>';
            $buffer[] = $tag;
        }

        $final = '';
        if ($headTags === true) {
            $final .= "\t<head>\n";
        }
        $final .= "\t\t". implode("\n\t\t", $buffer);
        if ($headTags === true) {
            $final .= "\n\t</head>\n";
        }
        return $final;
    }

}
