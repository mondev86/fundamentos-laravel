<?php

namespace App\Data;

class LaravelData
{
    public static function topics(): array
    {
        return [
            [
                'slug' => 'routing',
                'title' => 'Routing',
                'icon' => '🛣️',
                'sections' => [
                    [
                        'title' => '¿Qué es el routing en Laravel?',
                        'content' => 'Laravel enruta las peticiones HTTP a través de archivos en routes/. Los más usados son web.php (con sesiones/CSRF) y api.php (stateless).',
                        'code' => <<<'CODE'
// Rutas básicas
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Ruta con parámetro opcional
Route::get('/profile/{name?}', function ($name = 'Guest') {
    return "Hello, $name";
});

// Rutas resource (genera las 7 rutas CRUD automáticamente)
Route::resource('posts', PostController::class);

// Rutas con nombre (para usar en blade o redirect)
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
CODE,
                        'tip' => 'En entrevistas preguntan: ¿cuál es la diferencia entre web.php y api.php? → Middleware. web.php incluye web middleware group (session, CSRF, cookies). api.php incluye api middleware group (throttle, sin sesión).',
                    ],
                    [
                        'title' => 'Route Groups y Middleware',
                        'content' => 'Agrupa rutas para aplicar middleware, prefijos o namespaces en bloque.',
                        'code' => <<<'CODE'
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::resource('users', UserController::class);
});

// Anidar grupos
Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ProfileController::class, 'show']);
});
CODE,
                        'tip' => 'php artisan route:list muestra todas las rutas registradas — muy útil para debug.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué método HTTP usa Route::resource para actualizar un recurso?', 'options' => ['GET', 'POST', 'PUT/PATCH', 'DELETE'], 'answer' => 2],
                    ['q' => '¿Qué archivo de rutas incluye protección CSRF por defecto?', 'options' => ['api.php', 'console.php', 'web.php', 'channels.php'], 'answer' => 2],
                    ['q' => '¿Qué comando lista todas las rutas registradas?', 'options' => ['php artisan route:show', 'php artisan route:list', 'php artisan routes', 'php artisan list:routes'], 'answer' => 1],
                ],
            ],
            [
                'slug' => 'eloquent',
                'title' => 'Eloquent ORM',
                'icon' => '🗄️',
                'sections' => [
                    [
                        'title' => 'Modelos y convenciones',
                        'content' => 'Eloquent mapea tablas a clases. Por convención User → tabla users, Post → posts. Puedes sobrescribir con $table.',
                        'code' => <<<'CODE'
class Post extends Model
{
    protected $table = 'blogposts';
    protected $fillable = ['title', 'body', 'userid'];
    protected $hidden = ['deletedat'];
    protected $casts = [
        'publishedat' => 'datetime',
        'isactive'    => 'boolean',
        'meta'         => 'array',
    ];
}
CODE,
                        'tip' => 'IMPORTANTE: diferencia entre $fillable y $guarded. $fillable es whitelist (recomendado), $guarded es blacklist. Nunca uses $guarded = [] en producción sin cuidado.',
                    ],
                    [
                        'title' => 'Queries frecuentes',
                        'content' => 'Eloquent provee un query builder fluido.',
                        'code' => <<<'CODE'
// Obtener todos
$users = User::all();

// Buscar por PK (lanza 404 si no existe con findOrFail)
$user = User::findOrFail($id);

// Where con múltiples condiciones
$posts = Post::where('status', 'published')
             ->where('user_id', auth()->id())
             ->latest()
             ->paginate(15);

// firstOrCreate — busca o crea
$user = User::firstOrCreate(
    ['email' => 'john@example.com'],
    ['name' => 'John', 'password' => bcrypt('secret')]
);

// Eager loading (evita N+1)
$posts = Post::with(['author', 'comments.user'])->get();
CODE,
                        'tip' => 'El problema N+1 es la pregunta de entrevista más común sobre Eloquent. Siempre usa with() para cargar relaciones.',
                    ],
                    [
                        'title' => 'Relaciones',
                        'content' => 'Las relaciones se definen como métodos en el modelo.',
                        'code' => <<<'CODE'
class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

class Post extends Model
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}

// Uso
$user->posts()->create(['title' => 'New Post']);
$post->tags()->attach([1, 2, 3]);
$post->tags()->sync([1, 4]); // sincroniza (borra los que no están)
CODE,
                        'tip' => 'Saber explicar hasOne vs hasMany vs belongsToMany de memoria es esencial.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué método de Eloquent evita el problema N+1?', 'options' => ['load()', 'with()', 'join()', 'include()'], 'answer' => 1],
                    ['q' => '¿Cuál es la diferencia entre save() y create()?', 'options' => ['No hay diferencia', 'create() requiere $fillable, save() no', 'save() requiere $fillable, create() no', 'create() no persiste en DB'], 'answer' => 1],
                    ['q' => '¿Qué relación usarías para "un Post tiene muchos Comments"?', 'options' => ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany'], 'answer' => 1],
                    ['q' => '¿Qué hace sync() en una relación BelongsToMany?', 'options' => ['Agrega IDs sin borrar los existentes', 'Sincroniza la tabla pivote borrando los que no están en el array', 'Solo borra registros', 'Es igual a attach()'], 'answer' => 1],
                ],
            ],
            [
                'slug' => 'migrations',
                'title' => 'Migrations & Schema',
                'icon' => '📦',
                'sections' => [
                    [
                        'title' => 'Crear y correr migraciones',
                        'content' => 'Las migraciones son control de versiones para tu base de datos.',
                        'code' => <<<'CODE'
// Crear migración
php artisan make:migration create_posts_table

// Estructura de una migración
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('body')->nullable();
        $table->enum('status', ['draft', 'published'])->default('draft');
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
        $table->softDeletes();
    });
}

// Comandos
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed
CODE,
                        'tip' => 'foreignId($col)->constrained() es el shortcut moderno. ¿Qué diferencia entre migrate:fresh y migrate:refresh? fresh BORRA todas las tablas, refresh hace rollback.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué diferencia hay entre migrate:fresh y migrate:refresh?', 'options' => ['Son idénticos', 'fresh borra todas las tablas, refresh hace rollback y re-migra', 'refresh borra todas las tablas, fresh hace rollback', 'fresh solo migra, refresh también hace seed'], 'answer' => 1],
                    ['q' => '¿Qué columnas crea $table->timestamps()?', 'options' => ['created_at únicamente', 'updated_at únicamente', 'created_at y updated_at', 'created_at, updated_at y deleted_at'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'middleware',
                'title' => 'Middleware',
                'icon' => '🔒',
                'sections' => [
                    [
                        'title' => '¿Qué es middleware y cómo crearlo?',
                        'content' => 'Middleware filtra las peticiones HTTP antes (o después) de que lleguen al controlador.',
                        'code' => <<<'CODE'
// Crear middleware
php artisan make:middleware EnsureUserIsAdmin

// app/Http/Middleware/EnsureUserIsAdmin.php
public function handle(Request $request, Closure $next): Response
{
    if (! $request->user()?->isAdmin()) {
        return redirect('/home')->with('error', 'Unauthorized');
    }
    return $next($request);
}

// Registrar en bootstrap/app.php (Laravel 11+)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => EnsureUserIsAdmin::class,
    ]);
})

// Aplicar en rutas
Route::get('/admin', [AdminController::class, 'index'])->middleware('admin');
CODE,
                        'tip' => '$next($request) es clave: si no lo llamas, la petición no llega al controlador.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué hace $next($request) dentro de un middleware?', 'options' => ['Termina la petición', 'Redirige al usuario', 'Pasa la petición al siguiente middleware/controlador', 'Registra la petición en logs'], 'answer' => 2],
                    ['q' => '¿En qué archivo se registran los aliases de middleware en Laravel 11?', 'options' => ['app/Http/Kernel.php', 'config/middleware.php', 'bootstrap/app.php', 'routes/web.php'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'service-container',
                'title' => 'Service Container & Providers',
                'icon' => '⚙️',
                'sections' => [
                    [
                        'title' => 'Service Container (IoC)',
                        'content' => 'El contenedor de servicios de Laravel gestiona las dependencias. Permite inyectar clases automáticamente.',
                        'code' => <<<'CODE'
// Binding simple
app()->bind(PaymentGateway::class, StripeGateway::class);

// Singleton (misma instancia siempre)
app()->singleton(CacheManager::class, function ($app) {
    return new CacheManager($app['config']['cache']);
});

// Resolver (injection automática en constructores)
class OrderController extends Controller
{
    public function __construct(
        private PaymentGateway $payment,
        private OrderRepository $orders,
    ) {}
}

// Service Provider
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, StripeGateway::class);
    }
}
CODE,
                        'tip' => 'Pregunta clásica: ¿diferencia entre bind() y singleton()? bind() crea nueva instancia cada vez, singleton() reutiliza la misma.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Cuál es la diferencia entre bind() y singleton() en el Service Container?', 'options' => ['No hay diferencia', 'singleton() crea nueva instancia cada vez', 'bind() crea nueva instancia cada vez, singleton() reutiliza la misma', 'bind() es para interfaces, singleton() para clases'], 'answer' => 2],
                    ['q' => '¿En qué método del Service Provider van los bindings?', 'options' => ['boot()', 'register()', 'handle()', 'init()'], 'answer' => 1],
                ],
            ],
            [
                'slug' => 'blade',
                'title' => 'Blade Templates',
                'icon' => '🎨',
                'sections' => [
                    [
                        'title' => 'Sintaxis esencial de Blade',
                        'content' => 'Blade es el motor de plantillas de Laravel. Compila a PHP puro y se cachea.',
                        'code' => <<<'CODE'
{{-- Comentario (no se renderiza en HTML) --}}

{{-- Escapado (seguro contra XSS) --}}
{{ $variable }}

{{-- Sin escapar (cuidado con XSS) --}}
{!! $htmlContent !!}

{{-- Condicionales --}}
@if($user->isAdmin())
    <span>Admin</span>
@else
    <span>User</span>
@endif

{{-- Loops --}}
@foreach($posts as $post)
    <p>{{ $loop->iteration }}. {{ $post->title }}</p>
@endforeach

{{-- Layouts --}}
@extends('layouts.app')
@section('content')
    <h1>Contenido</h1>
@endsection

{{-- Componentes --}}
<x-alert type="success" :message="$message" />
CODE,
                        'tip' => '{{ }} usa htmlspecialchars() — siempre úsalo por defecto. Solo usa {!! !!} cuando el HTML viene de fuente 100% confiable.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué diferencia hay entre {{ $var }} y {!! $var !!}?', 'options' => ['Ninguna', '{{ }} escapa HTML (seguro), {!! !!} no escapa (riesgo XSS)', '{!! !!} escapa HTML, {{ }} no', '{{ }} es para strings, {!! !!} para arrays'], 'answer' => 1],
                    ['q' => '¿Qué propiedad del objeto $loop indica si es la última iteración?', 'options' => ['$loop->end', '$loop->final', '$loop->last', '$loop->isLast'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'requests-validation',
                'title' => 'Requests & Validation',
                'icon' => '✅',
                'sections' => [
                    [
                        'title' => 'Form Requests y validación',
                        'content' => 'Laravel valida datos con reglas declarativas. Los Form Requests extraen la lógica del controlador.',
                        'code' => <<<'CODE'
// Crear Form Request
php artisan make:request StorePostRequest

// app/Http/Requests/StorePostRequest.php
class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'  => ['required', 'string', 'max:255'],
            'body'   => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'tags'   => ['array'],
            'image'  => ['nullable', 'image', 'max:2048'],
        ];
    }
}

// Controlador
public function store(StorePostRequest $request): RedirectResponse
{
    $validated = $request->validated();
    Post::create($validated);
    return redirect()->route('posts.index');
}
CODE,
                        'tip' => 'authorize() devuelve false → 403. En entrevistas: ¿por qué usar Form Request en vez de validate() en el controlador? → separación de responsabilidades, reutilizable, testeable.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué código HTTP retorna Laravel si authorize() devuelve false?', 'options' => ['401', '403', '404', '422'], 'answer' => 1],
                    ['q' => '¿Qué método retorna solo los datos que pasaron validación?', 'options' => ['$request->all()', '$request->input()', '$request->validated()', '$request->safe()'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'auth',
                'title' => 'Autenticación & Autorización',
                'icon' => '🔑',
                'sections' => [
                    [
                        'title' => 'Gates y Policies',
                        'content' => 'Gates son closures para autorización simple. Policies son clases para autorización basada en modelos.',
                        'code' => <<<'CODE'
// Gate simple
Gate::define('update-post', function (User $user, Post $post) {
    return $user->id === $post->user_id;
});

// Usar Gate
if (Gate::allows('update-post', $post)) { ... }
$this->authorize('update-post', $post);

// Policy
php artisan make:policy PostPolicy --model=Post

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}

// Sanctum (API tokens)
php artisan install:api
$token = $user->createToken('api-token')->plainTextToken;
Route::middleware('auth:sanctum')->get('/user', fn() => auth()->user());
CODE,
                        'tip' => 'Gate vs Policy: Gates para acciones generales, Policies para acciones sobre un modelo específico. Sanctum vs Passport: Sanctum es más simple (tokens + SPA), Passport es OAuth2 completo.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Cuándo preferirías una Policy sobre un Gate?', 'options' => ['Cuando la lógica es muy simple', 'Cuando la autorización está ligada a un modelo específico', 'Gates y Policies son intercambiables siempre', 'Cuando necesitas OAuth2'], 'answer' => 1],
                    ['q' => '¿Qué paquete de auth usa Laravel para APIs simples y SPAs?', 'options' => ['Passport', 'Fortify', 'Sanctum', 'Breeze'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'queues',
                'title' => 'Queues & Jobs',
                'icon' => '⏳',
                'sections' => [
                    [
                        'title' => 'Jobs y Queues',
                        'content' => 'Las queues permiten diferir trabajo pesado (emails, notificaciones) para no bloquear la respuesta HTTP.',
                        'code' => <<<'CODE'
// Crear Job
php artisan make:job SendWelcomeEmail

class SendWelcomeEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        Mail::to($this->user)->send(new WelcomeMail($this->user));
    }

    public function failed(\Throwable $e): void
    {
        Log::error("WelcomeEmail failed for {$this->user->id}");
    }
}

// Despachar
SendWelcomeEmail::dispatch($user);
SendWelcomeEmail::dispatch($user)->delay(now()->addMinutes(10));

// Correr worker
php artisan queue:work
php artisan queue:work --queue=emails,default
CODE,
                        'tip' => '¿qué driver de queue usarías en producción? Redis. ¿qué hace Horizon? Dashboard y gestión de workers Redis.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué interfaz debe implementar un Job para ejecutarse en queue?', 'options' => ['Queueable', 'ShouldQueue', 'Dispatchable', 'InteractsWithQueue'], 'answer' => 1],
                    ['q' => '¿Qué driver de queue se recomienda en producción?', 'options' => ['sync', 'database', 'redis', 'sqs'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'testing',
                'title' => 'Testing',
                'icon' => '🧪',
                'sections' => [
                    [
                        'title' => 'Feature & Unit Tests',
                        'content' => 'Laravel usa PHPUnit + helpers propios para testing. Feature tests prueban el stack completo, Unit tests prueban clases aisladas.',
                        'code' => <<<'CODE'
// Crear tests
php artisan make:test PostTest
php artisan make:test Services/TaxTest --unit

// Feature test
class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson('/api/posts', [
                             'title' => 'Mi Post',
                             'body'  => 'Contenido',
                         ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Mi Post']);

        $this->assertDatabaseHas('posts', ['title' => 'Mi Post']);
    }
}

// Factories
User::factory()->count(10)->create();
User::factory()->admin()->create();
CODE,
                        'tip' => 'RefreshDatabase usa transacciones (rápido). DatabaseMigrations hace migrate/rollback por test (más lento).',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué trait resetea la DB entre cada test de forma eficiente?', 'options' => ['DatabaseMigrations', 'RefreshDatabase', 'DatabaseTransactions', 'ResetDatabase'], 'answer' => 1],
                    ['q' => '¿Qué método simula un usuario autenticado en un Feature Test?', 'options' => ['$this->login($user)', '$this->auth($user)', '$this->actingAs($user)', '$this->withUser($user)'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'controllers',
                'title' => 'Controllers',
                'icon' => '🎮',
                'sections' => [
                    [
                        'title' => 'Controladores Básicos',
                        'content' => 'Los controladores agrupan la lógica de negocio. Reciben la petición HTTP y retornan una respuesta.',
                        'code' => <<<'CODE'
// Crear controlador
php artisan make:controller PostController

// Controlador básico
class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::latest()->get();
        return view('posts.index', compact('posts'));
    }

    public function store(PostRequest $request): RedirectResponse
    {
        Post::create($request->validated());
        return redirect()->route('posts.index');
    }

    public function show(Post $post): View
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        return view('posts.edit', compact('post'));
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());
        return redirect()->route('posts.show', $post);
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();
        return redirect()->route('posts.index');
    }
}
CODE,
                        'tip' => 'Usa --resource para generar un controlador con los 7 métodos CRUD: php artisan make:controller PostController --resource',
                    ],
                    [
                        'title' => 'Resource Controllers',
                        'content' => 'Los resource controllers proporcionan métodos RESTful estándar para CRUD.',
                        'code' => <<<'CODE'
// Rutas resource (automático)
Route::resource('posts', PostController::class);

// Métodos del resource controller:
// index   - GET /posts        - Listar todos
// create  - GET /posts/create - Mostrar formulario
// store   - POST /posts      - Guardar nuevo
// show    - GET /posts/{id}  - Mostrar uno
// edit    - GET /posts/{id}/edit - Editar formulario
// update  - PUT/PATCH /posts/{id} - Actualizar
// destroy - DELETE /posts/{id}   - Eliminar

// Solo algunos métodos
Route::resource('posts', PostController::class)->only(['index', 'show']);
Route::resource('posts', PostController::class)->except(['destroy']);
CODE,
                        'tip' => 'Route::apiResource() excluye create y edit (no necesarios en APIs). Combina con Route::resources() para múltiples recursos.',
                    ],
                    [
                        'title' => 'Dependencia en Controladores',
                        'content' => 'Inyecta dependencias en el constructor o directamente en los métodos.',
                        'code' => <<<'CODE'
// Inyección en constructor
class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth')->except('index');
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $this->userService->update($id, $request->validated());
        return back()->with('success', 'Usuario actualizado');
    }
}

// Inyección directa en método
public function show(Request $request, User $user)
{
    // $request tiene la petición actual
    // $user tiene el modelo inyectado por Route Model Binding
}
CODE,
                        'tip' => 'Laravel resuelve automáticamente las dependencias tipeadas. Si la clase no existe, lanza error claro. Usa interfaces para facilitar testing.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué comando crea un resource controller?', 'options' => ['php artisan make:controller Post --resource', 'php artisan make:controller PostController --resource', 'php artisan make:resource PostController', 'php artisan controller:make PostController'], 'answer' => 1],
                    ['q' => '¿Qué método de resource controller responde a POST /posts?', 'options' => ['index', 'create', 'store', 'show'], 'answer' => 2],
                    ['q' => '¿Cómo se llama la técnica de inyectar modelos en controladores por la ruta?', 'options' => ['Route Binding', 'Route Model Binding', 'Model Injection', 'Dependency Injection'], 'answer' => 1],
                ],
            ],
            [
                'slug' => 'events-listeners',
                'title' => 'Events & Listeners',
                'icon' => '🔔',
                'sections' => [
                    [
                        'title' => 'Sistema de Eventos',
                        'content' => 'Events y Listeners desacoplan lógica. Un evento puede tener múltiples listeners que reaccionan.',
                        'code' => <<<'CODE'
// Crear
php artisan make:event OrderPlaced
php artisan make:listener SendOrderConfirmation --event=OrderPlaced

// Evento
class OrderPlaced extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}
}

// Listener
class SendOrderConfirmation
{
    public function handle(OrderPlaced $event): void
    {
        Mail::to($event->order->user)->send(new OrderConfirmation($event->order));
    }
}

// Registrar en EventServiceProvider (Laravel <11) o en AppServiceProvider
protected $listen = [
    OrderPlaced::class => [
        SendOrderConfirmation::class,
        UpdateInventory::class,
        SendAdminNotification::class,
    ],
];

// Disparar evento
event(new OrderPlaced($order));
CODE,
                        'tip' => 'Usa Event::fake() en tests para evitar ejecutar listeners reales. Los eventos son ideales para lógica que no debe estar en el controlador.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué comando crea un evento y listener juntos?', 'options' => ['php artisan make:event-listener', 'php artisan event:generate', 'php artisan make:event Listener', 'php artisan generate:event-listener'], 'answer' => 1],
                    ['q' => '¿Cómo se dispara un evento manualmente?', 'options' => ['Event::fire(new OrderPlaced($order))', 'event(new OrderPlaced($order))', 'OrderPlaced::dispatch($order)', 'OrderPlaced::trigger($order)'], 'answer' => 1],
                ],
            ],
            [
                'slug' => 'notifications',
                'title' => 'Notifications',
                'icon' => '📧',
                'sections' => [
                    [
                        'title' => 'Notificaciones en Laravel',
                        'content' => 'Laravel Notifications permite enviar mensajes por múltiples canales (mail, SMS, Slack, DB).',
                        'code' => <<<'CODE'
// Crear notificación
php artisan make:notification OrderShipped

class OrderShipped extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'slack'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu orden ha sido enviada')
            ->line("Orden #{$this->order->id}")
            ->action('Ver orden', url('/orders/' . $this->order->id))
            ->line('Gracias por tu compra');
    }

    public function toArray(object $notifiable): array
    {
        return ['order_id' => $this->order->id, 'status' => 'shipped'];
    }
}

// Enviar
$user->notify(new OrderShipped($order));
Notification::send($users, new OrderShipped($order));

// Enviar luego (queue)
$user->notify((new OrderShipped($order))->delay(now()->addMinutes(5)));
CODE,
                        'tip' => 'Usa ShouldQueue en la notificación para enviarla asíncronamente. Las notificaciones en DB se ven con Notification::unreadNotifications().',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué método define los canales de una notificación?', 'options' => ['channels()', 'via()', 'send()', 'routes()'], 'answer' => 1],
                    ['q' => '¿Cómo enviar una notificación a múltiples usuarios?', 'options' => ['$user->notifyMany()', 'Notification::send()', 'Notification::batch()', 'notify()->all()'], 'answer' => 1],
                ],
            ],
            [
                'slug' => 'api-resources',
                'title' => 'API Resources',
                'icon' => '🌐',
                'sections' => [
                    [
                        'title' => 'Transformación de Datos API',
                        'content' => 'API Resources transforman modelos Eloquent a JSON. Separación clara entre estructura interna y respuesta API.',
                        'code' => <<<'CODE'
// Crear
php artisan make:resource PostResource
php artisan make:resource PostCollection

// Resource (transforma un item)
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->whenPivotLoaded('post_tags', $this->pivot->slug),
            'author' => new UserResource($this->whenLoaded('user')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->timestamp,
            'meta' => [
                'can_edit' => $request->user()?->can('update', $this->resource),
            ],
        ];
    }
}

// Collection (transforma colección)
class PostCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
            ],
        ];
    }
}

// Uso en controlador
return PostResource::collection(Post::with('user', 'tags')->paginate(10));
return new PostResource($post);
CODE,
                        'tip' => 'Usa $this->whenLoaded() para evitar cargar relaciones si no están disponibles. Cuando loaded() está disponible, solo entonces incluye el dato.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué comando crea un API Resource?', 'options' => ['php artisan make:api-resource', 'php artisan make:resource', 'php artisan make:api', 'php artisan make:json-resource'], 'answer' => 1],
                    ['q' => '¿Qué método evita incluir datos si la relación no está cargada?', 'options' => ['whenLoaded()', 'ifLoaded()', 'checkLoaded()', 'isLoaded()'], 'answer' => 0],
                ],
            ],
            [
                'slug' => 'sessions',
                'title' => 'Sessions & Cookies',
                'icon' => '🍪',
                'sections' => [
                    [
                        'title' => 'Session en Laravel',
                        'content' => 'Las sesiones permiten almacenar datos del usuario entre peticiones HTTP.',
                        'code' => <<<'CODE'
// Leyendo sesión
$value = session('key');
$value = session()->get('key');
$value = $request->session()->get('key');

// Con valor por defecto
session()->get('key', 'default');
session()->get('key', fn() => expensiveOperation());

// Escribir sesión
session(['key' => 'value']);
$request->session()->put('key', 'value');

// Olvdar valor
$request->session()->forget('key');
$request->session()->flush(); // borrar todo

// Flash (solo para la siguiente petición)
$request->session()->flash('status', 'Task completed!');
$request->session()->reflash(); // mantener todos los flashes

// Verificar existencia
session()->has('key');
session()->exists('key'); // existe aunque sea null
CODE,
                        'tip' => 'session() helper globally available es más limpio que $request->session(). En entrevistas: diferencia entre flash y put? flash dura solo una request.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué método de sesión dura solo una petición?', 'options' => ['put()', 'flash()', 'keep()', 'store()'], 'answer' => 1],
                    ['q' => '¿Cuál es la diferencia entre has() y exists()?', 'options' => ['has() verifica no null, exists() verifica existencia', 'Son idénticos', 'exists() es más lento', 'has() no existe en Laravel'], 'answer' => 0],
                ],
            ],
            [
                'slug' => 'file-storage',
                'title' => 'File Storage',
                'icon' => '📁',
                'sections' => [
                    [
                        'title' => 'Storage & Archivos',
                        'content' => 'Laravel Storage abstrae el manejo de archivos local o cloud (S3, Dropbox, etc).',
                        'code' => <<<'CODE'
// Configurar en config/filesystems.php
// drivers: local, ftp, sftp, s3, rackspace, dropbox

// Guardar archivo
Storage::put('file.txt', 'Contenido');
Storage::put('avatars/'.$user->id.'.jpg', $fileContent);

// Subir archivo desde request
$path = $request->file('avatar')->store('avatars', 'local');
$path = $request->file('avatar')->storeAs('avatars', 'nombre.jpg');

// Eliminar
Storage::delete('file.txt');
Storage::delete(['file1.txt', 'file2.txt']);

// Leer
$contents = Storage::get('file.txt');
$exists = Storage::disk('s3')->exists('file.jpg');

// URL pública
url(Storage::url('file.txt'));

// Descargar
return Storage::download('file.txt');
return Storage::download('file.txt', 'nombre.pdf');
CODE,
                        'tip' => 'Usa storage/app/public para archivos públicos (necesita php artisan storage:link). Para cloud, configura .env con credentials y cambia driver a s3.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué comando crea el enlace simbólico para storage?', 'options' => ['php artisan storage:link', 'php artisan storage:publish', 'php artisan link:storage', 'php artisan make:storage-link'], 'answer' => 0],
                    ['q' => '¿Qué método guarda archivo y retorna la ruta?', 'options' => ['save()', 'put()', 'store()', 'upload()'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'collections',
                'title' => 'Collections',
                'icon' => '📊',
                'sections' => [
                    [
                        'title' => 'Colecciones de Laravel',
                        'content' => 'Las colecciones son wrapper para arrays con métodos funcionales poderosos.',
                        'code' => <<<'CODE'
$posts = Post::all(); // Collection

// Transformar
$titles = $posts->pluck('title');
$posts->transform(fn($p) => strtoupper($p->title));

// Filtrar
$published = $posts->filter(fn($p) => $p->status === 'published');
$active = $posts->where('active', true);

// Ordenar
$sorted = $posts->sortBy('created_at');
$desc = $posts->sortByDesc('id');

// Buscar
$post = $posts->firstWhere('slug', 'mi-post');
$first = $posts->first(fn($p) => $p->isFeatured());

// Agregar/remover
$posts->push($newPost);
$filtered = $posts->reject(fn($p) => $p->isDraft);

// Métodos de array
$posts->map(fn($p) => $p->title)->toArray();
$posts->sum('price');
$posts->avg('rating');
$posts->count();

// Chunk
$chunks = $posts->chunk(10);
foreach ($chunks as $chunk) { ... }

// Pipe
$result = $posts
    ->filter(fn($p) => $p->published)
    ->sortBy('date')
    ->take(5)
    ->pluck('title');
CODE,
                        'tip' => 'Usa pipe() para encadenar muchas operaciones: $collection->pipe(fn($c) => $c->first()). Las colecciones son lazy con métodos como map() pero eager con all().',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué método extrae una sola columna como array?', 'options' => ['map()', 'pluck()', 'extract()', 'column()'], 'answer' => 1],
                    ['q' => '¿Qué método divide una colección en grupos?', 'options' => ['split()', 'partition()', 'chunk()', 'group()'], 'answer' => 2],
                ],
            ],
            [
                'slug' => 'error-handling',
                'title' => 'Manejo de Errores',
                'icon' => '🚨',
                'sections' => [
                    [
                        'title' => 'Excepciones y Handler',
                        'content' => 'Laravel maneja errores automáticamente. El Exception Handler (app/Exceptions/Handler.php) decide cómo renderizar cada tipo de excepción.',
                        'code' => <<<'CODE'
// Generar excepción personalizada
throw new \Exception('Algo salió mal');
throw new \RuntimeException('Error de runtime');

// Excepciones HTTP
throw new \Illuminate\Http\Exception\HttpResponseException(
    response()->json(['error' => 'No autorizado'], 401)
);
throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
throw new ModelNotFoundException::forModel(Post::class);

// Capturar en controlador
try {
    $post = Post::findOrFail($id);
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    return response()->json(['error' => 'Post no encontrado'], 404);
}

// Reportar excepción (log)
try {
    // código
} catch (\Exception $e) {
    report($e); // logea y continúa
    return back()->with('error', 'Algo falló');
}
CODE,
                        'tip' => 'Usa report($e) para loggear sin interrumpir. En producción, configura .env para evitar mostrar errores: APP_DEBUG=false. Personaliza render() en Handler para excepciones específicas.',
                    ],
                    [
                        'title' => 'Errores HTTP Personalizados',
                        'content' => 'Personaliza las páginas de error 404, 500, etc. en resources/views/errors/',
                        'code' => <<<'CODE'
// Crear página de error personalizada
// resources/views/errors/404.blade.php
@extends('errors::layout')
@section('title', 'Página no encontrada')
@section('message', 'La página que buscas no existe.')

// Error 500
resources/views/errors/500.blade.php

// Para APIs, las respuestas JSON son automáticas
// config/error-logging.php para configurar logueo

// Excepciones personalizadas
php artisan make:exception CustomException

class CustomException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
CODE,
                        'tip' => 'Usa abort(404), abort(403), abort(500) en cualquier momento. Para APIs, las respuestas son JSON automáticamente. Configura APP_DEBUG=false en producción.',
                    ],
                    [
                        'title' => 'Validación y Errores',
                        'content' => 'Los errores de validación se manejan automáticamente y se pasan a la vista como variable $errors.',
                        'code' => <<<'CODE'
// Validación básica en controlador
$request->validate([
    'title' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
]);

// Validación manual
$validator = Validator::make($request->all(), rules: [
    'name' => 'required',
]);

if ($validator->fails()) {
    return back()
        ->withErrors($validator)
        ->withInput();
}

// En Blade - mostrar errores
@if($errors->any())
    <ul>
    @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    </ul>
@endif

// Error específico
<input type="text" name="title">
@error('title')
    <span class="error">{{ $message }}</span>
@enderror
CODE,
                        'tip' => '$errors está disponible en todas las vistas automáticamente. Usa withErrors() para pasar errores personalizados. En APIs, retorna 422 con JSON de errores.',
                    ],
                ],
                'quiz' => [
                    ['q' => '¿Qué método loguea una excepción sin interrumpir?', 'options' => ['log()', 'report()', 'catch()', 'notify()'], 'answer' => 1],
                    ['q' => '¿Qué archivo crea las páginas de error personalizadas?', 'options' => ['resources/views/errors/*.blade.php', 'resources/views/pages/error.blade.php', 'app/Http/Errors/*.php', 'config/errors.php'], 'answer' => 0],
                    ['q' => '¿Qué variable global tiene los errores de validación en Blade?', 'options' => ['$error', '$errors', '$validation', '$messages'], 'answer' => 1],
                    ['q' => '¿Qué hace abort(404)?', 'options' => ['Redirige a /404', 'Lanza NotFoundHttpException', 'Loggea error', 'Envía email'], 'answer' => 1],
                ],
            ],
        ];
    }

    public static function interviewQuestions(): array
    {
        return [
            ['q' => '¿Qué es el Service Container en Laravel?', 'a' => 'Es el sistema de IoC (Inversion of Control) de Laravel. Gestiona la resolución de dependencias automáticamente. Cuando un controlador tiene un parámetro en su constructor, el contenedor lo resuelve e inyecta sin que lo pidas explícitamente.'],
            ['q' => '¿Cómo funciona el ciclo de vida de una petición en Laravel?', 'a' => '1. public/index.php carga el autoloader y crea la Application. 2. Se crea el HTTP Kernel. 3. Se ejecutan los middlewares globales. 4. El Router despacha la petición al controlador. 5. El controlador retorna una Response. 6. Los middlewares "after" se ejecutan. 7. Se envía la respuesta al cliente.'],
            ['q' => '¿Qué es el problema N+1 y cómo lo resuelves?', 'a' => 'Ocurre cuando haces 1 query para obtener N registros, y luego N queries para obtener sus relaciones. Ejemplo: $posts = Post::all(); foreach($posts as $p) { $p->author->name; } → 1 + N queries. Solución: Post::with("author")->get() → solo 2 queries.'],
            ['q' => '¿Diferencia entre hasOne y belongsTo?', 'a' => 'hasOne: la foreign key está en la OTRA tabla. User hasOne Profile → profiles.user_id. belongsTo: la foreign key está en TU tabla. Profile belongsTo User → profiles.user_id. Son las dos caras de la misma relación.'],
            ['q' => '¿Qué son los Observers en Eloquent?', 'a' => 'Clases que escuchan eventos del modelo (creating, created, updating, updated, deleting, deleted). Útiles para extraer lógica del modelo. php artisan make:observer UserObserver --model=User. Se registran en AppServiceProvider::boot() con User::observe(UserObserver::class).'],
            ['q' => '¿Diferencia entre session() y cache()?', 'a' => 'session() es específica del usuario (almacena datos por usuario entre requests). cache() es compartida entre todos los usuarios (ideal para datos costosos de calcular como configuraciones, listas estáticas). Cache tiene drivers: file, redis, memcached.'],
            ['q' => '¿Cuándo usarías un Event/Listener vs un Job?', 'a' => 'Events/Listeners: para desacoplar acciones del sistema (UserRegistered → SendWelcomeEmail, NotifyAdmin). Varios listeners pueden reaccionar a un evento. Jobs: para trabajo diferido o pesado específico. Un Job hace una cosa. Los Listeners pueden despachar Jobs.'],
            ['q' => '¿Qué es eager loading condicional (lazy eager loading)?', 'a' => 'load() carga relaciones en una colección ya obtenida: $posts = Post::all(); $posts->load("author"). Útil cuando no sabes si necesitas la relación hasta después de obtener los datos. Diferente de with() que va en la query inicial.'],
            ['q' => '¿Cuál es la diferencia entre un Controller y un Resource Controller?', 'a' => 'Un Controller básico puede tener cualquier método personalizado. Un Resource Controller tiene métodos RESTful estándar (index, create, store, show, edit, update, destroy) mapeados a rutas CRUD automáticamente con Route::resource().'],
            ['q' => '¿Qué es Route Model Binding?', 'a' => 'Es la inyección automática de modelos en controladores. Laravel busca un registro en la BD por el ID de la ruta y lo inyecta. Si no existe, lanza 404 automáticamente. Personalizable con resolve() o route key name.'],
            ['q' => '¿Cuándo usarías Events vs Jobs?', 'a' => 'Events: para notificaciones de algo que ocurrió (uno a muchos listeners). Jobs: para ejecutar una tarea específica, usualmente diferida. Los listeners pueden despachar jobs. Un evento es "algo pasó", un job es "haz esta tarea".'],
            ['q' => '¿Para qué sirve una API Resource en Laravel?', 'a' => 'Transforma modelos Eloquent a JSON estructurado para APIs. Permite controlar exactamente qué campos se exponen, renombrar, anidar relaciones, y añadir meta-información. Además permite formato condicional según permisos del usuario.'],
            ['q' => '¿Cuál es la diferencia entre session() y cache()?', 'a' => 'Session es por usuario (datos específicos de una sesión de usuario). Cache es global (datos compartidos entre todos los usuarios). Ejemplo session: carrito de compras. Ejemplo cache: configuración de la app que se lee frecuentemente.'],
            ['q' => '¿Qué es el problema N+1 y cómo evitarlo en Laravel?', 'a' => 'Hacer N+1 queries sin querer. Post::all() luego foreach $post->author = N+1. Solución: Post::with("author")->get() = 2 queries. También with(["author", "comments"]) para múltiples relaciones.'],
            ['q' => '¿Qué son los Accessors y Mutators en Eloquent?', 'a' => 'Accessors: transformar dato al leerlo (getNameAttribute). Mutators: transformar dato al guardarlo (setNameAttribute). Definidos en el modelo como métodos: function getXxxAttribute() / function setXxxAttribute().'],
        ];
    }
}
