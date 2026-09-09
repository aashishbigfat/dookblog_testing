<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Role;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\PostPolicy;
use App\Policies\TopicPolicy;
use App\Policies\DestinationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Post::class => PostPolicy::class,
        Category::class => CategoryPolicy::class,
        Tag::class => TagPolicy::class,
        Topic::class => TopicPolicy::class,
        Destination::class => DestinationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('user_view', 'App\Policies\UserPolicy@user_view');
        Gate::define('user_create', 'App\Policies\UserPolicy@user_create');
        Gate::define('user_edit', 'App\Policies\UserPolicy@user_edit');
        Gate::define('user_activate_inactivate', 'App\Policies\UserPolicy@user_activate_inactivate');

        Gate::define('role_view', 'App\Policies\RolePolicy@role_view');
        Gate::define('role_create', 'App\Policies\RolePolicy@role_create');
        Gate::define('role_edit', 'App\Policies\RolePolicy@role_edit');

        Gate::define('post_view', 'App\Policies\PostPolicy@post_view');
        Gate::define('post_create', 'App\Policies\PostPolicy@post_create');
        Gate::define('post_edit', 'App\Policies\PostPolicy@post_edit');
        Gate::define('post_delete', 'App\Policies\PostPolicy@post_delete');

        Gate::define('tag_delete', 'App\Policies\TagPolicy@tag_delete');
        Gate::define('tag_create', 'App\Policies\TagPolicy@tag_create');
        Gate::define('tag_edit', 'App\Policies\TagPolicy@tag_edit');

        Gate::define('category_create', 'App\Policies\CategoryPolicy@category_create');
        Gate::define('category_delete', 'App\Policies\CategoryPolicy@category_delete');
        Gate::define('category_edit', 'App\Policies\CategoryPolicy@category_edit');

        Gate::define('topic_assign', 'App\Policies\TopicPolicy@topic_assign');
        Gate::define('topic_view', 'App\Policies\TopicPolicy@topic_view');
        Gate::define('topic_edit', 'App\Policies\TopicPolicy@topic_edit');

        Gate::define('destination_assign', 'App\Policies\DestinationPolicy@destination_assign');
        Gate::define('destination_list', 'App\Policies\DestinationPolicy@destination_list');
        Gate::define('destination_edit', 'App\Policies\DestinationPolicy@destination_edit');
        Gate::define('destination_status', 'App\Policies\DestinationPolicy@destination_status');

        //Passport::routes();
    }
}
