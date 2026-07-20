# Política de retenção de dados (rascunho) — PDV

> **Status:** RASCUNHO operacional + jurídico — prazos **sugeridos** para o produto; validar com advogado/contabilidade (obrigações fiscais BR, CDC, trabalhista).  
> **Data:** 2026-07-20  
> Relacionada: [Política de Privacidade](./privacy-policy.md), [Sign-off counsel](./counsel-sign-off.md), ADR-0008, RN-070.

## Princípios

1. Guardar o **mínimo** necessário à finalidade (minimização).
2. Separar **dado cadastral** de **dado fiscal/transacional**.
3. Eliminação ou **anonimização** ao fim do prazo (preferir anonimizar vendas agregadas se analytics precisar continuar).
4. Pedido de exclusão do titular **não** apaga automaticamente obrigações legais (hold).
5. Backups seguem o prazo do dado mais longo neles contido, com rotação.

## Tabela de retenção (proposta MVP)

| Categoria | Exemplos | Prazo proposto | Ação ao expirar | Notas |
|-----------|----------|----------------|-----------------|-------|
| Cadastro de cliente (PII) | CPF, e-mail, telefone, endereço, nascimento | Enquanto houver relacionamento **ou** até **5 anos** após última venda vinculada | Eliminar registro ou anonimizar (manter id sintético se FK de venda exigir) | Alinhar a reclamações CDC / política da rede |
| Nome do cliente | `name` plaintext | Mesmo prazo do cadastro | Idem | Usado em listagem/campanhas |
| Vendas e itens | `sales`, `sale_lines`, pagamentos | **5 anos** a contar do fato gerador | Arquivar cold storage ou anonimizar cliente_id | Conferir obrigação fiscal (NFC-e / livro fiscal) da operação real |
| Turnos de caixa | `cash_shifts` | **5 anos** | Idem vendas | Fechamento / auditoria operacional |
| Estornos / devoluções | `refunds` | **5 anos** (mínimo alinhado à venda) | Idem | RN-019a |
| Auditoria sensível | `audit_logs` | **5 anos** (ou = prazo da entidade auditada) | Append-only; sem UPDATE/DELETE em produção | RN-070; exclusão só via processo controlado fora do app |
| Contas de usuário (staff) | `users`, MFA | Vigência do vínculo + **5 anos** | Desativar → depois eliminar | MFA secret cifrado |
| Sessões / rate limit | Redis, cache | Horas / dias (TTL técnico) | Expiração automática | Não é arquivo permanente |
| Idempotência financeira | `idempotency_records` | **7 dias** (`IDEMPOTENCY_RETENTION_DAYS`) | Purge diário `idempotency:purge` | RN-073; não é PII de titular |
| Outbox / webhook retry | Redis | Até liquidação ou TTL curto (dias) | Purge após confirmed/failed terminal | ADR-0009 |
| Logs de aplicação | `storage/logs` | **90 dias** (default ops) | Rotação / delete | Sem PII em claro |
| Backups MySQL | `backups/*.sql.gz` | Espelhar o maior prazo acima **ou** máx. **5 anos** com prune | Destroy + checksum inventory | Chaves `CUSTOMER_PII_*` / `APP_KEY` **fora** do dump |

## Pedidos de eliminação (titular)

1. Verificar identidade do solicitante.
2. Verificar **holds** (processo, fiscal, antifraude).
3. Se livre: apagar ou anonimizar cadastro; vendas históricas permanecem com referência anonimizada se a lei fiscal exigir conservação.
4. Registrar o atendimento (quem, quando, escopo) — preferencialmente fora da tabela append-only de mutações de preço, ou em canal DPO.

_Implementação de API “direitos do titular” ainda **não** existe no MVP; o canal é manual via encarregado até haver Action dedicada._

## Responsabilidades

| Papel | Responsabilidade |
|-------|------------------|
| Controlador | Definir prazos finais, DPO, responder titulares |
| Engenharia | Criptografia, backups, não logar PII, executar jobs de purge quando existirem |
| Operações | Rotação de backup, `APP_DEBUG=false` em prod |

## Roadmap técnico (não bloqueia o texto legal)

- [ ] Job agendado de anonimização/purge conforme tabela
- [ ] Endpoint/admin “exportar / anonimizar cliente” com auditoria
- [ ] Política publicada na UI (link) e aceite operacional se necessário

## Histórico

| Versão | Data | Nota |
|--------|------|------|
| 0.1-draft | 2026-07-20 | Primeira proposta alinhada ao checklist security §12 |
