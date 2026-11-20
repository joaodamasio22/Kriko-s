const { test } = require('node:test');
const assert = require('node:assert/strict');
const { readFileSync } = require('node:fs');
const vm = require('node:vm');

const source = readFileSync(
    require('node:path').join(__dirname, '../public/assets/cardapio.js'),
    'utf8',
);

class Element {
    constructor() {
        this.listeners = {};
        this.children = [];
        this.value = '';
        this.dataset = {};
    }
    addEventListener(type, fn) {
        (this.listeners[type] ||= []).push(fn);
    }
    async fire(type) {
        for (const fn of this.listeners[type] || []) await fn({ preventDefault() {} });
    }
    setAttribute() {}
    append(...children) {
        this.children.push(...children);
    }
    replaceChildren() {
        this.children = [];
    }
}

function page(storage, product, fetch, location = {}) {
    const nodes = Object.fromEntries(
        [
            'pedidoForm',
            'pedidoItens',
            'pedidoTotal',
            'pedidoQuantidade',
            'pedidoEnviar',
            'pedidoMensagem',
        ].map((id) => [id, new Element()]),
    );
    const fields = Object.fromEntries(
        ['csrf', 'mesa_numero', 'observacao'].map((name) => [name, new Element()]),
    );
    fields.csrf.value = 'token';
    const form = nodes.pedidoForm;
    form.dataset.cartKey = 'cart-test';
    form.elements = { namedItem: (name) => fields[name] };
    form.reportValidity = () => true;
    form.reset = () => {
        fields.mesa_numero.value = '';
        fields.observacao.value = '';
    };
    const add = new Element();
    add.dataset = {
        productId: String(product.id),
        productName: product.nome,
        productPrice: String(product.preco),
    };
    vm.runInNewContext(source, {
        document: {
            querySelector: (selector) => nodes[selector.slice(1)],
            querySelectorAll: (selector) => (selector === '[data-add-product]' ? [add] : []),
            createElement: () => new Element(),
        },
        sessionStorage: {
            getItem: (key) => storage.get(key) ?? null,
            setItem: (key, value) => storage.set(key, value),
            removeItem: (key) => storage.delete(key),
        },
        FormData: class {
            constructor() {
                this.values = Object.fromEntries(
                    Object.entries(fields)
                        .filter(([, field]) => !field.disabled)
                        .map(([name, field]) => [name, field.value]),
                );
            }
            get(name) {
                return this.values[name];
            }
        },
        fetch,
        Intl,
        window: { location },
    });
    return { nodes, fields, form, add };
}

const pastel = { id: 1, nome: 'Pastel', preco: 1400 };
const churros = { id: 2, nome: 'Churros', preco: 2000 };

test('preserva itens, mesa e observação e envia pedido sem escolher pagamento', async () => {
    const storage = new Map();
    const first = page(storage, pastel);
    await first.add.fire('click');
    first.fields.mesa_numero.value = '2';
    first.fields.observacao.value = 'Sem açúcar';
    await first.form.fire('input');
    let payload;
    const second = page(storage, churros, async (_url, options) => {
        payload = JSON.parse(options.body);
        return { ok: true, json: async () => ({ pedido_id: 10, total_centavos: 3400 }) };
    });
    assert.equal(second.nodes.pedidoQuantidade.textContent, '1');
    assert.equal(second.fields.mesa_numero.value, '2');
    await second.add.fire('click');
    assert.equal(second.nodes.pedidoQuantidade.textContent, '2');
    await second.form.fire('submit');
    assert.deepEqual(payload.itens, [
        { produto_id: '1', quantidade: 1 },
        { produto_id: '2', quantidade: 1 },
    ]);
    assert.equal(payload.mesa_numero, '2');
    assert.equal(Object.hasOwn(payload, 'forma_pagamento'), false);
    assert.equal(payload.observacao, 'Sem açúcar');
    assert.equal(storage.size, 0);
    assert.equal(second.nodes.pedidoQuantidade.textContent, '0');
    assert.match(second.nodes.pedidoMensagem.textContent, /#10 registrado/);
});

test('mantém o carrinho após erro da API e permite reenviar', async () => {
    const storage = new Map();
    const current = page(storage, pastel, async () => ({
        ok: false,
        json: async () => ({ erro: 'Mesa inválida' }),
    }));
    await current.add.fire('click');
    await current.form.fire('submit');
    assert.equal(current.nodes.pedidoQuantidade.textContent, '1');
    assert.equal(current.nodes.pedidoEnviar.disabled, false);
    assert.equal(current.nodes.pedidoMensagem.textContent, 'Mesa inválida');
    assert.equal(JSON.parse(storage.get('cart-test')).itens.length, 1);
});

test('volta para o login ao enviar um pedido com sessão expirada', async () => {
    const storage = new Map();
    const destinos = [];
    const current = page(storage, pastel, async () => ({ ok: false, status: 401 }), {
        replace: (destino) => destinos.push(destino),
    });
    await current.add.fire('click');
    await current.form.fire('submit');

    assert.deepEqual(destinos, ['painel.php']);
    assert.equal(JSON.parse(storage.get('cart-test')).itens.length, 1);
});

test('bloqueia envios simultâneos e alterações enquanto aguarda a resposta', async () => {
    let finish;
    let calls = 0;
    const current = page(new Map(), pastel, () => {
        calls++;
        return new Promise((resolve) => {
            finish = resolve;
        });
    });
    await current.add.fire('click');
    const pending = current.form.fire('submit');
    await current.form.fire('submit');
    await current.add.fire('click');
    assert.equal(calls, 1);
    assert.equal(current.nodes.pedidoQuantidade.textContent, '1');
    assert.equal(current.add.disabled, true);
    finish({ ok: true, json: async () => ({ pedido_id: 11, total_centavos: 1400 }) });
    await pending;
    assert.equal(current.add.disabled, false);
});

test('reduz quantidade e remove item preservando a mudança entre páginas', async () => {
    const storage = new Map();
    const current = page(storage, pastel);
    await current.add.fire('click');
    await current.add.fire('click');
    await current.nodes.pedidoItens.children[0].children[1].fire('click');
    assert.equal(current.nodes.pedidoQuantidade.textContent, '1');
    await current.nodes.pedidoItens.children[0].children[1].fire('click');
    const next = page(storage, churros);
    assert.equal(next.nodes.pedidoQuantidade.textContent, '0');
    assert.equal(next.nodes.pedidoEnviar.disabled, true);
});
