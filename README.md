# Tehokas Desafio Técnico - Sistema de Checklist Inteligente

Este é o MVP do sistema de gestão de projetos e tarefas desenvolvido para o Desafio de Estágio da Tehokas. O sistema permite criar projetos e gerenciar tarefas em um quadro Kanban, com um indicador automático de saúde do projeto.

## 🚀 Tecnologias Utilizadas

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue.js 3 com Inertia.js
- **Estilização**: Tailwind CSS
- **Banco de Dados**: SQLite (Configuração padrão para facilidade de teste)

## 🛠️ Instalação e Execução

Siga os passos abaixo para rodar o projeto localmente:

1. **Clonar o Repositório**
   ```bash
   git clone <url-do-repositorio>
   cd TehokasDesafio
   ```

2. **Instalar Dependências Backend**
   ```bash
   composer install
   ```

3. **Instalar Dependências Frontend**
   ```bash
   npm install
   ```

4. **Configurar Ambiente**
   
   O projeto vem configurado para usar SQLite por padrão.
   ```bash
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Rodar a Aplicação**
   
   Você precisará de dois terminais:
   
   **Terminal 1 (Servidor Laravel):**
   ```bash
   php artisan serve
   ```
   
   **Terminal 2 (Compilador Vite):**
   ```bash
   npm run dev
   ```

6. **Acessar**
   Abra o navegador em `http://localhost:8000`. Registre-se ou faça login para começar.

## 💡 Diferenciais e Soluções

### Lógica do Indicador de Saúde
O "Indicador de Saúde" foi implementado diretamente no Model `Project` utilizando um Accessor (`getIsOnAlertAttribute`).
- Ele calcula a proporção de tarefas atrasadas (`status != completed` e `deadline < now`) sobre o total de tarefas.
- Se essa proporção for **maior que 20%**, o atributo `is_on_alert` retorna `true`, exibindo o badge "Em Alerta" no Frontend.

### Frontend com Inertia
A escolha do Inertia.js permitiu construir uma Single Page Application (SPA) robusta utilizando o roteamento e controllers clássicos do Laravel, eliminando a complexidade de gerenciar uma API REST separada para este MVP.

### Maior Dificuldade
A maior dificuldade técnica encontrada foi garantir a compatibilidade entre as versões mais recentes do Vite (v7) e os plugins do ecossistema Vue, o que foi resolvido ajustando as dependências do `package.json` para garantir estabilidade.

