# Política de Privacidade (rascunho LGPD) — PDV

> **Status:** RASCUNHO para revisão jurídica — **não** constitui aconselhamento legal.  
> **Idioma do produto:** inglês no código; este texto é em português (titulares BR).  
> **Data do rascunho:** 2026-07-20  
> Controles técnicos: [ADR-0008](../adr/0008-customer-pii-encryption-lgpd.md), [`docs/security.md`](../security.md).

Preencher antes de publicar:

| Campo | Valor |
|-------|--------|
| Controlador | _[Razão social / CNPJ]_ |
| Encarregado (DPO) | _[nome / e-mail]_ |
| Contato titular | _[e-mail canal LGPD]_ |

---

## 1. Quem somos

O **PDV** é um sistema de ponto de venda multi-loja (API + interface) usado por operadores de caixa e gestores. Esta política descreve o tratamento de dados pessoais no âmbito desse sistema.

## 2. Dados que tratamos

### 2.1 Clientes (cadastro e vendas)

| Dado | Finalidade principal | Base legal típica (a confirmar) |
|------|----------------------|--------------------------------|
| Nome | Identificação, listagens, campanhas | Execução de contrato / legítimo interesse (cadastro comercial) |
| CPF | Identificação única no POS, antifraude operacional | Obrigação legal / execução de contrato (conforme caso) |
| E-mail, telefone | Contato, cupons, comunicações comerciais autorizadas | Consentimento e/ou legítimo interesse |
| Data de nascimento | Campanhas de aniversário (RN-083) | Consentimento / legítimo interesse |
| Endereço | Campanhas regionais (RN-084), entrega se aplicável | Execução de contrato / legítimo interesse |
| Histórico de compras (venda, loja, valores) | Operação, trocas/devoluções, analytics (RN-080+) | Execução de contrato / legítimo interesse |

**Não** armazenamos número completo de cartão (PAN). Dados de cartão, quando digitados, são efêmeros em memória para validação e cobrança; ver ADR-0009.

### 2.2 Usuários do sistema (operadores / gestores)

| Dado | Finalidade |
|------|------------|
| Nome, e-mail, senha (hash), papel, lojas | Autenticação e autorização |
| Segredo MFA (cifrado) | Autenticação em dois fatores (gestores) |
| Trilhas de auditoria (ações sensíveis) | Segurança, accountability (RN-070) |

### 2.3 Dados técnicos

Logs de aplicação com identificador de correlação; IPs em rate limiting de login; cookies de sessão (Sanctum). Sem cookies de publicidade de terceiros no MVP.

## 3. Como protegemos

- Criptografia em repouso dos campos sensíveis de cliente (AES-256-CBC, chave dedicada).
- Índices cegos (HMAC) para busca por igualdade de CPF/e-mail.
- CPF mascarado na interface operacional; PII completa só para gestores autorizados.
- Sessão com cookies HttpOnly; MFA TOTP para gestores.
- Backups tratados como confidenciais; chaves fora do dump ([ops/backup-restore](../ops/backup-restore.md)).

## 4. Compartilhamento

Dados podem ser processados por:

- Infraestrutura de hospedagem (banco, cache, backups) sob contrato;
- Adquirente de pagamento (SOAP/webhook) — apenas o necessário à cobrança/estorno, **sem** retenção de PAN pelo PDV;
- Autoridades, quando houver obrigação legal.

Não vendemos cadastros de clientes.

## 5. Direitos do titular (LGPD arts. 18+)

Mediante canal do encarregado, o titular pode solicitar:

- Confirmação de tratamento e acesso;
- Correção de dados incompletos/inexatos;
- Anonimização, bloqueio ou eliminação de dados desnecessários;
- Portabilidade (quando aplicável);
- Informação sobre compartilhamentos;
- Revogação de consentimento (quando a base for consentimento).

**Limites:** retenção exigida por lei fiscal/consumidor, segurança, exercício regular de direitos em processo, ou integridade de trilhas de auditoria — ver [política de retenção](./data-retention.md).

Pedidos são registrados e respondidos no prazo legal (em regra até 15 dias, salvo complexidade).

## 6. Retenção

Prazos resumidos na [Política de retenção de dados](./data-retention.md). Após o prazo, dados são eliminados ou anonimizados, salvo obrigação legal em contrário.

## 7. Crianças e adolescentes

O cadastro de cliente no POS pressupõe relação comercial com titular capaz ou responsável legal. Não há fluxo dedicado a menores no MVP; se identificado uso inadequado, o controlador deve avaliar bloqueio/exclusão.

## 8. Transferência internacional

Se a hospedagem ou backups estiverem fora do Brasil, o controlador adotará mecanismo adequado (cláusulas contratuais / país adequado). _[Preencher localização real da infra.]_

## 9. Alterações

Alterações relevantes serão versionadas neste repositório (`docs/legal/`) e, em produção, comunicadas pelo canal usual aos operadores do sistema e, quando exigido, aos titulares.

## 10. Contato

Encarregado / canal LGPD: _[e-mail]_  
Controlador: _[razão social, CNPJ, endereço]_
