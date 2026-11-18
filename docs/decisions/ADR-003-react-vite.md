# ADR-003: React 19 + Vite + TypeScript

**Status:** Accepted  
**Data:** 17/11/2026

## Contexto

O PulseWatch precisa de uma interface moderna, responsiva e independente do backend, permitindo visualizar métricas, gerenciar monitores e acompanhar incidentes.

## Decisão

Utilizar React 19, Vite e TypeScript para construir uma aplicação Single Page Application (SPA).

A aplicação consumirá exclusivamente as APIs REST do Laravel.

## Tecnologias

- React 19.
- TypeScript.
- Vite.
- React Router.
- Biblioteca de gerenciamento de estado a definir.
- Biblioteca de componentes a definir.
- Vitest.
- React Testing Library.

## Justificativa

- Separação entre frontend e backend.
- Desenvolvimento independente.
- Build otimizado pelo Vite.
- Tipagem estática.
- Ecossistema maduro.
- Facilidade de containerização.
- Possibilidade de evolução da interface sem alterar o backend.

## Consequências

**Positivas:**
- Frontend independente.
- Build rápido.
- Organização modular.
- Facilidade de testes.
- Deploy independente.

**Atenções:**
- Configuração de autenticação.
- Estratégia de gerenciamento de estado.
- Tratamento de erros HTTP.
- Controle de permissões.
- Configuração de CORS.
- Gerenciamento de sessões.

## Alternativas consideradas

- Next.js.
- Vue.js.
- Aplicação frontend renderizada pelo Laravel.

## Resultado

O frontend será desenvolvido como SPA utilizando React 19, Vite e TypeScript.

Os arquivos compilados serão disponibilizados pelo Nginx.