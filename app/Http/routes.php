<?php
use Dingo\Api\Routing\Router;

Route::singularResourceParameters();
Route::get('/', ['as' => 'home', function () {
    return app()->version();
}]);

/*
|--------------------------------------------------------------------------
| API Transformers
|--------------------------------------------------------------------------
*/

app('Dingo\Api\Transformer\Factory')->register('App\Models\User', 'App\Http\Transformers\UserTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Campus', 'App\Http\Transformers\CampusTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Photo', 'App\Http\Transformers\PhotoTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Address', 'App\Http\Transformers\AddressTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Residence', 'App\Http\Transformers\ResidenceTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Course', 'App\Http\Transformers\CourseTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Degree', 'App\Http\Transformers\DegreeTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Responsibility', 'App\Http\Transformers\ResponsibilityTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Job', 'App\Http\Transformers\JobTransformer');
app('Dingo\Api\Transformer\Factory')->register('App\Models\Social', 'App\Http\Transformers\SocialTransformer');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes. They are managed by Dingo/API and can be accessed within the application.
|
*/

$api = app(Router::class);
$api->version('v1', ['namespace' => 'App\Http\Controllers'], function (Router $api) {

    $api->resources(['auth' => ['AuthController', ['only' => ['store']]]]);

    $api->group(['middleware' => 'api.auth'], function (Router $api) {

        $api->resources([
            'search'           => ['SearchController', ['only' => ['index']]],
            'users'            => ['UserController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]],
            'campuses'         => ['CampusController', ['only' => ['index', 'show']]],
            'photos'           => ['PhotoController', ['only' => ['index', 'show', 'destroy']]],
            'addresses'        => ['AddressController', ['only' => ['index', 'show', 'destroy']]],
            'residences'       => ['ResidenceController', ['only' => ['index', 'show']]],
            'courses'          => ['CourseController', ['only' => ['index', 'show']]],
            'degrees'          => ['DegreeController', ['only' => ['index', 'show']]],
            'responsibilities' => ['ResponsibilityController', ['only' => ['index', 'show']]],
            'jobs'             => ['JobController', ['only' => ['index', 'show']]],
            'socials'          => ['SocialController', ['only' => ['index', 'show']]],
        ]);
    });
});
