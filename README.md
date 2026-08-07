# Laravel SyncHub

[![Latest Version on Packagist](https://shields.io)](https://packagist.org)
[![Total Downloads](https://shields.io)](https://packagist.org)
[![Software License](https://shields.io)](LICENSE.md)
[![PHP Version](https://shields.io)](https://php.net)
[![Laravel Version](https://shields.io)](https://laravel.com)

O **Laravel SyncHub** é um ecossistema robusto para sincronização de dados e gerenciamento de barramentos de integração. Ele abstrai a complexidade de rotinas descentralizadas, transformando fluxos de dados isolados em pipelines monitoráveis, previsíveis e altamente extensíveis.

---

## 📌 O Problema que o SyncHub Resolve

Integrar ecossistemas descentralizados manualmente costuma gerar códigos duplicados, arquiteturas frágeis e falta de rastreabilidade. Lidar com fluxos de retries, validações de payloads e depuração de falhas operacionais torna-se insustentável a longo prazo.

Além disso, gerenciar a **árvore de dependências entre registros** (ex: impedir a sincronização de um *Pedido* se o *Cliente* correspondente falhou ou ainda não foi processado) exige uma lógica complexa e propensa a erros.

O **Laravel SyncHub** padroniza essa infraestrutura. Ele encapsula o ciclo de vida completo de cada transação, garantindo consistência técnica e de negócio para o seu projeto.

---

## ⚡ Principais Funcionalidades

*   **Abstração por Contextos:** Isolamento completo das regras de integração por domínios de negócio (ex: Vendas, Estoque, CRM).
*   **Pipeline de Processamento:** Fluxo nativo estruturado em etapas rígidas, previsíveis e customizáveis.
*   **Execução Assíncrona Nativa:** Integração profunda com as filas (*Laravel Queue*) para processamento distribuído de alta performance.
*   **Gestão de Árvore de Dependências:** Bloqueio e liberação automática de processos vinculados a dependências pendentes.
*   **Máquina de Estados Estrita:** Controle rigoroso e centralizado do status de cada execução para evitar condições de corrida.
*   **Rastreamento e Logs Granulares:** Histórico cronológico detalhado por etapa, facilitando a auditoria e o monitoramento.
*   **Tratamento Avançado de Falhas:** Captura inteligente de exceções com suporte nativo a reexecuções parciais ou totais (*rerun*).

---

## 🛠 Requisitos

*   **PHP:** `^8.1` ou superior
*   **Laravel:** `^10.0` | `^11.0` | `^12.0`
*   **Banco de Dados:** MySQL 8+, PostgreSQL 13+ ou equivalente (com suporte a JSON)
*   **Driver de Fila:** Redis, Database ou SQS (recomendado driver assíncrono)

---

## 🚀 Instalação

Instale o pacote via Composer:

```bash
composer require seu-vendor/laravel-synchub
```

Publique e execute as migrations para criar as tabelas de controle de estados, logs e dependências:

```bash
php artisan synchub:install
php artisan migrate
```

*(Opcional)* Publique o arquivo de configuração se precisar customizar as filas padrão ou o comportamento de retry:

```bash
php artisan vendor:publish --tag="synchub-config"
```

---

## 💻 Exemplo Prático de Uso

### 1. Definindo um Contexto com Dependências

O SyncHub permite que você isole seus domínios de integração. No exemplo abaixo, a sincronização de um pedido (`OrderSync`) aguarda de forma transparente caso o cliente (`CustomerSync`) ainda precise ser processado.

```php
namespace App\Sync\Contexts;

use Vendor\SyncHub\Context;
use Vendor\SyncHub\Facades\SyncHub;

class OrderSync extends Context
{
    /**
     * Define as dependências que precisam estar resolvidas antes deste processo rodar.
     */
    public function dependencies(array \$payload): array
    {
        return [
            SyncHub::dependency(CustomerSync::class, \$payload['customer_id'])
        ];
    }

    /**
     * Executa o pipeline de sincronização do registro.
     */
    public function handle(array \$payload): void
    {
        // Seu fluxo estruturado de integração entra aqui
        // Ex: HTTP::post('api/orders', \$payload);
    }
}
```

### 2. Disparando a Sincronização

Basta chamar o facade do SyncHub passando o contexto e o payload. O pacote cuidará do enfileiramento, verificação de dependências e logs automaticamente.

```php
use App\Sync\Contexts\OrderSync;
use Vendor\SyncHub\Facades\SyncHub;

SyncHub::dispatch(OrderSync::class, [
    'order_id' => 4589,
    'customer_id' => 123,
    'total' => 150.00
]);
```

---

## 📊 Comandos Artisan

O pacote disponibiliza comandos CLI para ajudar no gerenciamento da infraestrutura:

```bash
# Monitora o status atual dos processos travados ou pendentes
php artisan synchub:status

# Tenta reexecutar um processo específico que falhou
php artisan synchub:retry {process_id}

# Limpa logs antigos de transações concluídas com sucesso
php artisan synchub:clear --days=30
```

---

## 📄 Licença

Este projeto é um software open-source licenciado sob a [MIT License](LICENSE.md).
