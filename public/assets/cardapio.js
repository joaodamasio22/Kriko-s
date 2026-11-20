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
    const chave = pedidoForm.dataset.cartKey;
    const lista = document.querySelector('#pedidoItens');
    const totalEl = document.querySelector('#pedidoTotal');
    const quantidadeEl = document.querySelector('#pedidoQuantidade');
    const enviar = document.querySelector('#pedidoEnviar');
    const mensagem = document.querySelector('#pedidoMensagem');
    const botoesAdicionar = document.querySelectorAll('[data-add-product]');
    const campos = ['mesa_numero', 'observacao'];
    const moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    let enviando = false;
    let armazenamentoDisponivel = true;

    function salvarPedido() {
        try {
            sessionStorage.setItem(
                chave,
                JSON.stringify({
                    itens: [...pedido.values()],
                    campos: Object.fromEntries(
                        campos.map((nome) => [nome, pedidoForm.elements.namedItem(nome).value]),
                    ),
                }),
            );
        } catch {
            armazenamentoDisponivel = false;
            mensagem.textContent =
                'O navegador bloqueou o armazenamento. Mantenha esta página aberta para preservar o pedido.';
        }
    }

    try {
        const salvo = JSON.parse(sessionStorage.getItem(chave) || 'null');
        for (const item of salvo?.itens || []) {
            if (
                Number.isInteger(Number(item.id)) &&
                Number(item.id) > 0 &&
                Number.isInteger(item.quantidade) &&
                item.quantidade > 0 &&
                item.quantidade <= 50 &&
                Number.isInteger(item.preco) &&
                item.preco >= 0 &&
                typeof item.nome === 'string'
            ) {
                pedido.set(String(item.id), { ...item, id: String(item.id) });
            }
        }
        for (const nome of campos) {
            if (typeof salvo?.campos?.[nome] === 'string')
                pedidoForm.elements.namedItem(nome).value = salvo.campos[nome];
        }
    } catch {
        mensagem.textContent =
            'Não foi possível restaurar o carrinho. Adicione os itens novamente.';
    }

    function quantidadeTotal() {
        return [...pedido.values()].reduce((total, item) => total + item.quantidade, 0);
    }

    function aumentarItem(item) {
        if (enviando) return;
        if (item.quantidade >= 50 || quantidadeTotal() >= 100) {
            mensagem.textContent = 'Limite de 50 unidades por produto e 100 por pedido.';
            return;
        }
        item.quantidade += 1;
        atualizarPedido();
    }

    function renderPedido() {
        lista.replaceChildren();
        let valorTotal = 0;
        const quantidade = quantidadeTotal();
        for (const item of pedido.values()) {
            valorTotal += item.preco * item.quantidade;
            const linha = document.createElement('li');
            linha.className =
                'list-group-item d-flex align-items-center justify-content-between gap-2';
            const descricao = document.createElement('span');
            descricao.className = 'me-auto';
            descricao.textContent = `${item.nome} — ${moeda.format(item.preco / 100)}`;
            const diminuir = document.createElement('button');
            diminuir.className = 'btn btn-sm btn-outline-dark';
            diminuir.type = 'button';
            diminuir.textContent = '−';
            diminuir.disabled = enviando;
            diminuir.setAttribute('aria-label', `Remover uma unidade de ${item.nome}`);
            diminuir.addEventListener('click', () => {
                if (enviando) return;
                item.quantidade -= 1;
                if (item.quantidade < 1) pedido.delete(item.id);
                atualizarPedido();
            });
            const unidades = document.createElement('span');
            unidades.textContent = String(item.quantidade);
            const aumentar = document.createElement('button');
            aumentar.className = 'btn btn-sm btn-outline-dark';
            aumentar.type = 'button';
            aumentar.textContent = '+';
            aumentar.disabled = enviando || item.quantidade >= 50 || quantidade >= 100;
            aumentar.setAttribute('aria-label', `Adicionar uma unidade de ${item.nome}`);
            aumentar.addEventListener('click', () => aumentarItem(item));
            linha.append(descricao, diminuir, unidades, aumentar);
            lista.append(linha);
        }
        totalEl.textContent = moeda.format(valorTotal / 100);
        quantidadeEl.textContent = String(quantidade);
        enviar.disabled = enviando || pedido.size === 0;
        enviar.textContent = enviando ? 'Enviando…' : 'Enviar pedido';
        pedidoForm.setAttribute('aria-busy', String(enviando));
        for (const nome of campos) pedidoForm.elements.namedItem(nome).disabled = enviando;
        botoesAdicionar.forEach((botao) => {
            botao.disabled = enviando;
        });
    }

    function atualizarPedido() {
        salvarPedido();
        renderPedido();
    }

    pedidoForm.addEventListener('input', salvarPedido);
    pedidoForm.addEventListener('change', salvarPedido);
    botoesAdicionar.forEach((botao) => {
        botao.addEventListener('click', () => {
            if (enviando) return;
            const id = botao.dataset.productId;
            const existente = pedido.get(id);
            if (
                quantidadeTotal() >= 100 ||
                (existente?.quantidade || 0) >= 50 ||
                (!existente && pedido.size >= 30)
            ) {
                mensagem.textContent =
                    'Limite de 30 produtos, 50 unidades por produto e 100 unidades por pedido.';
                return;
            }
            if (existente) existente.quantidade += 1;
            else
                pedido.set(id, {
                    id,
                    nome: botao.dataset.productName,
                    preco: Number(botao.dataset.productPrice),
                    quantidade: 1,
                });
            mensagem.textContent = `${botao.dataset.productName} adicionado.`;
            atualizarPedido();
        });
    });

    document.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', (evento) => {
            if (enviando) {
                evento.preventDefault();
                mensagem.textContent = 'Aguarde a confirmação do envio antes de trocar de tela.';
            } else if (armazenamentoDisponivel) salvarPedido();
        });
    });

    pedidoForm.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        if (enviando || pedido.size === 0 || !pedidoForm.reportValidity()) return;
        const dados = new FormData(pedidoForm);
        salvarPedido();
        enviando = true;
        renderPedido();
        mensagem.textContent = 'Enviando pedido…';
        try {
            const resposta = await fetch('api/pedidos.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({
                    csrf: dados.get('csrf'),
                    mesa_numero: dados.get('mesa_numero'),
                    observacao: dados.get('observacao'),
                    itens: [...pedido.values()].map(({ id, quantidade }) => ({
                        produto_id: id,
                        quantidade,
                    })),
                }),
            });
            if (resposta.status === 401) {
                window.location.replace('painel.php');
                return;
            }
            const resultado = await resposta.json().catch(() => {
                throw new Error(
                    'O servidor retornou uma resposta inválida. Seu carrinho foi mantido.',
                );
            });
            if (!resposta.ok)
                throw new Error(resultado.erro || 'Não foi possível enviar o pedido.');
            pedido.clear();
            pedidoForm.reset();
            try {
                sessionStorage.removeItem(chave);
            } catch {}
            mensagem.textContent = `Pedido #${resultado.pedido_id} registrado. Total: ${moeda.format(resultado.total_centavos / 100)}. A equipe confirma o pagamento na mesa.`;
        } catch (erro) {
            mensagem.textContent = erro.message || 'Falha na conexão. Seu carrinho foi mantido.';
        } finally {
            enviando = false;
            renderPedido();
        }
    });
    renderPedido();
}
