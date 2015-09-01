<?php

namespace PhpSoft\Users\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use UserTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'location',
        'country',
        'biography',
        'occupation',
        'website',
        'image'
    ];

    public static $filters = [
        'name',
        'email',
        'username'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Create user
     * 
     * @param  array  $attributes
     * @return User
     */
    public static function create(array $attributes = [])
    {
        $attributes['password'] = bcrypt($attributes['password']);

        return parent::create($attributes)->fresh();
    }

    /**
     * Change password
     * 
     * @param  array  $attributes
     * @return User
     */
    public function changePassword($newPassword)
    {
        $user = $this;
        $user['password'] = bcrypt($newPassword);

        return $user->save();
    }

    /**
     * 
     * @param  array  $options
     * @return array
     */
    public static function browse($options = [])
    {
        $find = parent::select('*');

        if (!empty($options['filters'])) {
            $className = get_called_class();
            $filters = $className::$filters;
            $inFilters = array_intersect($filters, array_keys($options['filters']));

            if (!empty($inFilters)) {
                foreach ($inFilters as $key) {
                    $find = $find->where($key, 'LIKE', '%'. $options['filters'][$key] .'%');
                }
            }
        }

        if (!empty($options['order'])) {
            foreach ($options['order'] as $field => $direction) {
                $find = $find->orderBy($field, $direction);
            }
        }

        $total = $find->count();

        if (!empty($options['offset'])) {
            $find = $find->skip($options['offset']);
        }

        if (!empty($options['limit'])) {
            $find = $find->take($options['limit']);
        }

        if (!empty($options['cursor'])) {
            $find = $find->where('id', '<', $options['cursor']);
        }

        return [
            'total'  => $total,
            'offset' => empty($options['offset']) ? 0 : $options['offset'],
            'limit'  => empty($options['limit']) ? 0 : $options['limit'],
            'data'   => $find->get(),
        ];
    }
}