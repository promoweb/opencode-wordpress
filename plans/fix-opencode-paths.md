# Piano di Correzione: Percorsi OpenCode WordPress

## Problema

Il file [`.opencode/opencode.json`](.opencode/opencode.json) contiene percorsi con il prefisso `.opencode/` che causano un errore dopo l'installazione:

```
Configuration is invalid: bad file reference: "{file:.opencode/prompts/agents/wordpress-reviewer.txt}"
/home/user/.opencode/.opencode/prompts/agents/wordpress-reviewer.txt does not exist
```

### Analisi del Problema

Lo script [`install.sh`](install.sh) copia i file in questo modo:
- `.opencode/prompts/` → `~/.opencode/prompts/`
- `.opencode/commands/` → `~/.opencode/commands/`

Ma il file `opencode.json` mantiene i percorsi originali:
- `{file:.opencode/prompts/agents/wordpress-reviewer.txt}`

Questo crea un doppio percorso errato:
- `~/.opencode/.opencode/prompts/agents/wordpress-reviewer.txt` (inesistente)

### Percorsi da Correggere

Sono stati identificati **9 percorsi** errati nel file `opencode.json`:

| Riga | Tipo | Percorso Errato | Percorso Corretto |
|------|------|-----------------|-------------------|
| 22 | agent prompt | `{file:.opencode/prompts/agents/wordpress-reviewer.txt}` | `{file:prompts/agents/wordpress-reviewer.txt}` |
| 34 | agent prompt | `{file:.opencode/prompts/agents/wordpress-build-resolver.txt}` | `{file:prompts/agents/wordpress-build-resolver.txt}` |
| 46 | agent prompt | `{file:.opencode/prompts/agents/theme-reviewer.txt}` | `{file:prompts/agents/theme-reviewer.txt}` |
| 58 | agent prompt | `{file:.opencode/prompts/agents/plugin-reviewer.txt}` | `{file:prompts/agents/plugin-reviewer.txt}` |
| 70 | command template | `{file:.opencode/commands/wp-theme.md}` | `{file:commands/wp-theme.md}` |
| 76 | command template | `{file:.opencode/commands/wp-plugin.md}` | `{file:commands/wp-plugin.md}` |
| 82 | command template | `{file:.opencode/commands/wp-review.md}` | `{file:commands/wp-review.md}` |
| 88 | command template | `{file:.opencode/commands/wp-build-fix.md}` | `{file:commands/wp-build-fix.md}` |
| 94 | command template | `{file:.opencode/commands/wc-build.md}` | `{file:commands/wc-build.md}` |

---

## Soluzione

### Opzione A: Correzione nel File Sorgente (Raccomandata)

Correggere direttamente il file `.opencode/opencode.json` nel repository. Questo è l'approccio più pulito perché:
- Il file nel repository riflette la struttura dopo l'installazione
- Non richiede modifiche allo script di installazione
- È più manutenibile nel tempo

### Opzione B: Correzione Dinamica nello Script

Modificare lo script `install.sh` per convertire automaticamente i percorsi durante l'installazione usando `sed`.

---

## Piano di Implementazione

### Fase 1: Correzione del File opencode.json

Modificare il file [`.opencode/opencode.json`](.opencode/opencode.json) sostituendo tutte le occorrenze di `{file:.opencode/` con `{file:`.

**Modifiche richieste:**

```diff
- "prompt": "{file:.opencode/prompts/agents/wordpress-reviewer.txt}"
+ "prompt": "{file:prompts/agents/wordpress-reviewer.txt}"

- "prompt": "{file:.opencode/prompts/agents/wordpress-build-resolver.txt}"
+ "prompt": "{file:prompts/agents/wordpress-build-resolver.txt}"

- "prompt": "{file:.opencode/prompts/agents/theme-reviewer.txt}"
+ "prompt": "{file:prompts/agents/theme-reviewer.txt}"

- "prompt": "{file:.opencode/prompts/agents/plugin-reviewer.txt}"
+ "prompt": "{file:prompts/agents/plugin-reviewer.txt}"

- "template": "{file:.opencode/commands/wp-theme.md}\n\n$ARGUMENTS"
+ "template": "{file:commands/wp-theme.md}\n\n$ARGUMENTS"

- "template": "{file:.opencode/commands/wp-plugin.md}\n\n$ARGUMENTS"
+ "template": "{file:commands/wp-plugin.md}\n\n$ARGUMENTS"

- "template": "{file:.opencode/commands/wp-review.md}\n\n$ARGUMENTS"
+ "template": "{file:commands/wp-review.md}\n\n$ARGUMENTS"

- "template": "{file:.opencode/commands/wp-build-fix.md}\n\n$ARGUMENTS"
+ "template": "{file:commands/wp-build-fix.md}\n\n$ARGUMENTS"

- "template": "{file:.opencode/commands/wc-build.md}\n\n$ARGUMENTS"
+ "template": "{file:commands/wc-build.md}\n\n$ARGUMENTS"
```

### Fase 2: Verifica dello Script install.sh

Lo script [`install.sh`](install.sh) attuale copia correttamente i file, ma è bene aggiungere una verifica:

1. Verificare che la directory `prompts/agents/` venga creata
2. Verificare che la directory `commands/` venga creata
3. Aggiungere un passaggio di validazione post-installazione

### Fase 3: Aggiornamento Documentazione

Aggiornare [`docs/INSTALLATION.md`](docs/INSTALLATION.md) con:
- Nota sulla struttura dei percorsi
- Istruzioni per verificare l'installazione

---

## Struttura Corretta Post-Installazione

```
~/.opencode/
├── opencode.json              # File di configurazione con percorsi corretti
├── commands/
│   ├── wp-theme.md
│   ├── wp-plugin.md
│   ├── wp-review.md
│   ├── wp-build-fix.md
│   └── wc-build.md
├── instructions/
│   └── INSTRUCTIONS.md
├── prompts/
│   └── agents/
│       ├── wordpress-reviewer.txt
│       ├── wordpress-build-resolver.txt
│       ├── theme-reviewer.txt
│       └── plugin-reviewer.txt
├── skills/
│   ├── wordpress-theme-development/
│   ├── wordpress-plugin-development/
│   ├── woocommerce-patterns/
│   ├── wordpress-security/
│   ├── wordpress-rest-api/
│   ├── wordpress-testing/
│   ├── wordpress-hooks-filters/
│   └── wordpress-database/
├── agents/
│   ├── wordpress-reviewer.md
│   ├── wordpress-build-resolver.md
│   ├── theme-reviewer.md
│   └── plugin-reviewer.md
├── rules/
│   ├── common/
│   └── wordpress/
└── hooks/
    ├── php-lint.js
    ├── wp-debug-check.js
    └── security-check.js
```

---

## Comandi per l'Utente (Fix Immediato)

Per correggere immediatamente un'installazione esistente:

```bash
# Correggi i percorsi nel file opencode.json
sed -i 's|{file:\.opencode/|{file:|g' ~/.opencode/opencode.json

# Verifica che i file esistano
ls -la ~/.opencode/prompts/agents/
ls -la ~/.opencode/commands/

# Testa la configurazione
opencode config validate
```

---

## Checklist di Verifica

- [ ] File `.opencode/opencode.json` corretto con percorsi senza prefisso
- [ ] Script `install.sh` testato con nuova configurazione
- [ ] Verificato che `opencode config validate` non restituisca errori
- [ ] Testato un comando (es. `opencode /wp-theme "test"`)
- [ ] Documentazione aggiornata se necessario