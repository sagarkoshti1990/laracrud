<?php

namespace Sagartakle\Laracrud\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

use Illuminate\Database\Eloquent\Model;
use Sagartakle\Laracrud\Helpers\ObjectHelper;

class Activity extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'activity_log';
    
	
	protected $hidden = [
        
    ];

	protected $guarded = [];
    
    protected $mustBeApproved = false;
    protected $canBeRated = false;
    /**
     * The fillable fields for the model.
     *
     * @var    array
     */
    protected $fillable = [
        'user_id',
        'context_type',
        'context_id',
        'action',
        'description',
        'details',
        'data',
        'public',
        'developer',
        'ip_address',
        'user_agent',
    ];

    /**
     * The context item for the log entry.
     *
     * @var string
     */
    protected $contextItem = null;

    /**
     * The replacements prefix for language key descriptions and details.
     *
     * @var string
     */
    protected $replacementsPrefix = null;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the user that the activity belongs to.
     *
     * @return object
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
    
    /**
     * Get all of the owning commentable models.
     */
    public function context()
    {
        return $this->morphTo()->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Create an activity log entry.
     *
     * @param  mixed    $data
     * @return boolean
     */
    public static function log($action, $crud, $data = [], $public = true)
    {
        $arr = [];
        if(isset($crud->module->name)) {
            $arr['activity_name'] = $crud->module->name;
        } else if(isset($crud->name)) {
            $arr['activity_name'] = $crud->name;
        } else if(!($crud instanceof ObjectHelper)) {
            if(is_object($crud) && isset($crud->name)) {
                $arr['activity_name'] = $crud->name;
            } else if(is_array($crud) && isset($crud['name'])) {
                $arr['activity_name'] = $crud['name'];
            } else {
                $arr['activity_name'] = "Tests";
            }
        } else {
            $arr['activity_name'] = "Tests";
        }

        if(isset($crud->model)) {
            $arr['activity_context_type'] = get_class($crud->model);
        } else if(isset($crud->context_type)) {
            $arr['activity_context_type'] = $crud->context_type;
        } else if(!($crud instanceof ObjectHelper) && isset($crud['context_type'])) {
            $arr['activity_context_type'] = $crud['context_type'];
        } else {
            $arr['activity_context_type'] = "App\Models\Test";
        }

        if(isset($crud->action)) {
            $arr['activity_action'] = $crud->action;
        } else if(!($crud instanceof ObjectHelper) && isset($crud['action'])) {
            $arr['activity_action'] = $crud['action'];
        } else {
            $arr['activity_action'] = config('App.activity_log.'.$arr['activity_name'].'.'.strtoupper($action).'.action',$action);
        }

        if(isset($crud->description)) {
            $arr['activity_description'] = $crud->description;
        } else if(!($crud instanceof ObjectHelper) && isset($crud['description'])) {
            $arr['activity_description'] = $crud['description'];
        } else {
            $arr['activity_description'] = config('App.activity_log.'.$arr['activity_name'].'.'.strtoupper($action).'.description',$arr['activity_name'].' '.$action);
        }
        // dd($action);
        if(count($data)) {
            if($action == "Created") {
                $data_new = self::array_remove_null($data['new']);
                if(is_array($data_new) && count($data_new)) {
                    $activity = self::make([
                        'action' => $arr['activity_action'],
                        'context_type' => $arr['activity_context_type'],
                        'context_id' => $data['new']->id,
                        'description' => $arr['activity_description'],
                        'data' => ['new' => $data_new],
                        'public' => $public,
                    ]);
                }
            } else if($action == "Updated") {
                
                $collection = collect($data['new']);
                $data_new = $collection->diffAssoc($data['old']);

                $collection = collect($data['old']);
                $data_old = $collection->diffAssoc($data['new']);
                if($data_old->isNotEmpty() && $data_new->isNotEmpty()) {
                    $activity = self::make([
                        'action' => $arr['activity_action'],
                        'context_type' => $arr['activity_context_type'],
                        'context_id' => $data['new']->id,
                        'description' => $arr['activity_description'],
                        'data' => ['old' => $data_old, 'new' => $data_new],
                        'public' => $public,
                    ]);
                }
            } else if($action == "Deleted") {
                $data_new = self::array_remove_null($data['old']);
                if(is_array($data_new) && count($data_new)) {
                    $activity = self::make([
                        'action' => $arr['activity_action'],
                        'context_type' => $arr['activity_context_type'],
                        'context_id' => $data['old']->id,
                        'description' => $arr['activity_description'],
                        'data' => ['old' => $data_new],
                        'public' => $public,
                    ]);
                }
            }
        }
    }
    /**
     * Create an activity log entry.
     *
     * @param  mixed    $data
     * @return boolean
     */
    public static function make($data = [])
    {
        // set the defaults from config
        $defaults = config('App.log.defaults');
        if (!is_array($defaults)) {
            $defaults = [];
        }

        // if data is a string, create the array from the description
        if (is_string($data)) {
            $data = ['description' => $data];

            $description = strtolower($data['description']);

            if (substr($description, 0, 4) == "edit")
                $data['action'] = "Update";

            if (substr($description, 0, 6) == "update")
                $data['action'] = "Update";

            if (substr($description, 0, 6) == "delete")
                $data['action'] = "Delete";
        } else { // otherwise convert it to an array if it is an object 
            if (is_object($data))
                $data = (array) $data;
        }
        
        // set the user ID
        if (!isset($data['user_id'])) {
            $data['user_id'] = Auth::check() ? Auth::id() : null;
        }

        // allow "updated" boolean to set action and replace activity text verbs with "Updated"
        if (isset($data['updated'])) {
            if ($data['updated']) {
                $data['action'] = "Update";
                $data['description'] = str_replace('Added', 'Updated', str_replace('Created', 'Updated', $data['description']));
                $data['description'] = str_replace('added', 'updated', str_replace('created', 'updated', $data['description']));
            } else {
                $data['action'] = "Create";
            }
        }

        // allow "deleted" boolean to set action and replace activity text verbs with "Deleted"
        if (isset($data['deleted']) && $data['deleted']) {
            $data['action'] = "Delete";
            $data['description'] = str_replace('Added', 'Deleted', str_replace('Created', 'Deleted', $data['description']));
            $data['description'] = str_replace('added', 'deleted', str_replace('created', 'deleted', $data['description']));
        }

        // set developer flag
        if (!isset($data['developer']) && !is_null(session('developer')))
            $data['developer'] = true;

        // set IP address
        if (!isset($data['ipAddress']))
            $data['ipAddress'] = Request::getClientIp();

        // set user agent
        if (!isset($data['userAgent']))
            $data['userAgent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No User Agent';

        // set additional data and encode it as JSON if it is an array or an object
        if (isset($data['data']) && (is_array($data['data']) || is_object($data['data'])))
            $data['data'] = json_encode($data['data']);

        // format array keys to snake case for insertion into database
        $dataFormatted = [];
        foreach ($data as $key => $value) {
            $dataFormatted[\Str::snake($key)] = $value;
        }

        // merge defaults array with formatted data array
        $data = array_merge($defaults, $dataFormatted);

        // if language keys are being used and description / details are arrays, encode them in JSON
        if (isset($data['language_key']) && $data['language_key']) {
            if (is_array($data['description']) || is_object($data['description']))
                $data['description'] = json_encode($data['description']);
            if (isset($data['details']) && (is_array($data['details']) || is_object($data['details'])))
                $data['details'] = json_encode($data['details']);
        }

        // create the record
        return static::create($data);
    }

    /**
     * remove null value keys.
     *
     * @return object
     */
    public static function array_remove_null($item)
    {
        if (is_array($item) || is_object($item)) {
            
        return collect($item)
            ->reject(function ($item) {
                return is_null($item);
            })
            ->flatMap(function ($item, $key) {
    
                return is_numeric($key)
                    ? [self::array_remove_null($item)]
                    : [$key => self::array_remove_null($item)];
            })
            ->toArray();
        } else {
            return $item;
        }
    }

    /**
     * Filter out activities that are not public.
     *
     * @return QueryBuilder
     */
    public function scopeOnlyPublic($query)
    {
        return $query->where('public', true);
    }

    /**
     * Filter out activities that are public.
     *
     * @return QueryBuilder
     */
    public function scopeOnlyPrivate($query)
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
     * Get the name of the user.
     *
     * @return string
     */
    public function getName()
    {
        if ((bool) $this->developer)
            return config('App.log.names.developer');

        $user = $this->user;
        if (empty($user))
            return config('App.log.names.unknown');

        if (!config('App.log.full_name_as_name'))
            return !is_null($user->username) ? $user->username : $user->name;

        if (config('App.log.full_name_last_name_first'))
            return $user->last_name.', '.$user->first_name;
        else
            return $user->first_name.' '.$user->last_name;
    }

    /**
     * Get a shortened version of the user agent with title text of the full user agent.
     *
     * @return string
     */
    public function getUserAgentPreview($length = 42)
    {
        $userAgentPreview = substr($this->user_agent, 0, $length);

        if (strlen($this->user_agent) > $length)
            $userAgentPreview .= '<span class="ellipsis" title="'.$this->user_agent.'">...</span>';

        return $userAgentPreview;
    }

    /**
     * Get the icon class name for the log entry's action.
     *
     * @return string
     */
    public function getIcon()
    {
        $actionIcons = config('App.log.action_icons');

        $action_icon_bd_colors = config('App.log.action_icon_bd_colors');

        $actionFormatted = str_replace(' ', '_', trim(strtolower($this->action)));

        if (!is_null($this->action) && $this->action == "" || !isset($actionIcons[$actionFormatted]) || !isset($action_icon_bd_colors[$actionFormatted]))
            return $actionIcons['x']." ".$action_icon_bd_colors['x'];

        return $actionIcons[$actionFormatted]." ".$action_icon_bd_colors[$actionFormatted];
    }

    /**
     * Get the markup for the log entry's icon.
     *
     * @return string
     */
    public function getIconMarkup()
    {
        $iconElement     = config('App.log.action_icon.element');
        $iconClassPrefix = config('App.log.action_icon.class_prefix');

        return '<'.$iconElement.' class="'.$iconClassPrefix.$this->getIcon().'" title="'.$this->action.'"></'.$iconElement.'>';
    }

    /**
     * Get the markup for the log entry's icon.
     *
     * @return string
     */
    public function getHtmlDescription()
    {
        if($this->action == 'Created') {
            if($this->context_type == "App\Models\Recording") {
                return $this->description." By name <a href='".$this->context->filedata->path()."' target='_blank' class='btn btn-link'>".$this->context->filedata->name."</a>";
            } else if($this->context_type == "Actuallymab\LaravelComment\Models\Comment") {
                return $this->description." By name ".$this->context->{'comment'};
            } else {
                return $this->description." By name ".$this->context->{$this->getAttr()};
            }
        } else if($this->action == 'Updated') {
            return view('admin.Activities.update_render',['item' => $this])->render();
        } else if($this->action == 'Deleted') {
            if($this->context_type == "App\Models\Recording") {
                return $this->description." By name ".$this->context->filedata->{'path()'};
            } else if($this->context_type == "Actuallymab\LaravelComment\Models\Comment") {
                return $this->description." By name ".$this->context->{'comment'};
            } else {
                return $this->description." By name ".$this->context->{$this->getAttr()};
            }
        } else {
            return $this->description;
        }
    }
    
    public function getAttr()
    {
        $model_name = app($this->context_type);
        if(method_exists($model_name, 'get_module')) {
            $module = $model_name::get_module();
            return $module->represent_attr;
        }
        return 'id';
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
            $show_col = $module->show_col;
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

    /**
     * Get the description.
     *
     * @param  mixed    $firstPersonIfUser
     * @return string
     */
    public function getDescription($firstPersonIfUser = false)
    {
        if (!$this->language_key && false)
            return $this->description;

        $replacements = [];

        if (substr($this->description, 0, 1) == "[" && substr($this->description, -1) == "]") {
            $data = json_decode($this->data);

            $key              = $data[0];
            $replacementsData = $data[1];

            if (is_object($replacementsData)) {
                foreach ($replacementsData as $replacementKey => $value) {
                    $replacements[$replacementKey] = $this->getReplacementValue($value, $replacementsData);
                }
            }
        } else {
            $key = $this->description;
        }

        if (!isset($replacements['user']))
            $replacements['user'] = $this->getName();

        $descriptionsKeyPrefix = config('App.log.language_key.prefixes.descriptions');

        $makeFirstPerson = $firstPersonIfUser && Auth::check() && Auth::id() == $this->user_id;

        $you = trans($descriptionsKeyPrefix.'.partials.you');

        if ($makeFirstPerson)
            $replacements['user'] = strtolower($you);

        $description = ucfirst(trans($descriptionsKeyPrefix.'.'.$key, $replacements));

        if ($makeFirstPerson) {
            $their = strtolower(trans($descriptionsKeyPrefix.'.partials.their'));
            $your  = strtolower(trans($descriptionsKeyPrefix.'.partials.your'));

            $description = str_replace($their, $your, $description);

            $youWas  = $you.' '.strtolower(trans($descriptionsKeyPrefix.'.partials.was'));
            $youWere = $you.' '.strtolower(trans($descriptionsKeyPrefix.'.partials.were'));

            $description = str_replace($youWas, $youWere, $description);

            $youHas  = $you.' '.strtolower(trans($descriptionsKeyPrefix.'.partials.has'));
            $youHave = $you.' '.strtolower(trans($descriptionsKeyPrefix.'.partials.have'));

            $description = str_replace($youHas, $youHave, $description);
        }

        return $description;
    }

    /**
     * Get the linked description (if one is available). Otherwise, just get the description.
     *
     * @param  mixed    $class
     * @param  mixed    $firstPersonIfUser
     * @return string
     */
    public function getLinkedDescription($class = null, $firstPersonIfUser = false)
    {
        $description = $this->getDescription($firstPersonIfUser);

        if (is_null($this->getUrl()))
            return $description;

        return '<a href="'.$this->getUrl().'"'.(!is_null($class) ? ' class="'.$class.'"' : '').'>'.$description.'</a>' . "\n";
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
                    return trans(config('App.log.language_key.prefixes.details').'.'.$data[0]).': '.$data[1];
            }
            else
            {
                $details = [];

                foreach ($data as $label => $value)
                {
                    $details[] = trans(config('App.log.language_key.prefixes.details').'.'.$label).': '.$value;
                }

                return implode(', ', $details);
            }
        }

        return $this->details;
    }

    /**
     * Get the value for a replacement.
     *
     * @param  string   $value
     * @param  object   $replacementsData
     * @return string
     */
    protected function getReplacementValue($value, $replacementsData)
    {
        if (is_null($this->replacementsPrefix)) {
            $this->replacementsPrefix = config('App.log.language_key.prefixes.replacements');
            if (!is_null($this->replacementsPrefix))
                $this->replacementsPrefix .= ".";
        }

        if (substr($value, 0, 1) == "[" && substr($value, -1) == "]") {
            $value = substr($value, 1, strlen($value) - 2);
        } else {
            $value = explode('|', $value);

            if (count($value) == 1) {
                $value = trans($this->addReplacementsPrefix($value[0]));
            } else {
                $configString = $value[0];
                $value        = $this->addReplacementsPrefix($value[1]);

                $config = [
                    's' => false,
                    'p' => false,
                    'a' => false,
                    'l' => false,
                ];

                foreach (array_keys($config) as $item) {
                    if (stripos($configString, $item) !== false)
                        $config[$item] = true;
                }

                if ($config['s'] || $config['p']) {
                    $number = $config['s'] ? 1 : 2;

                    if ($config['s'] && $config['p'] && isset($replacementsData->number))
                        $number = (int) $replacementsData->number;

                    if ($config['a'] && $number == 1)
                        $value = trans_choice_a($value, $number);
                    else
                        $value = trans_choice($value, $number);
                } else {
                    if ($config['a'])
                        $value = trans_a($value);
                    else
                        $value = trans($value);
                }

                if ($config['l'])
                    $value = strtolower($value);
            }
        }

        return $value;
    }

    /**
     * Add the replacements prefix.
     *
     * @param  string   $value
     * @return string
     */
    protected function addReplacementsPrefix($value)
    {
        $prefix = $this->replacementsPrefix;

        if (is_numeric($value) || is_null($prefix) || substr($value, 0, strlen($prefix)) == $prefix)
            return $value;

        return $prefix.$value;
    }

    /**
     * Get the context item (if one is available).
     *
     * @param  boolean  $returnArray
     * @return object
     */
    public function getContextItem($returnArray = false)
    {
        if (is_null($this->contextItem)) {
            $contextTypeSettings = config('App.log.context_types.'.strtolower(\Str::snake($this->context_type)));

            if (!is_array($contextTypeSettings) || !isset($contextTypeSettings['model']))
                return null;

            $this->contextItem = call_user_func([$contextTypeSettings['model'], 'find'], (int) $this->context_id);
        }

        if ($returnArray && is_object($this->contextItem) && method_exists($this->contextItem, 'toArray'))
            return $this->contextItem->toArray();

        return $this->contextItem;
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
    
}
