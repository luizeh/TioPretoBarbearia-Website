# Sistema CRUD para Barbearia – Documento de Visão Geral do Projeto

## Visão Geral

O projeto consiste no desenvolvimento de um sistema web completo para gerenciamento de uma barbearia. O objetivo é facilitar tanto a experiência dos clientes quanto a administração do estabelecimento, centralizando agendamentos, vendas de produtos, gerenciamento de serviços, horários de funcionamento e comunicação com os clientes.

O sistema possuirá dois tipos de usuários:

* Cliente
* Administrador

Cada perfil terá permissões específicas, garantindo segurança e organização das funcionalidades.

---

# Fluxo Geral do Sistema

## Primeiro acesso

Ao acessar o endereço da aplicação, o visitante visualizará a página inicial da barbearia.

A página apresentará:

* Nome da barbearia
* Banner principal
* Fotos do estabelecimento
* Pequena apresentação
* Horário de funcionamento
* Botões para Login e Cadastro

Caso o usuário ainda não possua uma conta, poderá realizar seu cadastro.

---

# Cadastro

Para criar uma conta serão solicitados os seguintes dados:

* Nome
* Sobrenome
* Número de telefone
* E-mail
* Senha
* Cidade onde mora

Após concluir o cadastro:

* os dados serão validados;
* a senha será criptografada antes de ser armazenada;
* o usuário receberá automaticamente a permissão de Cliente;
* uma sessão será iniciada e o usuário poderá utilizar o sistema.

---

# Login

O login será realizado utilizando:

* E-mail
* Senha

Após autenticação:

Se for Cliente:

* será direcionado para a área do cliente.

Se for Administrador:

* será direcionado para o Dashboard Administrativo.

Todo o controle de permissões será realizado através da sessão do usuário.

---

# Área do Cliente

Após entrar no sistema, o cliente terá acesso ao menu principal.

As funcionalidades disponíveis serão:

* Página Inicial
* Agenda
* Produtos
* Carrinho
* Meus Agendamentos
* Meus Pedidos
* Meu Perfil
* Logout

---

# Perfil do Cliente

O cliente poderá visualizar e editar:

* Nome
* Sobrenome
* Cidade
* Telefone
* Senha

O e-mail continuará sendo utilizado como identificador da conta.

---

# Sistema de Serviços

Todos os serviços serão cadastrados exclusivamente pelo administrador.

Cada serviço possuirá:

* Nome
* Foto
* Descrição
* Preço
* Tempo estimado em minutos

Exemplos:

* Corte Masculino
* Barba
* Corte + Barba
* Hidratação
* Pigmentação

Esses serviços ficarão disponíveis para seleção durante o agendamento.

---

# Sistema de Agenda

Esta será a principal funcionalidade do sistema.

O cliente poderá navegar por um calendário e visualizar os dias disponíveis.

Ao selecionar uma data, o sistema consultará:

* horário de funcionamento da barbearia;
* agendamentos existentes;
* duração dos serviços.

A partir dessas informações serão exibidos apenas horários disponíveis.

Exemplo:

Horário da barbearia:

08:00 às 18:00

Serviço escolhido:

Corte + Barba

Tempo:

60 minutos

Horários disponíveis:

* 08:00
* 09:00
* 10:00
* 11:00

Caso outro cliente reserve às 09:00, o sistema bloqueará automaticamente o período correspondente.

Se a barbearia estiver fechada em determinado dia, toda a agenda exibirá "Fechado".

---

# Meus Agendamentos

O cliente poderá visualizar:

* serviço escolhido;
* data;
* horário;
* status;
* duração.

Status possíveis:

* Pendente
* Confirmado
* Cancelado
* Finalizado

Caso permitido pela regra da barbearia, o cliente poderá cancelar um agendamento antes do horário marcado.

---

# Loja de Produtos

O sistema também possuirá uma pequena loja virtual.

O administrador poderá cadastrar produtos.

Cada produto terá:

* Nome
* Foto
* Descrição
* Preço
* Estoque
* Tags

Exemplos de tags:

* Pomada
* Shampoo
* Barba
* Perfume

Os produtos serão exibidos em forma de catálogo.

---

# Carrinho

O cliente poderá adicionar produtos ao carrinho.

Cada item armazenará:

* Produto
* Quantidade
* Valor unitário
* Valor total

Será possível:

* alterar quantidade;
* remover itens;
* limpar o carrinho.

O valor total será atualizado automaticamente.

---

# Finalização da Compra

Ao clicar em "Encomendar", o sistema solicitará:

* endereço de entrega.

Neste momento será criado um Pedido.

Como o projeto é acadêmico, não haverá integração com sistemas de pagamento.

O pedido permanecerá aguardando confirmação do administrador.

---

# Meus Pedidos

O cliente poderá acompanhar seus pedidos.

Cada pedido exibirá:

* produtos;
* quantidade;
* valor total;
* endereço;
* data;
* status.

Status possíveis:

* Recebido
* Preparando
* Pronto
* Entregue
* Cancelado

---

# Dashboard Administrativo

Após o login como administrador, será exibido um painel contendo indicadores importantes.

Exemplos:

* total de clientes cadastrados;
* serviços cadastrados;
* produtos cadastrados;
* pedidos pendentes;
* agendamentos do dia;
* faturamento estimado.

Também poderá haver gráficos estatísticos.

---

# Gerenciamento de Clientes

O administrador poderá:

* visualizar clientes;
* pesquisar clientes;
* editar informações;
* desativar contas.

Também será possível visualizar:

* histórico de pedidos;
* histórico de agendamentos.

---

# Gerenciamento de Serviços

O administrador poderá:

* criar serviços;
* editar serviços;
* excluir serviços.

Ao alterar um serviço, os novos valores serão utilizados apenas em futuros agendamentos.

---

# Gerenciamento de Produtos

Será possível:

* cadastrar produtos;
* alterar estoque;
* editar preço;
* alterar foto;
* remover produtos.

O sistema poderá avisar quando o estoque estiver baixo.

---

# Gerenciamento da Agenda

O administrador visualizará toda a agenda da barbearia.

Será possível:

* confirmar agendamentos;
* cancelar agendamentos;
* remarcar horários;
* visualizar cliente responsável;
* visualizar serviço escolhido.

Também poderá configurar:

* dias de funcionamento;
* horário de abertura;
* horário de fechamento.

---

# Sistema de Logs

Todas as ações importantes realizadas pelos administradores serão registradas.

Exemplos:

* login;
* criação de produtos;
* edição de serviços;
* cancelamento de pedidos;
* alteração de horários;
* exclusão de registros.

Cada log armazenará:

* administrador responsável;
* ação realizada;
* descrição;
* data e horário.

---

# Integração com API de WhatsApp

Para melhorar a comunicação com os clientes, o sistema será integrado a uma API oficial de envio de mensagens via WhatsApp.

O objetivo dessa integração será automatizar lembretes e notificações sem que o administrador precise enviar mensagens manualmente.

## Funcionalidades

O administrador poderá enviar mensagens para um cliente específico diretamente pelo sistema.

Também poderá utilizar mensagens automáticas.

Exemplos:

### Lembrete de agendamento

Um dia antes do horário marcado, o sistema poderá enviar automaticamente:

"Olá, João! Passando para lembrar que você possui um horário agendado para amanhã às 14:00 na nossa barbearia. Estamos esperando por você."

---

### Confirmação de agendamento

Assim que o agendamento for realizado:

"Sua reserva foi realizada com sucesso."

---

### Cancelamento

Caso o administrador cancele um horário:

"Seu agendamento foi cancelado. Entre em contato conosco para reagendar."

---

### Pedido pronto

Quando os produtos estiverem disponíveis:

"Seu pedido já está pronto."

---

### Promoções

O administrador poderá selecionar diversos clientes e enviar campanhas promocionais.

Exemplos:

* descontos;
* novos produtos;
* novos serviços;
* horários disponíveis.

---

# Funcionamento da API

O sistema realizará uma requisição HTTP para a API de WhatsApp contendo:

* número do cliente;
* mensagem;
* identificação da aplicação;
* autenticação da API.

Fluxo:

Cliente realiza ação → Sistema identifica o evento → Gera a mensagem → Envia para a API → API entrega a mensagem ao WhatsApp do cliente → O sistema registra no banco se o envio foi concluído ou retornou erro.

---

# Histórico de Mensagens

Para cada envio será armazenado:

* cliente;
* número utilizado;
* conteúdo da mensagem;
* data e horário;
* status do envio;
* identificador retornado pela API.

Isso permitirá ao administrador consultar quais mensagens foram enviadas e verificar possíveis falhas.

---

# Segurança

O sistema utilizará:

* criptografia das senhas utilizando hash seguro;
* controle de permissões entre Cliente e Administrador;
* validação de formulários;
* proteção contra SQL Injection utilizando consultas preparadas;
* validação de sessões;
* controle de acesso às páginas administrativas;
* validação de upload de imagens.

---

# Objetivo Final

Ao final do desenvolvimento, o sistema permitirá que toda a operação da barbearia seja administrada por uma única aplicação.

Os clientes poderão criar contas, realizar agendamentos, acompanhar pedidos e encomendar produtos de forma prática.

Os administradores terão controle completo sobre clientes, serviços, produtos, agenda, pedidos, horários de funcionamento, estoque, relatórios, logs e comunicação via WhatsApp, tornando a gestão da barbearia mais organizada, eficiente e moderna.
