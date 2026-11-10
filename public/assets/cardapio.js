// Cada par de setas controla somente a fileira de produtos da sua seção.
for (const group of document.querySelectorAll('.menu-group')) {
  const carousel = group.querySelector('.product-carousel');
  const first = carousel.firstElementChild;
  const second = first?.nextElementSibling;
  if (!second) continue;

  const controls = group.querySelector('.carousel-controls');
  const previous = controls.querySelector('[data-scroll="-1"]');
  const next = controls.querySelector('[data-scroll="1"]');

  function updateControls() {
    const end = carousel.scrollWidth - carousel.clientWidth;
    controls.hidden = end <= 1;
    previous.disabled = carousel.scrollLeft <= 1;
    next.disabled = carousel.scrollLeft >= end - 1;
  }

  for (const button of [previous, next]) {
    button.addEventListener('click', () => {
      const step = second.offsetLeft - first.offsetLeft;
      carousel.scrollBy({ left: step * Number(button.dataset.scroll) });
    });
  }

  carousel.dataset.ready = '';
  carousel.addEventListener('scroll', updateControls, { passive: true });
  new ResizeObserver(updateControls).observe(carousel);
  updateControls();
}

const pedidoForm = document.querySelector('#pedidoForm');
if (pedidoForm) {
  const pedido = new Map();
  const lista = document.querySelector('#pedidoItens');
  const totalEl = document.querySelector('#pedidoTotal');
  const quantidadeEl = document.querySelector('#pedidoQuantidade');
  const enviar = document.querySelector('#pedidoEnviar');
  const mensagem = document.querySelector('#pedidoMensagem');
  const moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

  function renderPedido() {
    lista.replaceChildren();
    let quantidadeTotal = 0;
    let valorTotal = 0;

    for (const item of pedido.values()) {
      quantidadeTotal += item.quantidade;
      valorTotal += item.preco * item.quantidade;

      const linha = document.createElement('li');
      linha.className = 'list-group-item d-flex align-items-center justify-content-between gap-2';
      const descricao = document.createElement('span');
      descricao.className = 'me-auto';
      descricao.textContent = `${item.nome} — ${moeda.format(item.preco / 100)}`;

      const diminuir = document.createElement('button');
      diminuir.className = 'btn btn-sm btn-outline-dark';
      diminuir.type = 'button';
      diminuir.textContent = '−';
      diminuir.setAttribute('aria-label', `Remover uma unidade de ${item.nome}`);
      diminuir.addEventListener('click', () => {
        item.quantidade -= 1;
        if (item.quantidade < 1) pedido.delete(item.id);
        renderPedido();
      });

      const unidades = document.createElement('span');
      unidades.textContent = String(item.quantidade);

      const aumentar = document.createElement('button');
      aumentar.className = 'btn btn-sm btn-outline-dark';
      aumentar.type = 'button';
      aumentar.textContent = '+';
      aumentar.setAttribute('aria-label', `Adicionar uma unidade de ${item.nome}`);
      aumentar.addEventListener('click', () => {
        item.quantidade += 1;
        renderPedido();
      });

      linha.append(descricao, diminuir, unidades, aumentar);
      lista.append(linha);
    }

    totalEl.textContent = moeda.format(valorTotal / 100);
    quantidadeEl.textContent = String(quantidadeTotal);
    enviar.disabled = pedido.size === 0;
  }

  document.querySelectorAll('[data-add-product]').forEach((botao) => {
    botao.addEventListener('click', () => {
      const id = botao.dataset.productId;
      const existente = pedido.get(id);
      if (existente) existente.quantidade += 1;
      else pedido.set(id, {
        id,
        nome: botao.dataset.productName,
        preco: Number(botao.dataset.productPrice),
        quantidade: 1,
      });
      mensagem.textContent = `${botao.dataset.productName} adicionado.`;
      renderPedido();
    });
  });

  pedidoForm.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    if (pedido.size === 0) return;

    enviar.disabled = true;
    mensagem.textContent = 'Enviando pedido…';
    const dados = new FormData(pedidoForm);

    try {
      const resposta = await fetch('api/pedidos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          csrf: dados.get('csrf'),
          cardapio: dados.get('cardapio'),
          mesa_numero: dados.get('mesa_numero'),
          forma_pagamento: dados.get('forma_pagamento'),
          observacao: dados.get('observacao'),
          itens: [...pedido.values()].map(({ id, quantidade }) => ({ produto_id: id, quantidade })),
        }),
      });
      const resultado = await resposta.json();
      if (!resposta.ok) throw new Error(resultado.erro || 'Não foi possível enviar o pedido.');

      pedido.clear();
      renderPedido();
      mensagem.textContent = `Pedido ${resultado.pedido_id} enviado. A equipe confirma o pagamento na mesa.`;
      pedidoForm.reset();
    } catch (erro) {
      mensagem.textContent = erro.message;
      enviar.disabled = false;
    }
  });
}
