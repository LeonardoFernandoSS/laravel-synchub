# 🧠 Laravel Synchub: Synchronization Platform
Laravel Synchub is a robust synchronization platform designed to streamline data synchronization across multiple sources. This platform provides a seamless way to manage synchronization processes, ensuring data consistency and integrity. With its modular architecture and extensive feature set, Laravel Synchub is an ideal solution for developers seeking to integrate synchronization capabilities into their applications.

## 🚀 Features
- **Modular Architecture**: Laravel Synchub features a modular design, allowing developers to easily extend and customize the platform to meet their specific needs.
- **Synchronization Workflows**: The platform supports complex synchronization workflows, enabling developers to define custom workflows tailored to their application's requirements.
- **Queue-Based Processing**: Laravel Synchub utilizes a queue-based processing system, ensuring efficient and scalable synchronization processing.
- **Error Handling and Logging**: The platform provides robust error handling and logging mechanisms, enabling developers to monitor and troubleshoot synchronization processes effectively.
- **Extensive Configuration Options**: Laravel Synchub offers a wide range of configuration options, allowing developers to fine-tune the platform to suit their specific use cases.

## 🛠️ Tech Stack
* **Laravel Framework**: Laravel Synchub is built on top of the Laravel framework, leveraging its robust features and extensive ecosystem.
* **PHP**: The platform is written in PHP, ensuring seamless integration with existing PHP-based applications.
* **MySQL**: Laravel Synchub supports MySQL as its primary database management system, providing reliable data storage and retrieval.
* **Redis**: The platform utilizes Redis for queue-based processing, ensuring efficient and scalable synchronization processing.
* **Laravel Queue**: Laravel Synchub leverages Laravel's built-in queue system, providing a robust and reliable way to manage synchronization processes.

## 📦 Installation
To install Laravel Synchub, follow these steps:
1. **Clone the Repository**: Clone the Laravel Synchub repository using Git.
2. **Install Dependencies**: Install the required dependencies using Composer.
3. **Configure Environment Variables**: Configure the environment variables in the `.env` file.
4. **Run Migrations**: Run the database migrations to create the necessary tables.
5. **Publish Configuration Files**: Publish the configuration files using the `php artisan vendor:publish` command.

## 💻 Usage

Laravel SyncHub provides multiple ways to work with synchronizations. You can trigger synchronization through HTTP controllers, application handlers, or Artisan commands, depending on your application's needs.

### 1. Create a Synchronization Context

Use the `make:synchub` Artisan command to generate the components required by a synchronization context.

```bash
php artisan make:synchub context Customer
```

This generates the context and its gateways:

```text
app/
└── Contexts/
    └── Customer/
        ├── CustomerSyncContext.php
        ├── CustomerSourceGateway.php
        └── CustomerTargetGateway.php
```

Additional components can be generated independently:

```bash
php artisan make:synchub mapper Customer
php artisan make:synchub validator Customer
php artisan make:synchub dependency Customer
php artisan make:synchub after-sync Customer
```

The command also supports nested contexts:

```bash
php artisan make:synchub context Orders/Customer
```

Existing directories are reused, and existing files are preserved unless `--force` is provided.

```bash
php artisan make:synchub context Customer --force
```

### 2. Trigger a Synchronization

#### HTTP

Laravel SyncHub provides HTTP endpoints through `SyncController`.

To synchronize a single entity:

```http
POST /synchub/{context}
```

Example:

```bash
curl -X POST /synchub/customer \
    -H "Content-Type: application/json" \
    -d '{"id": 123}'
```

To synchronize multiple entities:

```http
POST /synchub/{context}/batch
```

Example:

```bash
curl -X POST /synchub/customer/batch \
    -H "Content-Type: application/json" \
    -d '{"ids":[123,456,789]}'
```

Both endpoints support the `force` option.

```json
{
    "id": 123,
    "force": true
}
```

For batch synchronization:

```json
{
    "ids": [123, 456, 789],
    "force": true
}
```

The synchronization is queued and the HTTP endpoint returns immediately:

```json
{
    "status": "queued"
}
```

#### Application Handlers

Synchronizations can also be triggered directly through the application handlers. This is useful when the synchronization is started by another service, event, job, command, or internal application process.

For a single entity:

```php
use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartSyncHandler;

$handler->handle(
    new StartSync(
        context: 'customer',
        id: 123,
        force: false,
    )
);
```

For batch synchronization:

```php
use Synchub\LaravelSynchub\Application\Sync\Commands\StartBatchSync;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartBatchSyncHandler;

$handler->handle(
    new StartBatchSync(
        context: 'customer',
        ids: [123, 456, 789],
        force: false,
        chunkSize: 100,
    )
);
```

The handlers provide the same application-level entry point used by the HTTP controller, keeping the synchronization logic independent from the transport layer.

### 3. Artisan Commands

If your application exposes Artisan commands for triggering synchronizations, those commands should delegate to the same application handlers instead of implementing synchronization logic themselves.

For example:

```php
$startSyncHandler->handle(
    new StartSync(
        context: $context,
        id: $id,
        force: $force,
    )
);
```

This keeps the architecture consistent:

```text
HTTP Controller ──┐
                  │
Artisan Command ──┼──> Command ──> Handler ──> Synchronization
                  │
Job / Event ──────┘
```

The `make:synchub` command is responsible for generating synchronization components; it does not execute a synchronization itself.

### 4. Monitor Synchronization Processes

Synchronization processes can be monitored through the HTTP endpoints provided by `SyncController`.

List synchronization processes:

```http
GET /synchub
```

View a specific process:

```http
GET /synchub/{id}
```

Get the current process status and logs:

```http
GET /synchub/{id}/status
```

Rerun a completed or failed process:

```http
POST /synchub/{id}/rerun
```

The status endpoint returns information such as:

```json
{
    "id": 123,
    "status": "success",
    "status_label": "Success",
    "current_step": {
        "value": "finished",
        "label": "Finished"
    },
    "duration_seconds": 12,
    "finished": true,
    "logs": []
}
```

### 5. Architecture

The recommended flow is:

```text
                    ┌──────────────────┐
                    │   HTTP Request   │
                    └────────┬─────────┘
                             │
                    ┌────────▼─────────┐
                    │  SyncController  │
                    └────────┬─────────┘
                             │
                             │
┌────────────────┐   ┌───────▼────────┐   ┌────────────────┐
│ Artisan Command│──>│     Command    │<──│ Job / Event    │
└────────────────┘   └───────┬────────┘   └────────────────┘
                             │
                    ┌────────▼─────────┐
                    │      Handler     │
                    └────────┬─────────┘
                             │
                    ┌────────▼─────────┐
                    │   SyncContext    │
                    └────────┬─────────┘
                             │
          ┌──────────────────┼──────────────────┐
          │                  │                  │
          ▼                  ▼                  ▼
 SourceGateway          SyncMapper        SyncValidator
          │                  │                  │
          └──────────────────┼──────────────────┘
                             │
                             ▼
                     TargetGateway
                             │
                             ▼
                    AfterSyncHandler
```

This structure allows the synchronization flow to be reused regardless of how it is triggered.


## 📂 Project Structure
```markdown
laravel-synchub/
├── config/
│   ├── synchub.php
│   └── ...
├── src/
│   ├── Application/
│   │   ├── Sync/
│   │   │   ├── Pipeline/
│   │   │   │   ├── SyncWorkflowFactory.php
│   │   │   │   ├── SyncWorkflow.php
│   │   │   └── ...
│   │   ├── Sync/
│   │   │   ├── Services/
│   │   │   │   ├── SyncProcessService.php
│   │   │   │   └── ...
│   │   ├── Sync/
│   │   │   ├── Jobs/
│   │   │   │   ├── ProcessSync.php
│   │   │   │   └── ...
│   │   └── ...
│   └── ...
├── src/
│   ├── Infrastructure/
│   │   ├── Providers/
│   │   │   ├── LaravelSynchubServiceProvider.php
│   │   │   └── ...
│   │   └── ...
│   └── ...
├── ...
```

## 🤝 Contributing
To contribute to Laravel Synchub, please follow these steps:
1. **Fork the Repository**: Fork the Laravel Synchub repository using Git.
2. **Create a New Branch**: Create a new branch for your contribution.
3. **Make Changes**: Make the necessary changes to the codebase.
4. **Submit a Pull Request**: Submit a pull request with your changes.

## 📝 License
Laravel Synchub is licensed under the MIT License.

## 📬 Contact
For more information about Laravel Synchub, please contact us at [leonardo.fernando06@gmail.com](mailto:leonardo.fernando06@gmail.com).
