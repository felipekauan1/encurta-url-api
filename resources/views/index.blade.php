<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Encurta URL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: sans-serif;
            background: #111110;
            color: #e8e8e4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            background: #1c1c1a;
            border: 0.5px solid #2e2e2b;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 560px;
        }

        h1 {
            font-size: 22px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 1.5rem;
            color: #e8e8e4;
        }

        .input-row {
            display: flex;
            gap: 8px;
        }

        input[type="url"] {
            flex: 1;
            padding: 8px 12px;
            border: 0.5px solid #3a3a36;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            background: #111110;
            color: #e8e8e4;
        }

        input[type="url"]::placeholder { color: #555552; }
        input[type="url"]:focus { border-color: #378ADD; }

        .btn-primary {
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            background: #185FA5;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary:hover { background: #1a6fbe; }
        .btn-primary:active { transform: scale(0.98); }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

        .btn-ghost {
            padding: 4px 10px;
            border: 0.5px solid #3a3a36;
            border-radius: 8px;
            background: transparent;
            color: #aaa;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .btn-ghost:hover { background: #2a2a27; }

        .error {
            margin-top: 1rem;
            font-size: 14px;
            color: #E24B4A;
            display: none;
        }

        .result {
            margin-top: 1.5rem;
            padding: 1rem 1.25rem;
            background: #151513;
            border: 0.5px solid #2e2e2b;
            border-radius: 8px;
            display: none;
        }

        .result-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .result-link {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .result-link a {
            font-size: 15px;
            font-weight: 500;
            color: #378ADD;
            text-decoration: none;
            word-break: break-all;
        }

        .result-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Cole a URL para ser encurtada</h1>

        <div class="input-row">
            <input type="url" id="url-input" placeholder="https://exemplo.com/link-muito-longo" />
            <button class="btn-primary" id="btn-encurtar" onclick="encurtar()">
                <i class="ti ti-scissors"></i> Encurtar
            </button>
        </div>

        <p class="error" id="error-msg"></p>

        <div class="result" id="result">
            <p class="result-label">Link encurtado</p>
            <div class="result-link">
                <a id="short-link" href="#" target="_blank"></a>
                <button class="btn-ghost" onclick="copiar()">
                    <i class="ti ti-copy"></i> Copiar
                </button>
            </div>
        </div>
    </div>

    <script>
        async function encurtar() {
            const input = document.getElementById('url-input');
            const btn = document.getElementById('btn-encurtar');
            const error = document.getElementById('error-msg');
            const result = document.getElementById('result');

            const url = input.value.trim();
            if (!url) {
                mostrarErro('Por favor, cole uma URL antes de encurtar.');
                return;
            }

            error.style.display = 'none';
            btn.disabled = true;
            btn.textContent = 'Encurtando...';

            try {
                const res = await fetch('/api/links', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ original_url: url })
                });

                const data = await res.json();

                if (!res.ok) {
                    mostrarErro(data.message || 'Erro ao encurtar o link.');
                    return;
                }

                const shortUrl = window.location.origin + '/' + data.link.short_code;
                const linkEl = document.getElementById('short-link');
                linkEl.href = shortUrl;
                linkEl.textContent = shortUrl;
                result.style.display = 'block';
                input.value = '';

            } catch (e) {
                mostrarErro('Não foi possível conectar à API. Verifique se o servidor está rodando.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-scissors"></i> Encurtar';
            }
        }

        function mostrarErro(msg) {
            const el = document.getElementById('error-msg');
            el.textContent = msg;
            el.style.display = 'block';
        }

        function copiar() {
            const link = document.getElementById('short-link').textContent;
            navigator.clipboard.writeText(link).then(() => {
                const btn = document.querySelector('.btn-ghost');
                btn.innerHTML = '<i class="ti ti-check"></i> Copiado!';
                setTimeout(() => {
                    btn.innerHTML = '<i class="ti ti-copy"></i> Copiar';
                }, 2000);
            });
        }

        document.getElementById('url-input').addEventListener('keydown', e => {
            if (e.key === 'Enter') encurtar();
        });
    </script>
</body>
</html>