@echo off
REM Script para publicar o repositório Clicksign no GitHub (Windows)

echo 🚀 Publicando repositório Clicksign...

REM 1. Inicializar repositório Git
echo 📁 Inicializando repositório Git...
git init

REM 2. Adicionar repositório remoto
echo 🔗 Adicionando repositório remoto...
git remote add origin https://github.com/Ferreiramg/clicksign.git

REM 3. Adicionar todos os arquivos
echo 📄 Adicionando arquivos...
git add .

REM 4. Fazer commit inicial
echo 💾 Fazendo commit inicial...
git commit -m "Initial commit: Clicksign Laravel 12 SDK - SDK completo para integração com Clicksign - Testes automatizados com Pest (73.5%% cobertura) - GitHub Actions para CI/CD - Suporte para Laravel 12+ e PHP 8.2+ - Documentação completa"

REM 5. Definir branch principal
echo 🌿 Configurando branch principal...
git branch -M main

REM 6. Fazer push inicial
echo ⬆️ Fazendo push inicial...
git push -u origin main

echo ✅ Repositório publicado com sucesso!
echo.
echo 📦 Próximos passos para publicar no Packagist:
echo 1. Acesse https://packagist.org/
echo 2. Faça login com GitHub
echo 3. Clique em 'Submit'
echo 4. Cole a URL: https://github.com/Ferreiramg/clicksign
echo 5. Clique em 'Check' e depois 'Submit'
echo.
echo 🎉 Seu pacote estará disponível via: composer require ferreiramg/clicksign

pause
