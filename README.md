# laravel-synchub

A Laravel package for building and managing reliable data synchronization processes between your application and external services.

The package provides a structured synchronization pipeline with support for individual and batch synchronization, process tracking, dependencies, retries, logging, and optional initial source payloads.

## Features

* **Individual synchronization** — Synchronize a single source entity.
* **Batch synchronization** — Process multiple source entities using Laravel job batches.
* **Optional initial payload** — Provide the source data when starting a synchronization and avoid an additional source lookup.
* **Fallback source loading** — When no initial payload is provided, the package loads the source data through the configured `SourceGateway`.
* **Process tracking** — Track synchronization status, current step, timestamps, payloads, responses, and errors.
* **Retry and rerun support** — Failed and completed processes can be executed again when allowed.
* **Dependencies** — Synchronizations can depend on other synchronization processes.
* **Triggered processes** — A synchronization can trigger other synchronization processes.
* **Logging** — Execution steps and relevant payloads can be recorded for monitoring and debugging.
* **Extensible architecture** — Source and target integrations can be implemented through dedicated gateways and contracts.

---

## Installation

You can install the package via Composer:

```bash
composer require leonardofernandoss/laravel-synchub
```

### Configuration

Publish the SyncHub configuration file:

```bash
php artisan vendor:publish --tag=laravel-synchub-config
```

This will create:

```plaintext
config/synchub.php
```

The configuration file allows you to customize the package behavior, including route configuration.

### Migrations

SyncHub automatically loads its migrations. No migration publishing is required. After installing the package, simply run:

```bash
php artisan migrate
```

### Dashboard Views

SyncHub includes a web dashboard for monitoring synchronization processes, including status, logs, payloads, dependencies and related processes.

The dashboard views are loaded automatically by the package. If you want to customize them, publish the views:

```bash
php artisan vendor:publish --tag=synchub-views
```

The views will be published to:

```plaintext
resources/views/vendor/synchub/
```

You can then customize the published Blade files without modifying the package source code.

### Dashboard Routes

The SyncHub web and API routes are loaded automatically by the package. Routes can be enabled or disabled through:

```php
'routes' => [
    'enabled' => true,
],
```

in `config/synchub.php`.

When enabled, the package registers the routes required by the synchronization API and dashboard. No route publishing or manual route registration is required.

### Dashboard Access

After installation, migrations and configuration, the dashboard can be accessed through the route registered by SyncHub.

For production applications, it is recommended to protect the dashboard with your application's authentication or authorization middleware.

---

## Registering a Sync Context

Before executing a synchronization, you must register the context in the SyncHub registry.
The registration creates the relationship between the identifier used by the synchronization entry point and the corresponding `SyncContext` and `SourceIdentityResolver` classes.

### Overview Flow

```plaintext
1. Create the context
       ↓
2. Register the context
       ↓
3. Use the identifier
       ↓
4. Start synchronization
```

### 1. Generate a Context

Use the `make:synchub-context` command with the `--identifier` option:

```bash
php artisan make:synchub-context Customer --identifier=customers
```

This command creates the synchronization components for the `Customer` context in `app/Contexts/Customer/`:

```plaintext
app/Contexts/Customer/
├── CustomerSyncContext.php
├── CustomerSourceGateway.php
├── CustomerTargetGateway.php
├── CustomerSourceIdentityResolver.php
├── CustomerSyncMapper.php
├── CustomerSyncValidator.php
├── CustomerSyncDependencyChecker.php
└── CustomerAfterSyncHandler.php
```

The `--identifier` option defines the name (`customers`) that will be used to reference this context from routes, commands, jobs, or other synchronization entry points.

```plaintext
customers
    ↓
CustomerSyncContext
    ↓
Synchronization pipeline
```

### 2. Register the Context

After creating the context, register it using the `Sync` facade:

```php
use Synchub\LaravelSynchub\Domain\Sync\Facades\Sync;
use App\Contexts\Customer\CustomerSyncContext;
use App\Contexts\Customer\CustomerSourceIdentityResolver;

Sync::register(
    'customers',
    CustomerSyncContext::class,
    CustomerSourceIdentityResolver::class,
);
```

#### Arguments Breakdown

| Argument | Description |
| :--- | :--- |
| `'customers'` | Unique identifier used to reference the synchronization context in endpoints and commands. |
| `CustomerSyncContext::class` | Class responsible for defining the synchronization context and dependencies. |
| `CustomerSourceIdentityResolver::class` | Class responsible for resolving the source entity identity. |

### Context Identifier Resolution

The identifier is the key used by SyncHub to resolve the registered context.

When a route or command references `"customers"`, SyncHub internally resolves it through the registry:

```plaintext
"customers"
     │
     ▼
SyncRegistry
     │
     ├── SyncContext
     │      └── CustomerSyncContext
     │
     └── SourceIdentityResolver
            └── CustomerSourceIdentityResolver
```

If the identifier has not been registered, SyncHub will throw an exception:

```plaintext
Sync context [customers] not registered.
```

### Where to Register

Context registrations are stored **in-memory at runtime** (not persisted in a database or config file). Therefore, registrations must occur on **every application boot** before any synchronization is executed.

A Service Provider is the recommended place for registration:

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Synchub\LaravelSynchub\Domain\Sync\Facades\Sync;
use App\Contexts\Customer\CustomerSyncContext;
use App\Contexts\Customer\CustomerSourceIdentityResolver;

class SyncServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Sync::register(
            'customers',
            CustomerSyncContext::class,
            CustomerSourceIdentityResolver::class,
        );
    }
}
```

Once registered, the identifier can be used by your synchronization routes, commands, jobs, or other entry points:

```plaintext
route/command
     │
     │ "customers"
     ▼
SyncRegistry
     │
     ▼
CustomerSyncContext
     │
     ▼
Sync pipeline
```

---

## Artisan Commands

SyncHub provides Artisan commands to generate synchronization contexts and their components.

### Generate a complete synchronization context

Create a complete synchronization context with an explicit identifier:

```bash
php artisan make:synchub-context Customer --identifier=customers
```

The command also supports nested contexts:

```bash
php artisan make:synchub-context Sales/Customer --identifier=customers
```

If files already exist, the command keeps the existing files by default. You can overwrite existing files using:

```bash
php artisan make:synchub-context Customer --identifier=customers --force
```

### Generate individual synchronization components

Individual components can also be generated using:

```bash
php artisan make:synchub {type} {name}
```

Available component types:
* `context`
* `identity-resolver`
* `mapper`
* `validator`
* `dependency`
* `after-sync`

Examples:

```bash
php artisan make:synchub context Customer
php artisan make:synchub identity-resolver Customer
php artisan make:synchub mapper Customer
php artisan make:synchub validator Customer
php artisan make:synchub dependency Customer
php artisan make:synchub after-sync Customer
```

Use `--force` to overwrite existing files:

```bash
php artisan make:synchub mapper Customer --force
```

---

## Recommended Setup

For a new synchronization, the simplest approach is to generate the complete context:

```bash
php artisan make:synchub-context Customer --identifier=customers
```

Then implement the generated components according to the requirements of the integration. A typical context may contain:

```plaintext
app/
└── Contexts/
    └── Customer/
        ├── CustomerSyncContext.php
        ├── CustomerSourceGateway.php
        ├── CustomerTargetGateway.php
        ├── CustomerMapper.php
        ├── CustomerValidator.php
        ├── CustomerDependencyChecker.php
        ├── CustomerAfterSyncHandler.php
        └── CustomerSourceIdentityResolver.php
```

Not all components are required for every synchronization. A simple synchronization may only require:
* `CustomerSyncContext`
* `CustomerSourceGateway`
* `CustomerTargetGateway`

While a more complex integration may additionally use:
* `CustomerSourceIdentityResolver`
* `CustomerMapper`
* `CustomerValidator`
* `CustomerDependencyChecker`
* `CustomerAfterSyncHandler`

The generated classes are starting points. Their implementations should contain the application-specific integration logic for the source system, target system, validation, mapping, dependencies, and post-synchronization behavior.

---

## Concepts

A synchronization is identified by a context and a source.
* The **context** (identifier) identifies the synchronization definition.
* The **source** identifies the entity being synchronized.

For example:

```yaml
context: customers
source: 123
```

The package resolves the source identity through the configured identity resolver and then executes the synchronization pipeline.

---

## Synchronization Contracts

Each synchronization context is composed of a set of contracts that define how the source data is loaded, validated, mapped, sent to the target service, persisted, and optionally followed by additional actions.

The main contracts available to a synchronization context are:
* `SourceGateway`
* `TargetGateway`
* `MappingRepository`
* `SyncMapper`
* `SyncValidator`
* `SyncDependencyChecker`
* `AfterSyncHandler`

Not every synchronization needs to implement all contracts. The `SyncMapper`, `SyncValidator`, `SyncDependencyChecker`, and `AfterSyncHandler` are optional.

### SyncContext

The `SyncContext` is the object that connects the synchronization implementation with the SyncHub pipeline.

```php
use Synchub\LaravelSynchub\Domain\Sync\Context\SyncContext;

final class CustomerSyncContext extends SyncContext
{
    public function __construct()
    {
        parent::__construct(
            source: new CustomerSourceGateway(),
            target: new CustomerTargetGateway(),
            repository: new CustomerMappingRepository(),
            mapper: new CustomerSyncMapper(),
            validator: new CustomerSyncValidator(),
        );
    }
}
```

The required dependencies are:

```php
public function __construct(
    public SourceGateway $source,
    public TargetGateway $target,
    public MappingRepository $repository,
    public ?SyncMapper $mapper = null,
    public ?SyncValidator $validator = null,
    public ?SyncDependencyChecker $dependencyChecker = null,
    public ?AfterSyncHandler $afterSync = null,
)
```

Therefore, a minimal synchronization requires:
1. A `SourceGateway`
2. A `TargetGateway`
3. A `MappingRepository`

The remaining contracts can be added according to the requirements of the synchronization.

### SourceGateway

The `SourceGateway` is responsible for retrieving the source data and storing the target identifier associated with the source entity.

```php
interface SourceGateway
{
    public function find(
        SourceIdentity $identity,
    ): ?array;

    public function saveTargetId(
        SourceIdentity $identity,
        TargetIdentity $targetIdentity,
    ): void;
}
```

Example:

```php
final class CustomerSourceGateway implements SourceGateway
{
    public function find(
        SourceIdentity $identity,
    ): ?array {
        return Customer::query()
            ->whereKey($identity->value())
            ->first()?->toArray();
    }

    public function saveTargetId(
        SourceIdentity $identity,
        TargetIdentity $targetIdentity,
    ): void {
        Customer::query()
            ->whereKey($identity->value())
            ->update([
                'target_id' => $targetIdentity->value(),
            ]);
    }
}
```

#### Initial source payload

The source lookup is optional when the synchronization is started with an initial payload. For example:

```json
{
    "source": 123,
    "data": {
        "id": 123,
        "name": "John",
        "email": "john@example.com"
    }
}
```

When data is provided, the synchronization can use this payload instead of calling `$sourceGateway->find($identity);`. When no initial payload is provided, the existing source loading flow is used.

### TargetGateway

The `TargetGateway` is responsible for creating or updating the entity in the external target system.

```php
interface TargetGateway
{
    public function create(
        SyncData $data
    ): SyncResultData;

    public function update(
        TargetIdentity $identity,
        SyncData $data
    ): SyncResultData;
}
```

The gateway provides two operations:
* `create()` — creates a new entity in the target system.
* `update()` — updates an existing entity using its `TargetIdentity`.

Example:

```php
final class CustomerTargetGateway implements TargetGateway
{
    public function create(
        SyncData $data
    ): SyncResultData {
        $response = Http::post(
            'https://api.example.com/customers',
            $data->toArray(),
        );

        return SyncResultData::fromResponse(
            $response->json(),
        );
    }

    public function update(
        TargetIdentity $identity,
        SyncData $data
    ): SyncResultData {
        $response = Http::put(
            "https://api.example.com/customers/{$identity->value()}",
            $data->toArray(),
        );

        return SyncResultData::fromResponse(
            $response->json(),
        );
    }
}
```

### MappingRepository

The `MappingRepository` stores the relationship between the source entity and the corresponding target entity.

```php
interface MappingRepository
{
    public function find(
        string $context,
        SourceIdentity $identity,
    ): ?MappingEntity;

    public function create(
        string $context,
        SourceIdentity $identity,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity;

    public function update(
        MappingEntity $mapping,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity;
}
```

Conceptually:

```plaintext
Source Identity
    │
    ▼
MappingRepository
    │
    ├── No mapping ──► TargetGateway::create()
    │
    └── Mapping exists ──► TargetGateway::update()
```

### SyncMapper

The `SyncMapper` transforms source data into the structure expected by the target system.

```php
interface SyncMapper
{
    public function map(
        array $data,
        array $lastPayload,
    ): SyncData;
}
```

Example:

```php
final class CustomerSyncMapper implements SyncMapper
{
    public function map(
        array $data,
        array $lastPayload,
    ): SyncData {
        return new SyncData([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }
}
```

The second argument, `$lastPayload`, contains the previous mapped payload when available, allowing the mapper to account for historical data.

### SyncValidator

The `SyncValidator` validates the source data before it continues through the synchronization pipeline.

```php
interface SyncValidator
{
    /**
     * @return array $errors
     */
    public function validate(
        array $data,
        array $lastPayload,
    ): array;
}
```

Example:

```php
final class CustomerSyncValidator implements SyncValidator
{
    public function validate(
        array $data,
        array $lastPayload,
    ): array {
        $validator = Validator::make(
            $data,
            [
                'name' => ['required', 'string'],
                'email' => ['required', 'email'],
            ],
        );

        return $validator->errors()->toArray();
    }
}
```

### SyncDependencyChecker

The `SyncDependencyChecker` allows a synchronization to declare or resolve dependencies based on its source data.

```php
interface SyncDependencyChecker
{
    /**
     * @return SyncDependencyData[]
     */
    public function check(array $data): array;
}
```

This contract is optional and should be used when a synchronization cannot execute until another synchronization completes (e.g., a Product depending on its Category).

### AfterSyncHandler

The `AfterSyncHandler` allows application-specific logic to run after the synchronization pipeline completes. Unlike other contracts, it is an abstract class.

```php
abstract class AfterSyncHandler
{
    abstract public function handle(
        SyncProcessEntity $process,
    ): void;
}
```

Example:

```php
final class CustomerAfterSyncHandler extends AfterSyncHandler
{
    public function handle(
        SyncProcessEntity $process,
    ): void {
        event(
            new CustomerSynchronized(
                $process->sourceIdentity,
            )
        );
    }
}
```

### SourceIdentityResolver

The `SourceIdentityResolver` converts the value received by the synchronization endpoint into a `SourceIdentity`.

```php
interface SourceIdentityResolver
{
    public function resolve(
        mixed $source,
    ): SourceIdentity;

    /**
     * @param array<mixed> $sources
     * @return array<SourceIdentity>
     */
    public function resolveMany(
        array $sources,
    ): array;
}
```

---

## Endpoints

### Individual Synchronization

An individual synchronization can be started through the synchronization endpoint:

```http
POST /synchub/{context}/sync
```

Where `{context}` is the registered identifier (e.g., `customers`).

Example request body:

```json
{
    "source": 123
}
```

Providing the initial payload:

```json
{
    "source": 123,
    "data": {
        "id": 123,
        "name": "John",
        "email": "john@example.com"
    }
}
```

Force synchronization:

```json
{
    "source": 123,
    "data": {
        "id": 123,
        "name": "John"
    },
    "force": true
}
```

### Batch Synchronization

Multiple source entities can be synchronized using the batch endpoint:

```http
POST /synchub/{context}/batch
```

Example request body:

```json
{
    "sources": [
        123,
        456,
        789
    ]
}
```

Initial payloads in batch synchronization:

```json
{
    "sources": [
        123,
        456,
        789
    ],
    "data": {
        "123": {
            "id": 123,
            "name": "John"
        },
        "789": {
            "id": 789,
            "name": "Mary"
        }
    }
}
```

---

## Synchronization Process & Monitoring

Each synchronization creates a process that tracks its execution state (`pending`, `processing`, `waiting_dependency`, `success`, `failed`, `error`, `obsolete`), current step, initial payloads, target responses, errors, and execution duration.

### Pipeline Architecture

```plaintext
Request
    ↓
StartSync / StartBatchSync
    ↓
Create Sync Process
    ↓
Synchronization Pipeline
    ↓
Load Source Data (Payload or SourceGateway::find)
    ↓
SyncValidator
    ↓
SyncMapper
    ↓
MappingRepository (Check mapping)
    ├── No mapping ──► TargetGateway::create()
    └── Mapping exists ──► TargetGateway::update()
    ↓
Save Target Identity
    ↓
AfterSyncHandler
    ↓
Complete Process
```

---

## Contributing

Please read `CONTRIBUTING.md` for information about the contribution process and development guidelines.

## License

This project is licensed under the MIT License.