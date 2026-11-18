# PulseWatch | Architecture

**Documento:** Arquitetura de Software  
**Versão:** 1.1  
**Status:** Aprovado para o MVP  
**Atualização:** 17/11/2026

## 1. Introdução

O PulseWatch utiliza uma arquitetura baseada em serviços desacoplados, com comunicação assíncrona por mensageria.

A solução separa as responsabilidades entre gestão de negócio, agendamento, execução de verificações, apresentação de informações e infraestrutura.

O objetivo é permitir evolução independente dos componentes, escalabilidade horizontal e tolerância a falhas.

## 2. Decisões arquiteturais aprovadas

| ID | Decisão | Tecnologia | Status |
|---|---|---|---|
| ADR-001 | Agendamento | Laravel Scheduler | Aprovado |
| ADR-002 | Motor de monitoramento | Python Worker + FastAPI | Aprovado |
| ADR-003 | Frontend | React 19 + Vite + TypeScript | Aprovado |

## 3. Stack tecnológica

| Componente | Tecnologia | Responsabilidade |
|---|---|---|
| Frontend | React 19 | Interface |
| Build frontend | Vite | Desenvolvimento e build |
| Linguagem frontend | TypeScript | Tipagem estática |
| Backend | PHP 8.x / Laravel | Regras de negócio |
| Scheduler | Laravel Scheduler | Agendamento |
| Monitoring Engine | Python 3.x | Execução de verificações |
| API interna Python | FastAPI | Health checks e administração |
| HTTP Client | HTTPX | Requisições HTTP |
| Assincronismo | asyncio | Concorrência |
| Mensageria | RabbitMQ | Comunicação entre serviços |
| Banco | PostgreSQL | Persistência oficial |
| Cache | Redis | Cache e locks |
| Containers | Docker | Isolamento dos serviços |
| Proxy | Nginx | Roteamento |
| CI/CD | GitHub Actions | Automação |

## 4. Componentes arquiteturais

### 4.1. Frontend

O frontend será uma aplicação SPA independente, desenvolvida com React 19, Vite e TypeScript.

Responsabilidades:
- Autenticação.
- Dashboard.
- Gestão de monitores.
- Consulta de incidentes.
- Histórico de verificações.
- Gráficos de desempenho.
- Configuração de notificações.

O frontend consumirá exclusivamente as APIs disponibilizadas pelo Laravel.

O processo de build será realizado pelo Vite, gerando os arquivos estáticos que serão disponibilizados pelo Nginx.

### 4.2. Backend Laravel

O Laravel será o núcleo de negócio da plataforma.

Responsabilidades:
- Autenticação e autorização.
- Gestão de usuários.
- Cadastro e configuração de monitores.
- Agendamento de verificações.
- Publicação de mensagens.
- Consumo dos resultados.
- Persistência dos históricos.
- Gestão de incidentes.
- Gerenciamento de notificações.
- Exposição de indicadores.

O Laravel será o proprietário dos dados de negócio.

### 4.3. Laravel Scheduler

O Laravel Scheduler será responsável por identificar os monitores que precisam ser verificados.

Funcionamento:

1. O Scheduler será executado periodicamente pelo ambiente.
2. O Laravel consultará os monitores ativos cujo próximo horário de execução foi atingido.
3. O sistema gerará um identificador único para cada verificação.
4. A solicitação será registrada para publicação.
5. O evento será publicado no RabbitMQ.
6. O próximo horário de verificação será atualizado.

Requisitos:
- Evitar agendamentos duplicados.
- Utilizar índices para localizar monitores elegíveis.
- Implementar locks quando necessário.
- Controlar a quantidade de mensagens publicadas por execução.
- Garantir recuperação de solicitações não publicadas.

Será avaliada a utilização do padrão Transactional Outbox para garantir consistência entre a persistência do agendamento e a publicação da mensagem.

### 4.4. Python Monitoring Engine

O motor Python será dividido em dois processos independentes.

**A. Monitoring Worker**

Responsável por:
- Consumir mensagens do RabbitMQ.
- Executar verificações HTTP/HTTPS.
- Utilizar HTTPX e asyncio.
- Medir latência.
- Identificar timeouts.
- Validar códigos HTTP esperados.
- Normalizar resultados.
- Publicar eventos de conclusão.

**B. FastAPI**

Responsável por:
- Health checks.
- Readiness checks.
- Informações operacionais do serviço.
- Endpoints administrativos internos, quando necessários.

A API FastAPI não será responsável pelo consumo principal das filas.

Os processos poderão compartilhar módulos, configurações e regras técnicas, mas terão ciclos de vida e escalabilidade independentes.

### 4.5. RabbitMQ

O RabbitMQ será utilizado para comunicação assíncrona.

Filas principais:

| Fila | Produtor | Consumidor |
|---|---|---|
| monitor.check.requested | Laravel | Python Worker |
| monitor.check.completed | Python Worker | Laravel |

Requisitos:
- Mensagens persistentes.
- Acknowledgements manuais.
- Publisher confirms.
- Retry com limite de tentativas.
- Dead Letter Exchange.
- Prefetch configurável.
- Mensagens versionadas.
- Identificadores de correlação.

### 4.6. PostgreSQL

Será o banco oficial da plataforma.

O Laravel será responsável pela persistência dos dados de negócio.

O Python não deverá realizar escritas diretamente nas tabelas de negócio.

### 4.7. Redis

Responsabilidades:
- Cache.
- Locks distribuídos.
- Coordenação de agendamentos.
- Rate limiting.
- Controle temporário de execução.

O Redis não será a fonte oficial de dados.

## 5. Fluxo de monitoramento

1. Laravel Scheduler identifica um monitor elegível.
2. Laravel registra uma solicitação de verificação.
3. A mensagem é publicada no RabbitMQ.
4. Python Worker consome a solicitação.
5. Python valida os parâmetros e a URL.
6. HTTPX executa a requisição.
7. O worker mede o tempo de resposta.
8. O resultado é normalizado.
9. O worker publica monitor.check.completed.
10. Laravel consome o resultado.
11. Laravel verifica o check_id e a idempotência.
12. O resultado é persistido.
13. As regras de incidentes são avaliadas.
14. Os indicadores são atualizados.

## 6. Contratos de mensageria

### 6.1. Solicitação

Routing key: `monitor.check.requested`

```json
{
  "event_id": "uuid",
  "event_type": "monitor.check.requested",
  "version": 1,
  "occurred_at": "2026-11-17T10:00:00Z",
  "data": {
    "check_id": "uuid",
    "monitor_id": 123,
    "url": "https://api.example.com/health",
    "method": "GET",
    "timeout_ms": 5000,
    "expected_status_codes": [200],
    "headers": {},
    "body": null
  }
}
```

### 6.2. Resultado

Routing key: `monitor.check.completed`

```json
{
  "event_id": "uuid",
  "event_type": "monitor.check.completed",
  "version": 1,
  "occurred_at": "2026-11-17T10:00:02Z",
  "data": {
    "check_id": "uuid",
    "monitor_id": 123,
    "success": true,
    "status_code": 200,
    "response_time_ms": 125,
    "error_type": null,
    "error_message": null,
    "checked_at": "2026-11-17T10:00:02Z"
  }
}
```

Os contratos serão versionados e documentados independentemente dos modelos internos de cada serviço.

## 7. Confiabilidade e processamento

A estratégia de entrega será at-least-once.

O sistema não assumirá entrega exactly-once.

Mecanismos obrigatórios:

- check_id único por verificação.
- event_id único por evento.
- Persistência idempotente.
- Publisher confirms.
- Manual acknowledgement.
- Retry limitado.
- Dead Letter Queue.
- Registro de mensagens não processadas.

O worker somente confirmará o consumo da solicitação depois de garantir a publicação confirmada do resultado.

O consumidor Laravel deverá confirmar a mensagem de resultado após a transação de persistência ser concluída.

## 8. Tratamento de falhas

### Falhas externas

- HTTP 500.
- HTTP 502.
- HTTP 503.
- Timeout.
- Falha DNS.
- Connection refused.
- Erro TLS.

Essas situações serão registradas como resultados válidos de monitoramento.

### Falhas internas

- Erro de processamento.
- Mensagem inválida.
- Falha de publicação.
- Indisponibilidade do broker.
- Erro de persistência.

Essas situações utilizarão retry controlado e DLQ.

## 9. Gestão de incidentes

A gestão de incidentes será responsabilidade do Laravel.

Regras:
- Falhas consecutivas configuráveis.
- Criação automática de incidentes.
- Associação de novas falhas ao incidente ativo.
- Resolução após sucessos consecutivos.
- Histórico de eventos.
- Controle de notificações duplicadas.

Estados:
- OPEN.
- ACKNOWLEDGED.
- RESOLVED.

## 10. Segurança

A plataforma deverá implementar:

- Autenticação e autorização.
- Comunicação autenticada entre serviços.
- TLS em produção.
- Gestão segura de credenciais.
- Rate limiting.
- Validação de URLs.
- Proteção contra SSRF.
- Bloqueio de redes privadas.
- Bloqueio de loopback e link-local.
- Proteção contra DNS rebinding.
- Controle de redirecionamentos.
- Restrição de protocolos e portas.

## 11. Escalabilidade

Os componentes deverão permitir escalabilidade independente.

O frontend poderá ser servido por múltiplas instâncias Nginx.

A API Laravel poderá receber múltiplas instâncias.

O número de workers Python poderá ser aumentado conforme o volume de verificações e o tamanho das filas.

A API FastAPI poderá ser escalada independentemente dos workers.

## 12. Observabilidade

Ferramentas previstas:
- Prometheus.
- Grafana.
- OpenTelemetry.

Indicadores:
- Quantidade de verificações.
- Tempo médio de resposta.
- Erros por serviço.
- Tamanho das filas.
- Mensagens em retry.
- Quantidade de workers.
- Tempo de processamento.
- Falhas de comunicação.

Os logs deverão ser estruturados e conter identificadores de correlação.

## 13. Infraestrutura

O ambiente inicial utilizará Docker Compose.

Serviços previstos:
- frontend.
- nginx.
- laravel-api.
- laravel-scheduler.
- laravel-queue-worker, quando necessário.
- monitoring-worker.
- monitoring-api.
- postgres.
- redis.
- rabbitmq.

## 14. Fora do escopo do MVP

- Kubernetes.
- Multi-região.
- Inteligência artificial para diagnóstico.
- Agentes geográficos distribuídos.
- Multi-tenancy avançado.
- Monitoramento de infraestrutura física.

## 15. Princípios arquiteturais

- Separation of Concerns.
- Clean Architecture quando aplicável.
- SOLID.
- Baixo acoplamento.
- Alta coesão.
- API First.
- Comunicação assíncrona.
- Idempotência.
- Tolerância a falhas.
- Segurança por padrão.
- Observabilidade.
- Evolução incremental.

## 16. Documentação de decisões

As decisões arquiteturais aprovadas estão registradas em:

- ADR-001: Laravel Scheduler.
- ADR-002: Python Worker independente com FastAPI.
- ADR-003: React 19 + Vite + TypeScript.

Novas decisões relevantes deverão ser documentadas em `docs/decisions/`.

---

**PulseWatch | Monitor what matters.**