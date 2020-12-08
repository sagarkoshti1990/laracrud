<?php

namespace Sagartakle\Laracrud\Helpers\Traits;

trait ActivityTrait
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activities()
    {
        return $this->morphMany(\Activity::class, 'context');
    }

    /**
     * Filter out activities that are not public.
     *
     * @return QueryBuilder
     */
    public function getOnlyPublic($query)
    {
        return $query->where('public', true);
    }

    /**
     * Filter out activities that are public.
     *
     * @return QueryBuilder
     */
    public function getOnlyPrivate($query)
    {
        return $query->where('public', false);
    }

    /**
     * Filter out activities that were not carried out by the developer.
     *
     * @return QueryBuilder
     */
    public function scopeOnlyDeveloper($query)
    {
        return $query->where('developer', true);
    }

    /**
     * Filter out activities that were carried out by the developer.
     *
     * @return QueryBuilder
     */
    public function scopeOnlyUser($query)
    {
        return $query->where('developer', false);
    }

    /**
     * Get additional data.
     *
     * @param  mixed    $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if (substr($this->data, 0, 1) == "{" && substr($this->data, -1) == "}") {
            $data = json_decode($this->data);

            if (!is_null($key))
                return isset($data->{$key}) ? $data->{$key} : null;
            else
                return $data;
        }

        return $this->data;
    }

    /**
     * Get the details.
     *
     * @return string
     */
    public function getDetails()
    {
        if (!$this->language_key)
            return $this->details;

        $replacements = [];

        $array  = substr($this->details, 0, 1) == "[" && substr($this->details, -1) == "]";
        $object = substr($this->details, 0, 1) == "{" && substr($this->details, -1) == "}";

        if ($array || $object)
        {
            $data = json_decode($this->details);

            if ($array)
            {
                if (count($data) == 2)
                    return trans(config('stlc.language_key.prefixes.details').'.'.$data[0]).': '.$data[1];
            }
            else
            {
                $details = [];

                foreach ($data as $label => $value)
                {
                    $details[] = trans(config('stlc.language_key.prefixes.details').'.'.$label).': '.$value;
                }

                return implode(', ', $details);
            }
        }

        return $this->details;
    }

    /**
     * Get the URL for the log entry's context type if possible.
     *
     * @return string
     */
    public function getUrl()
    {
        $model_name = app($this->context_type);
        if(method_exists($model_name, 'get_module')) {
            $module = $model_name::get_module();
            $show_col = $module->represent_attr;
            if(isset($this->context)) {
                $url = '<a href="' . url(config('stlc.route_prefix') .'/'.$module->name_db.'/'. $this->context->id) . '">' . $this->context->$show_col . '</a>';
            } else {
                $url = $this->context_id;
            }
        } else {
            $url = $this->context_id;
        }
        
        return $url;
    }

}
