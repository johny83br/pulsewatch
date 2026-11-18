# ADR-001: Laravel Scheduler

**Status:** Accepted  
**Data:** 17/11/2026

## Contexto

O PulseWatch precisa executar verificações periódicas nos monitores cadastrados, respeitando intervalos configuráveis e evitando execuções duplicadas.

## Decisão

Utilizar o Laravel Scheduler como mecanismo central de agendamento.

O Scheduler será responsável por identificar monitores elegíveis e publicar solicitações de verificação no RabbitMQ.

## Justificativa

- Aproveitamento dos recursos nativos do Laravel.
- Centralização das regras de agendamento.
- Integração direta com os modelos de negócio.
- Facilidade de manutenção.
- Menor complexidade inicial.

## Consequências

**Positivas:**
- Menor quantidade de componentes.
- Agendamento centralizado.
- Facilidade de integração com PostgreSQL.
- Controle de execução pelo backend principal.

**Atenções:**
- Evitar consultas desnecessárias ao banco.
- Implementar locks para concorrência.
- Garantir recuperação de mensagens não publicadas.
- Avaliar Transactional Outbox.

## Alternativas consideradas

- Scheduler implementado em Python.
- Serviço externo de agendamento.

## Resultado

Laravel Scheduler será a única autoridade de agendamento do MVP.