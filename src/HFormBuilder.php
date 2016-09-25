<?php

namespace HHCollection\HForm;

use Collective\Html\FormBuilder;
use Illuminate\Support\HtmlString;

class HFormBuilder extends FormBuilder
{
    protected $cfg;

    /**
     * @param mixed $cfg
     */
    public function setCfg($cfg)
    {
        $this->cfg = $cfg;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCfg()
    {
        return $this->cfg;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        $cfg = $this->getCfg();

        if (isset($cfg['title'])) {
            return $cfg['title'];
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        $cfg = $this->getCfg();

        if (isset($cfg['url'])) {
            return $cfg['url'];
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getMethods()
    {
        $cfg = $this->getCfg();

        if (!empty($cfg['method'])) {
            return $cfg['method'];
        }

        return 'POST';
    }

    /**
     * @return mixed
     */
    public function isFormFile()
    {
        $cfg = $this->getCfg();

        if (isset($cfg['files']) && $cfg['files']) {
            return $cfg['files'];
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function isModel()
    {
        $cfg = $this->getCfg();

        if (isset($cfg['node']) && isset($cfg['model']) && $cfg['node'] instanceof $cfg['model']) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        $cfg = $this->getCfg();

        if (isset($cfg['items']) && is_array($cfg['items'])) {
            return $cfg['items'];
        }

        return null;
    }


    /**
     * render form with template
     */
    public function make($view = null)
    {
        $viewDef = $view ?: 'vendor.form.form-panel';
        // Open form
        $frmOp = $this->makeOpen();
        // Close form
        $frmCl = $this->close();
        
        $frmBd = '';
        // header
        $frmBd .= $this->makeHeader();
        // body
        $frmBd .= $this->makeBody();
        // footer
        $frmBd .= $this->makeFooter();
        
        return new HtmlString(
            $this->view
                ->make($view ?: $viewDef, compact('frmOp', 'frmBd', 'frmCl'))
                ->render()
        );
    }

    /**
     * Make form
     *
     * @return mixed
     */
    public function makeOpen()
    {
        $cfg = $this->getCfg();

        if ($this->isModel()) {
            return $this->model($cfg['node'], [
                'url' => $this->getUrl(),
                'method' => $this->getMethods(),
                'files' => $this->isFormFile(),
            ]);
        }

        return $this->open([
            'url' => $this->getUrl(),
            'method' => $this->getMethods(),
            'files' => $this->isFormFile(),
        ]);

    }

    /**
     * Make commponents
     *
     * @return mixed
     */
    public function makeConponent($item = null, $view = null)
    {
        if (!empty($item) && is_array($item)) {
            $viewDef = 'vendor.form.components.form-group';
            // check and set type for input
            $type = !empty($item['xtype']) ? $item['xtype'] : 'text';
            $name = !empty($item['name']) ? $item['name'] : null;
            $labelField = !empty($item['labelField']) ? $item['labelField'] : null;
            $options = array_except($item, ['name', 'xtype', 'labelField']);
//            class="form-control"
            $input = '';

            $label = $this->label($name, null, array_except($options, ['class']), $escape_html = true);
            switch (true) {
                // select
                case in_array($type, ['select']):
                    $list = (!empty($item['list']) && is_array($item['list'])) ? $item['list'] : [];
                    $selected = !empty($item['selected']) ? $item['selected'] : null;

                    $input = $this->{$type}($name, $list, $selected, $options);
                    break;

                // selectRange, selectYear
                case in_array($type, ['selectRange', 'selectYear']):
                    $begin = !empty($item['begin']) ? $item['begin'] : null;
                    $end = !empty($item['end']) ? $item['end'] : null;
                    $selected = !empty($item['selected']) ? $item['selected'] : null;

                    $input = $this->{$type}($name, $begin, $end, $selected, $options);
                    break;

                // selectMonth
                case in_array($type, ['selectMonth']):
                    $selected = !empty($item['selected']) ? $item['selected'] : null;
                    $format = !empty($item['format']) ? $item['format'] : '%B';

                    $input = $this->{$type}($name, $selected, $options, $format);
                    break;

                // getSelectOption
                case in_array($type, ['getSelectOption']):
                    $display = !empty($item['display']) ? $item['display'] : null;
                    $value = !empty($item['value']) ? $item['value'] : null;
                    $selected = !empty($item['selected']) ? $item['selected'] : null;

                    $input = $this->{$type}($display, $value, $selected);
                    break;

                // checkbox, radio
                case in_array($type, ['checkbox', 'radio']):
                    $value = !empty($item['value']) ? $item['value'] : null;
                    $checked = !empty($item['checked']) ? $item['checked'] : null;

                    $input = $this->{$type}($name, $value, $checked, $options);
                    break;

                // image
                case in_array($type, ['image']):
                    $url = !empty($item['url']) ? $item['url'] : null;

                    $input = $this->{$type}($url, $name, $options);
                    break;

                // password
                case in_array($type, ['password']):
                    $input = $this->{$type}($name, $options);
                    break;

                // submit, button
                case in_array($type, ['submit', 'button']):
                    $value = !empty($item['value']) ? $item['value'] : null;

                    $input = $this->{$type}($value, $options);
                    break;

                // text, hidden, email, tel, number, data, dateTime, dateTimeLocal, time, url, textarea, color
                default:
                    // re-check with model form
                    $value = !empty($item['value']) ? $item['value'] : null;
                    $input = $this->{$type}($name, $value, $options);
                    break;
            }

            return new HtmlString(
                $this->view
                    ->make($view ?: $viewDef, compact('label', 'input'))
                    ->render()
            );
        }

        return '';
    }

    /**
     * Make form heading
     *
     * @return mixed
     */
    public function makeHeader($view = null)
    {
        $viewDef = 'vendor.form.components.panel-header';
        $title = $this->getTitle();

        return new HtmlString(
            $this->view
                ->make($view ?: $viewDef, compact('title'))
                ->render()
        );
    }

    /**
     * Make form heading
     *
     * @return mixed
     */
    public function makeBody($view = null)
    {
        $viewDef = 'vendor.form.components.panel-body';
        $fields = '';
        
        if ($this->getItems() !== null) {
            $items = $this->getItems();

            foreach ($items as $item) {
                $fields .= $this->makeConponent($item);
            }
        }
        
        return new HtmlString(
            $this->view
                ->make($view ?: $viewDef, compact('fields'))
                ->render()
        );
    }
    /**
     * Make form heading
     *
     * @return mixed
     */
    public function makeFooter($view = null)
    {
        $viewDef = 'vendor.form.components.panel-footer';
        $buttons = $this->getTitle();

        return new HtmlString(
            $this->view
                ->make($view ?: $viewDef, compact('buttons'))
                ->render()
        );
    }

    /**
     * Overwrite a form label element.
     *
     * @param  string $name
     * @param  string $value
     * @param  array $options
     * @param  bool $escape_html
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function label($name, $value = null, $options = [], $escape_html = true)
    {
        $this->labels[] = $name;

        $required = !empty($options['required']) ? $options['required'] : false;

        $options = $this->html->attributes($options);

        $value = $this->formatLabel($name, $value);

        if ($escape_html) {
            $value = $this->html->entities($value);
        }

        $label = $required ? '<label for="' . $name . '"' . $options . '>' . $value . '<span class="required"> *</span></label>' : '<label for="' . $name . '"' . $options . '>' . $value . '</label>';
        return $this->toHtmlString($label);
    }

}