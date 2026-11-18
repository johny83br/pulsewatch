# ADR-002: Python Worker independente com FastAPI

**Status:** Accepted  
**Data:** 17/11/2026

## Contexto

O PulseWatch precisa executar verificações HTTP concorrentes, processando solicitações de monitoramento de maneira assíncrona.

Também precisa disponibilizar informações internas de saúde e administração do serviço Python.

## Decisão

Adotar Python com dois processos independentes:

1. Monitoring Worker.
2. FastAPI.

O Monitoring Worker será responsável pelo consumo das filas RabbitMQ e execução das verificações.

FastAPI será responsável por health checks, readiness checks e endpoints internos.

Ambos poderão compartilhar módulos e regras técnicas.

## Tecnologias

- Python 3.x.
- asyncio.
- HTTPX.
- aio-pika.
- FastAPI.
- Pydantic.
- Pytest.

## Justificativa

- Separação entre processamento e interface HTTP.
- Escalabilidade independente.
- Melhor isolamento de falhas.
- Processamento concorrente.
- Facilidade de testes.
- Possibilidade de adicionar novos tipos de verificações.

## Consequências

**Positivas:**
- Workers independentes.
- Melhor controle de concorrência.
- Separação clara de responsabilidades.
- Possibilidade de executar múltiplas instâncias.

**Atenções:**
- Gerenciamento de conexão com RabbitMQ.
- Controle de concorrência.
- Tratamento de cancelamento de tarefas.
- Garantia de publicação dos resultados.
- Monitoramento da saúde dos workers.

## Alternativas consideradas

- Processamento dentro do FastAPI.
- Implementação do motor em Laravel.

## Resultado

O worker Python será um processo independente e o FastAPI terá ciclo de vida próprio.

A escalabilidade dos dois componentes será independente.