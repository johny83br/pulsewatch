# PulseWatch

### API & Service Reliability Monitoring

Plataforma inteligente para monitoramento contínuo de APIs, aplicações e serviços externos, permitindo acompanhar disponibilidade, desempenho, identificar incidentes e receber notificações automaticamente.

**Versão:** 0.1.0  
**Status:** Planejamento e definição arquitetural  
**Licença:** A definir

---

## 1. Sobre o projeto

O PulseWatch é uma plataforma de monitoramento de APIs e serviços desenvolvida com o objetivo de oferecer visibilidade sobre a disponibilidade e a confiabilidade de aplicações.

A solução permitirá cadastrar endpoints, executar verificações periódicas, registrar métricas de desempenho, identificar indisponibilidades e gerenciar incidentes automaticamente.

O projeto utiliza uma arquitetura híbrida, combinando PHP, Python e TypeScript, com comunicação assíncrona entre componentes.

O PulseWatch também tem como objetivo explorar práticas modernas de engenharia de software, incluindo arquitetura distribuída, mensageria, processamento concorrente, observabilidade, testes automatizados e infraestrutura como código.

## 2. Objetivos

- Monitorar continuamente APIs e serviços HTTP/HTTPS.
- Identificar indisponibilidades e degradações de desempenho.
- Registrar histórico de verificações.
- Gerenciar incidentes automaticamente.
- Disponibilizar indicadores de disponibilidade e desempenho.
- Enviar notificações sobre incidentes e recuperações.
- Permitir acompanhamento centralizado de múltiplos serviços.
- Oferecer uma arquitetura escalável e extensível.

## 3. Funcionalidades

### 3.1. Gestão de usuários
- Cadastro e autenticação.
- Gerenciamento de perfil.
- Controle de acesso.
- Recuperação de senha.

### 3.2. Gestão de monitores
- Cadastro de serviços e endpoints.
- Configuração de métodos HTTP.
- Configuração de headers e parâmetros.
- Definição de intervalos de monitoramento.
- Configuração de timeout.
- Definição de códigos HTTP esperados.
- Ativação e desativação de monitores.

### 3.3. Motor de monitoramento
- Execução periódica de verificações.
- Requisições HTTP/HTTPS assíncronas.
- Medição do tempo de resposta.
- Identificação de erros HTTP.
- Identificação de timeouts.
- Registro dos resultados.
- Processamento concorrente.

### 3.4. Gestão de incidentes
- Identificação automática de falhas.
- Controle de falhas consecutivas.
- Criação automática de incidentes.
- Reconhecimento de incidentes.
- Resolução automática após recuperação.
- Histórico de eventos.

### 3.5. Dashboard
- Total de serviços monitorados.
- Serviços disponíveis e indisponíveis.
- Incidentes ativos.
- Percentual de disponibilidade.
- Tempo médio de resposta.
- Histórico de desempenho.
- Gráficos de disponibilidade.

### 3.6. Notificações
Canais planejados:
- E-mail.
- Telegram.
- Discord.
- Webhooks.

### 3.7. Relatórios
- Histórico de verificações.
- Relatórios de incidentes.
- Disponibilidade por período.
- Indicadores de SLA.
- Tempo total de indisponibilidade.

## 4. Stack tecnológica

| Componente | Tecnologia | Responsabilidade |
|---|---|---|
| Backend principal | PHP 8.x / Laravel | Regras de negócio e gestão |
| Monitoring Engine | Python 3.x / FastAPI | Monitoramento e processamento |
| Frontend | React 19 / TypeScript | Interface do usuário |
| Banco de dados | PostgreSQL | Persistência |
| Cache | Redis | Cache e controle temporário |
| Mensageria | RabbitMQ | Comunicação assíncrona |
| Containerização | Docker | Ambientes isolados |
| Proxy reverso | Nginx | Roteamento HTTP/HTTPS |
| CI/CD | GitHub Actions | Automação de pipelines |
| Métricas | Prometheus | Coleta de métricas |
| Dashboards | Grafana | Visualização de métricas |
| Tracing | OpenTelemetry | Rastreamento distribuído |

As tecnologias acima representam a stack planejada. A implementação será realizada progressivamente, conforme as etapas do projeto.

## 5. Arquitetura

O PulseWatch será organizado em três componentes principais:

### 5.1. Backend Laravel

Responsável por:
- Autenticação e autorização.
- Gestão de usuários.
- Cadastro de monitores.
- Gerenciamento de configurações.
- Gestão de incidentes.
- Consulta de históricos.
- Exposição das APIs públicas.

### 5.2. Monitoring Engine Python

Responsável por:
- Consumo das mensagens de monitoramento.
- Execução de verificações HTTP.
- Processamento assíncrono.
- Medição de latência.
- Identificação de falhas.
- Registro dos resultados.
- Comunicação com a camada de mensageria.

### 5.3. Frontend React

Responsável por:
- Dashboard.
- Gerenciamento de monitores.
- Visualização de incidentes.
- Histórico de verificações.
- Gráficos e indicadores.
- Configurações da plataforma.

### 5.4. Fluxo de comunicação

1. O usuário cadastra um monitor pela interface.
2. O frontend envia a solicitação ao backend Laravel.
3. O Laravel persiste as configurações.
4. O sistema agenda a verificação.
5. Uma mensagem é publicada no RabbitMQ.
6. O worker Python consome a mensagem.
7. O worker executa a requisição HTTP.
8. O resultado é persistido no PostgreSQL.
9. As regras de monitoramento identificam possíveis incidentes.
10. O dashboard apresenta os indicadores atualizados.

## 6. Infraestrutura e serviços

A infraestrutura será baseada em containers Docker.

Serviços planejados:

- laravel-api: backend principal.
- monitoring-engine: motor de monitoramento.
- monitoring-worker: execução das verificações.
- frontend: aplicação React.
- postgres: banco de dados.
- redis: cache.
- rabbitmq: mensageria.
- nginx: proxy reverso.

O ambiente de desenvolvimento será orquestrado inicialmente pelo Docker Compose.

## 7. Estrutura do repositório

```text
pulsewatch/
├── backend/
│   └── laravel-api/
├── monitoring-engine/
│   ├── app/
│   └── tests/
├── frontend/
│   └── web/
├── infrastructure/
│   ├── docker/
│   ├── nginx/
│   └── monitoring/
├── docs/
│   ├── architecture/
│   ├── decisions/
│   ├── diagrams/
│   ├── api/
│   └── database/
├── scripts/
├── .github/
│   └── workflows/
├── docker-compose.yml
├── .env.example
├── Makefile
├── CONTRIBUTING.md
├── CHANGELOG.md
├── ROADMAP.md
└── README.md
```

## 8. Modelagem de dados

As principais entidades previstas são:

| Entidade | Descrição |
|---|---|
| users | Usuários da plataforma |
| monitors | Serviços monitorados |
| monitor_checks | Resultados das verificações |
| incidents | Incidentes identificados |
| incident_events | Eventos dos incidentes |
| notification_channels | Canais de notificação |
| notification_logs | Histórico de notificações |
| monitor_groups | Agrupamento de monitores |
| monitor_thresholds | Limites e regras de monitoramento |

O PostgreSQL será a fonte oficial dos dados da aplicação.

Redis será utilizado para dados temporários, cache e mecanismos de controle quando necessário.

RabbitMQ será responsável pelo transporte de mensagens entre os componentes.

## 9. Requisitos não funcionais

### Desempenho
- Suportar verificações concorrentes.
- Evitar bloqueios durante o monitoramento.
- Utilizar consultas otimizadas.
- Controlar o consumo de recursos.

### Escalabilidade
- Permitir execução de múltiplos workers.
- Permitir expansão horizontal do motor de monitoramento.
- Utilizar processamento assíncrono.
- Desacoplar os componentes da aplicação.

### Confiabilidade
- Implementar retry controlado.
- Garantir idempotência no processamento.
- Evitar duplicidade de incidentes.
- Registrar falhas de processamento.
- Implementar mecanismos de recuperação.

### Segurança
- Autenticação e autorização.
- Proteção de credenciais.
- Validação de entradas.
- Rate limiting.
- Proteção contra SSRF.
- Restrição de acesso a redes internas.
- Gerenciamento seguro de variáveis de ambiente.

### Manutenibilidade
- Separação de responsabilidades.
- Código organizado por domínio.
- Testes automatizados.
- Documentação técnica.
- Decisões arquiteturais registradas.

## 10. Testes e qualidade

Ferramentas planejadas:

| Ferramenta | Finalidade |
|---|---|
| PHPUnit / Pest | Testes PHP |
| Pytest | Testes Python |
| Vitest | Testes frontend |
| React Testing Library | Testes de componentes |
| Playwright | Testes E2E |
| Testcontainers | Testes de integração |

Os módulos críticos deverão possuir testes unitários e de integração.

A meta inicial de cobertura será de 80% nos módulos críticos, sem tratar cobertura isoladamente como garantia de qualidade.

## 11. Observabilidade

A própria plataforma deverá ser observável.

Serão acompanhados indicadores como:

- Saúde dos serviços.
- Tempo de resposta das APIs.
- Quantidade de verificações executadas.
- Falhas no processamento.
- Tamanho das filas RabbitMQ.
- Estado dos workers.
- Consumo de recursos.
- Erros internos.

Prometheus, Grafana e OpenTelemetry serão utilizados na evolução da infraestrutura de observabilidade.

## 12. Roadmap

### Fase 1: Fundação
- [x] Criar repositório.
- [x] Definir estrutura do monorepositório.
- [ ] Configurar Docker Compose.
- [ ] Configurar PostgreSQL.
- [x] Preparar documentação.

### Fase 2: Backend
- [ ] Criar aplicação Laravel.
- [ ] Implementar autenticação.
- [ ] Criar gestão de monitores.
- [ ] Implementar migrations.
- [ ] Documentar endpoints.

### Fase 3: Monitoring Engine
- [ ] Criar aplicação Python.
- [ ] Implementar HTTP Checker.
- [ ] Implementar processamento assíncrono.
- [ ] Criar worker.
- [ ] Registrar resultados.

### Fase 4: Mensageria
- [ ] Configurar RabbitMQ.
- [ ] Implementar produtores e consumidores.
- [ ] Implementar retry.
- [ ] Implementar idempotência.

### Fase 5: Incidentes
- [ ] Implementar regras de falhas consecutivas.
- [ ] Criar incidentes automaticamente.
- [ ] Implementar reconhecimento.
- [ ] Implementar resolução automática.

### Fase 6: Frontend
- [ ] Criar aplicação React.
- [ ] Desenvolver dashboard.
- [ ] Criar gestão de monitores.
- [ ] Criar gestão de incidentes.
- [ ] Implementar gráficos.

### Fase 7: Notificações e relatórios
- [ ] Implementar notificações por e-mail.
- [ ] Integrar Telegram.
- [ ] Implementar webhooks.
- [ ] Criar relatórios.

### Fase 8: Qualidade e produção
- [ ] Implementar testes automatizados.
- [ ] Configurar CI/CD.
- [ ] Implementar observabilidade.
- [ ] Revisar segurança.
- [ ] Preparar ambiente de produção.

## 13. Princípios de desenvolvimento

O desenvolvimento do PulseWatch seguirá os seguintes princípios:

- Clean Code.
- SOLID.
- Separation of Concerns.
- Domain-Driven Design quando aplicável.
- Dependency Injection.
- Fail Fast.
- Idempotência.
- Test-Driven Development nos módulos críticos.
- API First.
- Infrastructure as Code quando aplicável.

A arquitetura deverá privilegiar simplicidade, clareza e evolução incremental.

## 14. Decisões arquiteturais

As decisões relevantes serão documentadas em `docs/decisions/`, utilizando Architecture Decision Records (ADR).

Cada ADR deverá conter:

- Contexto.
- Problema.
- Alternativas avaliadas.
- Decisão adotada.
- Consequências.
- Status.

As decisões ainda não validadas deverão permanecer identificadas como propostas.

## 15. Licença

A licença do projeto será definida antes da publicação pública do repositório.

## 16. Considerações finais

O PulseWatch nasce como um projeto de engenharia de software com foco em confiabilidade, monitoramento e arquitetura distribuída.

Sua evolução será incremental, priorizando entregas funcionais, decisões técnicas justificadas e qualidade de código.

O objetivo é construir uma plataforma funcional, escalável e preparada para cenários reais de monitoramento de aplicações e serviços.

---

**PulseWatch | Monitor what matters.**
