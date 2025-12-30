<?php
require_once 'config.php';
$config = include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Prompt Gen</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>AI Prompt Generator</h1>
            <a href="admin.php" class="admin-link">Settings</a>
        </header>

        <main>
            <form id="promptForm">
                <div class="form-group">
                    <label>Fact Topic</label>
                    <input type="text" name="topic" placeholder="e.g. The size of the universe" required>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Scenes (1-10)</label>
                        <input type="number" name="scenes" min="1" max="10" value="3" required>
                    </div>
                    <div class="form-group">
                        <label>Visual Style</label>
                        <select name="style">
                            <option value="Cyberpunk">Cyberpunk</option>
                            <option value="Hyper-realistic">Hyper-realistic</option>
                            <option value="Cinematic 3D">Cinematic 3D</option>
                            <option value="Noir">Noir</option>
                            <option value="Minimalist">Minimalist</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Fact Intensity</label>
                        <select name="intensity">
                            <option value="Educational">Educational</option>
                            <option value="Mind-blowing">Mind-blowing</option>
                            <option value="Dark/Eerie">Dark/Eerie</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Audio Tone</label>
                        <select name="tone">
                            <option value="Deep/Authoritative">Deep/Authoritative</option>
                            <option value="Friendly/Informative">Friendly/Informative</option>
                            <option value="Dramatic/Suspenseful">Dramatic/Suspenseful</option>
                        </select>
                    </div>
                </div>

                <div class="toggle-group">
                    <label class="switch">
                        <input type="checkbox" name="continuity" checked>
                        <span class="slider"></span>
                        Continuity Lock
                    </label>
                    <label class="switch">
                        <input type="checkbox" name="subtitles">
                        <span class="slider"></span>
                        Subtitle Mode
                    </label>
                </div>

                <button type="submit" id="generateBtn">Generate Prompts</button>
            </form>

            <div id="loading" class="hidden">
                <div class="spinner"></div>
                <p>Consulting the AI...</p>
            </div>

            <div id="results" class="hidden">
                <div class="actions">
                    <button id="regenerateBtn" class="secondary">Regenerate</button>
                    <button id="copyBtn" class="secondary">Copy All</button>
                </div>
                <div id="outputContent" class="output-box"></div>
            </div>
        </main>
    </div>

    <script>
        const form = document.getElementById('promptForm');
        const generateBtn = document.getElementById('generateBtn');
        const regenerateBtn = document.getElementById('regenerateBtn');
        const results = document.getElementById('results');
        const loading = document.getElementById('loading');
        const output = document.getElementById('outputContent');

        async function triggerGeneration() {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            data.continuity = formData.get('continuity') === 'on';
            data.subtitles = formData.get('subtitles') === 'on';

            loading.classList.remove('hidden');
            results.classList.add('hidden');
            generateBtn.disabled = true;

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                if (result.success) {
                    output.innerText = result.content;
                    results.classList.remove('hidden');
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (e) {
                alert('Connection failed.');
            } finally {
                loading.classList.add('hidden');
                generateBtn.disabled = false;
            }
        }

        form.onsubmit = (e) => { e.preventDefault(); triggerGeneration(); };
        regenerateBtn.onclick = triggerGeneration;
        document.getElementById('copyBtn').onclick = () => {
            const text = output.innerText;
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('Copied to clipboard');
        };
    </script>
</body>
</html>
