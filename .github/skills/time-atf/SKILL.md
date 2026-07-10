---
name: time-atf
description: >
  Time ATF - Análise Técnica e Funcional (pipeline multi-agente de compatibilidade backend ↔ frontend).
  Use quando: analisar compatibilidade entre backend e frontend; revisar contratos de API; verificar mensagens de erro e status HTTP; avaliar impacto em regras de negócio; checar retornos esperados e tratamento visual de respostas.
argument-hint: "[descrição da tarefa, ticket ou rota a analisar]"
user-invocable: true
---

# Time ATF — Análise Técnica e Funcional

Você vai conduzir uma análise técnica e funcional sobre o contexto abaixo, acionando agentes especializados em sequência. O foco é a **compatibilidade entre backend e frontend**: contratos de API, mensagens de erro, status HTTP, retornos esperados, tratamento visual/funcional das respostas e impacto nas regras de negócio. O resultado final é um parecer consolidado com veredito, tabela de impacto e plano de melhoria.

**Contexto a analisar:** $ARGUMENTS

## Regras gerais

- Se o Agente 3 identificar dúvida, inconsistência ou análise rasa, o fluxo **retorna ao responsável** (Agente 1 ou 2) antes de consolidar.
- Toda conclusão deve apontar evidência concreta (arquivo, rota, campo, trecho), não suposição.
- Separe sempre mensagem técnica (debug) de mensagem para o usuário final.
- Não proponha alteração sem avaliar fallback e risco de quebra em telas/integrações que usam a mesma rota.

---

## Fase 1 — Análise paralela

Acione os dois agentes em paralelo.

### 🔌 Agente 1 — Analista Backend/API

- Avalia retornos, contratos, payloads, status HTTP e mensagens de erro.
- Verifica se sucesso, erro de validação, erro de regra de negócio e erro inesperado são previsíveis e distinguíveis.
- Aponta campos ausentes, inconsistentes ou com nomes divergentes, e risco de quebra em integrações.
- **Saída:** problemas encontrados · incompatibilidades técnicas · riscos · sugestões · pontos a validar pelo Agente 3.

### 🖥️ Agente 2 — Analista Frontend/UX Funcional

- Avalia como o frontend consome, interpreta e exibe os retornos.
- Verifica campos esperados vs. retornados, exibição de erros, vazamento de erro técnico na tela, fallback ausente e fluxo após erro.
- Aponta onde o usuário fica sem mensagem clara, com tela travada ou ação incompleta.
- **Saída:** problemas encontrados · incompatibilidades com o backend · riscos para o usuário · sugestões · pontos a validar pelo Agente 3.

---

## Fase 2 — Confronto técnico

### ⚖️ Agente 3 — Revisor Sênior de Conflitos

- Confronta os pareceres dos Agentes 1 e 2: onde concordam, onde divergem, o que cada um ignorou (técnico vs. usuário).
- Verifica se a solução resolve o problema ponta a ponta e se é segura, arriscada ou exige revisão.
- **Regra de retorno:** havendo dúvida ou análise insuficiente, devolve ao agente responsável com motivo, pergunta objetiva e a informação necessária para fechar o parecer.
- **Saída:** pontos de concordância · pontos de divergência · como foram resolvidos · pontos aprofundados · riscos restantes · recomendação preliminar.

---

## Fase 3 — Consolidação final

### 🏛️ Agente 4 — Consolidador / Arquiteto de Solução

- Não apenas resume: decide e propõe um plano de melhoria seguro com base nos três pareceres.
- Produz a **saída final obrigatória** abaixo.

**Veredito** (escolher um):

- ✅ seguro — sem risco relevante de quebra; melhora a compatibilidade.
- ⚠️ revisar — parece correto mas depende de validação, ajuste de contrato ou alinhamento.
- ❌ risco de quebra — pode quebrar fluxos, contratos, telas, validações ou integrações.

**Pontos de divergência entre os agentes:**

| Divergência | Agente 1 defendeu | Agente 2 defendeu | Decisão final | Justificativa |
| ----------- | ----------------- | ----------------- | ------------- | ------------- |

**Tabela de impacto:**

| Alteração | Como era antes | O que afeta | Impacto na funcionalidade |
| --------- | -------------- | ----------- | ------------------------- |

**Recomendações priorizadas** (práticas e aplicáveis) e **próximos passos**.

---

## Padrão recomendado de retorno de erro

Sempre que propuser ajuste no backend, mire neste contrato previsível:

```json
{
  "success": false,
  "code": "BUSINESS_RULE_ERROR",
  "message": "Não foi possível concluir a operação.",
  "userMessage": "Este título não pode ser recebido porque já está quitado.",
  "details": { "field": "idtitulo", "reason": "Título já quitado" }
}
```

`success` resultado · `code` código interno · `message` técnica · `userMessage` segura p/ exibir · `details` debug/validação.

---

## Fluxo de Execução

```
[Contexto / rota]
   → Fase 1: Agente 1 (Backend/API) ‖ Agente 2 (Frontend/UX)   ← em paralelo
      → Fase 2: Agente 3 (Confronto)  ──↺ retorna a 1 ou 2 se faltar dado
         → Fase 3: Agente 4 (Consolidação)
            → [Veredito + Tabela de Impacto + Recomendações]
```
