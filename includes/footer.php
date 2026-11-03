<!-- Rodapé compartilhado; o ano é atualizado pelo PHP. -->
<footer class="bg-dark text-light pt-5 pb-4 mt-5">
  <div class="container">
    <div class="row gy-4 align-items-center pb-4">
      <div class="col-md-6"><a href="index.php" class="brand text-warning text-decoration-none">KRIKO’S</a><p class="small mb-0 mt-2 text-light text-opacity-75">Tem fome que só um bom pastel resolve.</p></div>
      <div class="col-md-6"><nav class="d-flex gap-4 justify-content-md-end" aria-label="Navegação do rodapé"><a class="link-light link-offset-3" href="pastel.php">Pastéis</a><a class="link-light link-offset-3" href="churros.php">Churros</a><a class="link-light link-offset-3" href="index.php#cardapio">Cardápio</a></nav></div>
    </div>
    <div class="border-top border-light border-opacity-25 pt-3 d-flex flex-wrap justify-content-between gap-2 small text-light text-opacity-75"><span>© <?= date('Y') ?> Kriko’s. Pastéis & churros.</span><span>Do salgado ao doce, escolha o seu.</span></div>
  </div>
</footer>
<!-- O bundle oficial controla o menu móvel; não há framework adicional. -->
<script src="assets/vendor/bootstrap.bundle.min.js"></script>
</body>
</html>
