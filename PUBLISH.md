# Comandos para publicar o repositório

## 1. Inicializar o repositório Git (se ainda não foi feito)
```bash
git init
```

## 2. Adicionar o repositório remoto
```bash
git remote add origin https://github.com/Ferreiramg/clicksign.git
```

## 3. Adicionar todos os arquivos
```bash
git add .
```

## 4. Fazer o primeiro commit
```bash
git commit -m "Initial commit: Clicksign Laravel SDK"
```

## 5. Definir a branch principal como main
```bash
git branch -M main
```

## 6. Fazer o push inicial
```bash
git push -u origin main
```

## 7. Para commits futuros
```bash
# Adicionar mudanças
git add .

# Fazer commit
git commit -m "Descrição das mudanças"

# Fazer push
git push
```

## 8. Para publicar no Packagist
1. Acesse https://packagist.org/
2. Faça login com sua conta GitHub
3. Clique em "Submit"
4. Cole a URL do seu repositório: https://github.com/Ferreiramg/clicksign
5. Clique em "Check"
6. Se tudo estiver OK, clique em "Submit"

## 9. Para configurar auto-update no Packagist
1. No seu repositório GitHub, vá em Settings > Webhooks
2. Clique em "Add webhook"
3. Cole a URL: https://packagist.org/api/github?username=Ferreiramg
4. Selecione "Just the push event"
5. Clique em "Add webhook"

Agora o Packagist será atualizado automaticamente a cada push!
