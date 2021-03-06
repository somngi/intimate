<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $table = 'posts';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(){
        return $this->hasMany(Comments::class,'post_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postMedia(){
        return $this->hasMany(PostMedia::class,'post_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags(){
        return $this->belongsToMany(Tag::class,'post_tag','post_id','tag_id');
    }

    public function archives(){
        return $this->selectRaw('year(created_at) year,monthname(created_at) month, count(*) count')
            ->where('is_published',1)
            ->groupBy('year','month')
            ->orderByRaw('min(created_at) desc')
            ->get();
    }

    public function tagsList(){
        $tags = Tag::has('posts')->get();
        foreach ($tags as $tag){
            $class = array_random(['madium','large',' ','madium']);
            $tag->class = $class;
        }
        return $tags;
    }

    public function latestPosts(){
        return $this->with('category')
            ->with('postMedia')
            ->where('is_published',1)
            ->limit(5)
            ->orderBy('created_at','decs')
            ->get();
    }

    public function popularPosts(){
        return $this->with('category')
            ->with('postMedia')
            ->where('is_published',1)
            ->where('is_popular',1)
            ->limit(5)
            ->orderBy('created_at','decs')
            ->get();
    }

    public function latestComments(){
        return Comments::with('post')
            ->limit(5)
            ->orderBy('created_at','decs')
            ->get();
    }

    public function urlEncode($string){
        $url = preg_replace("/[#$%^&*()+=\-\_\[\]\`\‘\’\';,.\/{}|\":<>?@!~\\\\]/",'',$string);
        $url = str_replace(' ','-',strtolower($url));
        return $url;
    }
}
