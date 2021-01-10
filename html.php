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

    // Use index 0 for default URL in arrays, so ['url', 'attr'=>'value'] or ['url']
    const OPG_IMAGE = ['secure_url', 'type', 'width', 'height', 'alt'];
    const OPG_VIDEO = ['secure_url', 'type', 'width', 'height'];
    const OPG_AUDIO = ['secure_url', 'type'];
    static private array $opengraph = [
        'og:url' => '',
        'og:type' => 'website',
        'og:title' => '',
        'og:description' => '',
    ];
    static private array $opengraphBlocks = [];

    static public function setTitle (string $title) {
        self::$title = $title;
        if (strlen(self::$opengraph['og:title'])<1) {
            self::$opengraph['og:title'] = $title;
        }
    }

    static public function setTitleEnd (string $title_end) {
        self::$title_end = $title_end;
    }

    static public function setDescription (string $description) {
        self::$description = $description;
        if (strlen(self::$opengraph['og:description'])<1) {
            self::$opengraph['og:description'] = $title;
        }
    }

    static public function setCanonical (string $url) {
        self::$canonical = $url;
        self::$opengraph['og:url'] = $url;
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
        $tags_opengraph = self::$opengraph;
        $tags_opengraph_blocks = self::$opengraphBlocks;
        $buffer = [
            '<title>'. strip_tags(trim(self::$title)) . strip_tags(rtrim(self::$title_end)) .'</title>'
        ];
        // Basic meta tags
        foreach ($tags_meta as $meta) {
            $tag = '<meta ';
            foreach ($meta as $attr => $value) {
                $tag .= $attr .'="'. htmlentities(trim($value)) .'" ';
            }
            $tag .= '/>';
            $buffer[] = $tag;
        }
        // Opengraph
        foreach($tags_opengraph as $property => $content) {
            $tag = '<meta property="'.$property.'" content="'.$content.'" />';
            $buffer[] = $tag;
        }
        foreach($tags_opengraph_blocks as $opengraph) {
            foreach($opengraph as $property => $content) {
                $tag = '<meta property="'.$property.'" content="'.$content.'" />';
                $buffer[] = $tag;
            }
        }
        // Basic Links
        foreach ($tags_link as $link) {
            $tag = '<link ';
            foreach ($link as $attr => $value) {
                $tag .= $attr .'="'. htmlentities(trim($value)) .'" ';
            }
            $tag .= '/>';
            $buffer[] = $tag;
        }
        // Scripts
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
                $tag .= trim($body);
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

    static function setOpenGraphType (string $type) {
        self::$opengraph['type'] = $type;
    }

    static function addOpenGraphImage ($data) {
        if (is_array($data)) {
            $blob = ['og:image'=>$data[0]];
            foreach(self::OPG_IMAGE as $attr) {
                if (isset($data[$attr])) {
                    $blob['og:image:'. $attr] = $data[$attr];
                }
            }
        } elseif (is_string($data) || is_float($data)) {
            $blob = ['og:image'=>$data];
        }
        if ($blob) {
            self::$opengraphBlocks[] = $blob;
        }
    }

    static function addOpenGraphVideo ($data) {
        if (is_array($data)) {
            $blob = ['og:video'=>$data[0]];
            foreach(self::OPG_IMAGE as $attr) {
                if (isset($data[$attr])) {
                    $blob['og:video:'. $attr] = $data[$attr];
                }
            }
        } elseif (is_string($data) || is_float($data)) {
            $blob = ['og:video'=>$data];
        }
        if ($blob) {
            self::$opengraphBlocks[] = $blob;
        }
    }

    static function addOpenGraphAudio ($data) {
        if (is_array($data)) {
            $blob = ['og:audio'=>$data[0]];
            foreach(self::OPG_IMAGE as $attr) {
                if (isset($data[$attr])) {
                    $blob['og:audio:'. $attr] = $data[$attr];
                }
            }
        } elseif (is_string($data) || is_float($data)) {
            $blob = ['og:audio'=>$data];
        }
        if ($blob) {
            self::$opengraphBlocks[] = $blob;
        }
    }

}
