<?php
/* HTML Object Strings | https://gitlab.com/byjoby/html-object-strings | MIT License */
namespace HtmlObjectStrings;

trait TagTrait
{
    public $tag = 'span';
    public $selfClosing = false;
    public $content = null;

    protected $classes = [];
    protected $attributes = [];
    protected $hidden = false;

    public function hidden($hidden=null)
    {
        if ($hidden !== null) {
            $this->hidden = $hidden;
        }
        return $this->hidden;
    }

    protected function htmlContent()
    {
        if (is_array($this->content)) {
            return implode(PHP_EOL, $this->content);
        } else {
            return $this->content;
        }
    }

    protected function htmlAttributes()
    {
        $attr = $this->attributes;
        if ($this->classes()) {
            $attr['class'] = implode(' ', $this->classes());
        }
        return $attr;
    }

    public function addClass(string $name)
    {
        if (!$name) {
            return;
        }
        $this->classes[] = $name;
        $this->classes = array_unique($this->classes);
        sort($this->classes);
    }

    public function hasClass(string $name) : bool
    {
        return in_array($name, $this->classes);
    }

    public function removeClass(string $name)
    {
        $this->classes = array_filter(
            $this->classes,
            function ($e) use ($name) {
                return $e != $name;
            }
        );
        sort($this->classes);
    }

    public function classes() : array
    {
        return $this->classes;
    }

    public function attr(string $name, $value = null)
    {
        if ($value === false) {
            unset($this->attributes[$name]);
            return null;
        }
        if ($value !== null) {
            $this->attributes[$name] = $value;
        }
        return @$this->attributes[$name];
    }

    public function data(string $name, $value = null)
    {
        return $this->attr("data-$name", $value);
    }

    public function string() : string
    {
        //output empty string if hidden
        if ($this->hidden()) {
            return '';
        }
        //build output
        $out = '';
        //build opening tag
        $out .= '<'.$this->tag;
        //build attributes
        if ($attr = $this->htmlAttributes()) {
            foreach ($attr as $key => $value) {
                if ($value === null) {
                    $out .= " $key";
                } else {
                    $value = htmlspecialchars($value);
                    $out .= " $key=\"$value\"";
                }
            }
        }
        //continue t close opening tag and add content and closing tag if needed
        if ($this->selfClosing) {
            $out .= ' />';
        } else {
            $out .= '>';
            //build content
            $out .= $this->htmlContent();
            //build closing tag
            $out .= '</'.$this->tag.'>';
        }
        return $out;
    }

    public function __toString()
    {
        return $this->string();
    }
}
