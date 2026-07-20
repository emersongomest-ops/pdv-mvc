# Sign-off jurídico LGPD — pacote para counsel (PDV)

> **Status:** Pacote de engenharia pronto. **Parecer / aprovação de advogado** ainda é obrigatório antes do go-live (RN-072).  
> **Data:** 2026-07-20  
> **Não é** aconselhamento legal. Engenharia não preenche CNPJ, DPO nem bases legais finais.

Documentos base:

| Documento | Caminho | Status |
|-----------|---------|--------|
| Política de Privacidade (rascunho) | [`privacy-policy.md`](./privacy-policy.md) | Draft |
| Política de retenção (rascunho) | [`data-retention.md`](./data-retention.md) | Draft |
| Controles técnicos PII | [`../adr/0008-customer-pii-encryption-lgpd.md`](../adr/0008-customer-pii-encryption-lgpd.md) | Aceito (engenharia) |
| Segurança / checklist | [`../security.md`](../security.md) §12 | Gate aberto até este sign-off |

---

## 1. Objetivo do counsel

1. Validar (ou reescrever) textos de privacidade e retenção para o **controlador real**.
2. Confirmar bases legais por categoria de dado (§2 da política).
3. Definir prazos finais de retenção vs obrigações fiscais/CDC/trabalhistas.
4. Preencher identidade do controlador, encarregado (DPO) e canal do titular.
5. Assinar (e-mail / parecer) liberando publicação e go-live multi-loja.

---

## 2. Campos obrigatórios (preencher — não commitar segredos)

Copiar para a política publicada / registro interno. Valores **não** devem ser inventados pela engenharia.

| Campo | Onde atualizar | Valor |
|-------|----------------|-------|
| Razão social / CNPJ | `privacy-policy.md` cabeçalho + §10 | `<PREENCHER>` |
| Endereço do controlador | `privacy-policy.md` §10 | `<PREENCHER>` |
| Encarregado (DPO) — nome | `privacy-policy.md` | `<PREENCHER>` |
| Encarregado — e-mail / canal | `privacy-policy.md` | `<PREENCHER>` |
| Contato titular (se distinto) | `privacy-policy.md` | `<PREENCHER>` |
| País / região da infra (transferência) | `privacy-policy.md` §8 | `<PREENCHER>` |
| Operadores / suboperadores (hosting, backup, adquirente) | Anexo contrato / §4 | `<PREENCHER>` |

Após preenchimento em produção, preferir **site / app** como canônico; manter `docs/legal/` versionado como espelho técnico.

---

## 3. Perguntas para o counsel (checklist)

### Bases e finalidades

- [ ] Bases legais da tabela de clientes (CPF, contato, campanhas RN-083/084) estão corretas para o modelo de negócio?
- [ ] Tratamento de histórico de compras + analytics exige consentimento separado ou basta legítimo interesse / contrato?
- [ ] Logs com IP (rate limit) e cookies de sessão: enquadramento adequado no texto?

### Retenção

- [ ] Prazo proposto de **5 anos** para vendas/turnos/auditoria é compatível com a operação (NFC-e / obrigações locais)?
- [ ] Cadastro PII “5 anos após última venda” vs “enquanto houver relacionamento” — qual regra final?
- [ ] Pedido de exclusão do titular vs hold fiscal/processual — procedimento OK (§ eliminação em `data-retention.md`)?
- [ ] Backups com PII cifrada: prazo e destroy alinhados?

### Direitos do titular

- [ ] Canal manual via DPO é aceitável no MVP (sem API de export/erase ainda)?
- [ ] Prazo de resposta (15 dias) e registro de atendimento — processo interno definido?
- [ ] Portabilidade: escopo mínimo (JSON/CSV de cadastro + histórico) a oferecer?

### Terceiros e internacionais

- [ ] Cláusulas com hosting / adquirente / Cloudflare (Turnstile) / HIBP cobrem o papel de operador?
- [ ] Transferência internacional: mecanismo (SCC / país adequado) se infra fora do BR?

### Publicação

- [ ] Versão aprovada pode ser publicada na UI / site?
- [ ] Aceite operacional (staff) necessário além da política de clientes?

---

## 4. O que a engenharia já garante (contexto técnico)

| Tema | Evidência |
|------|-----------|
| PII de cliente cifrada em repouso + blind index | ADR-0008, `PiiCrypto` |
| Sem PAN em persistência | ADR-0009 |
| MFA gestores, auditoria sensível | ADR-0010, RN-070 |
| Drafts de privacidade/retenção | este diretório |
| API de direitos do titular | **Não** no MVP — canal DPO manual (`data-retention.md`) |
| Job de purge/anonimização | Roadmap em `data-retention.md` (não bloqueia texto) |

Counsel **não** precisa revalidar código linha a linha; pode pedir evidência pontual (testes Feature Customers / Security).

---

## 5. Formato do parecer (mínimo)

| Campo | Conteúdo |
|-------|----------|
| Data | |
| Advogado / escritório | |
| Documentos revisados | privacy + retention (+ anexos) |
| Versão aprovada | ex. `privacy-policy.md` @ commit / tag |
| Ressalvas | lista |
| Liberação go-live | Sim / Não / Condicional |
| Assinatura / e-mail | |

Armazenar o parecer **fora** do git público se contiver dados do escritório; opcionalmente referenciar ticket interno em `docs/legal/` sem anexar PII.

---

## 6. Fechamento do item §12 em `docs/security.md`

Marcar **Legal sign-off** completo somente quando:

1. [ ] Campos da §2 deste arquivo preenchidos no texto publicado  
2. [ ] Parecer com liberação go-live (ou condicional com tickets abertos aceitos pelo owner)  
3. [ ] Status dos drafts atualizado de RASCUNHO → **Aprovado counsel** (data + referência)  
4. [ ] RN-072 considerado atendido para produção (owner + counsel)

Este pacote sozinho fecha apenas a linha de **preparação** jurídica no checklist.

---

## 7. Histórico

| Data | Nota |
|------|------|
| 2026-07-20 | Pacote de sign-off criado (paralelo ao brief de pen-test) |
