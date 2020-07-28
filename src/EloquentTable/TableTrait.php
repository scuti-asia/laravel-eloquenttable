<?php

namespace Scuti\EloquentTable;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

trait TableTrait
{
    public $eloquentTableColumns = array();

    public $eloquentTableHiddenColumns = array();

    public $eloquentTableModifications = array();

    public $eloquentTableRowAttributesModifications = array();

    public $eloquentTableCellAttributesModifications = array();

    public $eloquentTableAttributes = array();

    public $eloquentTableMeans = array();

    public $eloquentTableSort = array();

    public $eloquentTablePages = false;

    public function columns(array $columns = array())
    {
        $this->eloquentTableColumns = $columns;

        return $this;
    }

    public function hidden(array $columns = array())
    {
        $this->eloquentTableHiddenColumns = $columns;

        return $this;
    }

    public function showPages()
    {
        $this->eloquentTablePages = true;

        return $this;
    }

    public function attributes(array $attributes = array())
    {
        $this->eloquentTableAttributes = $this->arrayToHtmlAttributes($attributes);

        return $this;
    }

    public function render($view = '')
    {
        if (count($this->eloquentTableAttributes) === 0) {
            $attributes = Config::get('eloquenttable.default_table_attributes', []);

            $this->attributes($attributes);
        }

        if (!$view) {
            $view = 'eloquenttable::laravel-table';
        }

        return View::make($view, [
            'collection' => $this,
        ])->render();
    }

    public function modify($column, Closure $closure)
    {
        $this->eloquentTableModifications[$column] = $closure;

        return $this;
    }

    public function modifyCell($column, $closure)
    {
        $this->eloquentTableCellAttributesModifications[$column] = $closure;

        return $this;
    }

    public function modifyRow($name, $closure)
    {
        $this->eloquentTableRowAttributesModifications[$name] = $closure;

        return $this;
    }

    public function getCellAttributes($column, $record = null)
    {
        $attributes = array();
        if (array_key_exists($column, $this->eloquentTableCellAttributesModifications)) {
            $attributes = call_user_func($this->eloquentTableCellAttributesModifications[$column], $record);
            if (array_key_exists($column, $this->eloquentTableHiddenColumns)) {
                $attributes = array_merge($attributes, $this->eloquentTableHiddenColumns[$column]);
            } elseif (in_array($column, $this->eloquentTableHiddenColumns)) {
                $attributes = array_merge($attributes, Config::get('eloquenttable.default_hidden_column_attributes'));
            }

            return $this->arrayToHtmlAttributes($attributes);
        } else {
            return;
        }
    }

    public function getRowAttributes($record)
    {
        $attributes = array();
        foreach ($this->eloquentTableRowAttributesModifications as $closure) {
            $tmpAtrributes = call_user_func($closure, $record);
            if (is_array($tmpAtrributes)) {
                $attributes = array_merge($attributes, $tmpAtrributes);
            }
        }

        return $this->arrayToHtmlAttributes($attributes);
    }

    public function sortable($columns = array())
    {
        $this->eloquentTableSort = $columns;

        return $this;
    }

    public function means($column, $relation)
    {
        $this->eloquentTableMeans[$column] = $relation;

        return $this;
    }

    public function getRelationshipProperty($column)
    {
        $attributes = explode('.', $column);

        $tmpStr = $this;

        foreach ($attributes as $attribute) {
            if ($attribute === end($attributes)) {
                if (is_object($tmpStr)) {
                    $tmpStr = $tmpStr->$attribute;
                }
            } else {
                $tmpStr = $this->$attribute;
            }
        }

        return $tmpStr;
    }

    public function getRelationshipObject($column)
    {
        $attributes = explode('.', $column);

        if (count($attributes) > 1) {
            $relationship = $attributes[count($attributes) - 2];
        } else {
            $relationship = $attributes[count($attributes) - 1];
        }

        return $this->$relationship;
    }

    public function getHiddenColumnAttributes($column)
    {
        if (array_key_exists($column, $this->eloquentTableHiddenColumns)) {
            return $this->arrayToHtmlAttributes($this->eloquentTableHiddenColumns[$column]);
        } elseif (in_array($column, $this->eloquentTableHiddenColumns)) {
            return $this->arrayToHtmlAttributes(Config::get('eloquenttable.default_hidden_column_attributes'));
        } else {
            return;
        }
    }

    public function scopeSort($query, $field = null, $sort = null)
    {
        if ($field && $sort) {
            $columns = Schema::getColumnListing($this->getTable());

            if (in_array($field, $columns)) {
                if ($sort === 'asc' || $sort === 'desc') {
                    return $query->orderBy($field, $sort);
                }
            }
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function newCollection(array $models = array())
    {
        return new TableCollection($models);
    }

    private function arrayToHtmlAttributes(array $attributes = array())
    {
        $attributeString = '';

        if (count($attributes) > 0) {
            foreach ($attributes as $key => $value) {
                $attributeString .= ' '.$key."='".$value."'";
            }
        }

        return $attributeString;
    }
}
